<?php namespace Waka\Pdfer\Classes;

use App;
use Waka\Pdfer\Models\WakaPdf;
use Waka\Utils\Classes\DataSource;

class PdfCreator
{

    private $modelSource;
    private $dataSourceId;
    private $additionalParams;
    private $dataSourceAdditionalParams;
    private $dataSource;

    private $isTwigStarted;

    public function __construct($pdf_id)
    {
        $wakapdf = WakaPdf::find($pdf_id);
        $this->wakapdf = $wakapdf;
        $this->dataSource = new DataSource($this->wakapdf->data_source);

    }

    public function renderPdf($dataSourceId, $inline = false)
    {
        $data = $this->prepareCreatorVars($dataSourceId);
        $pdf = $this->createPdf($data['html']);
        if ($inline) {
            return $pdf->inline($data['fileName']);
        } else {
            return $pdf->download($data['fileName']);

        }

    }

    public function renderCloud($dataSourceId)
    {
        $data = $this->prepareCreatorVars($dataSourceId);
        $pdf = $this->createPdf($data['html']);
        $pdfContent = $pdf->output();

        $folderOrg = new \Waka\Cloud\Classes\FolderOrganisation();
        $folders = $folderOrg->getFolder($this->modelSource);

        $cloudSystem = App::make('cloudSystem');
        $lastFolderDir = $cloudSystem->createDirFromArray($folders);

        //\Storage::cloud()->put('test.txt', 'Hello World');

        \Storage::cloud()->put($lastFolderDir['path'] . '/' . $data['fileName'], $pdfContent);

    }

    public function prepareCreatorVars($modelId)
    {

        $varName = strtolower($this->dataSource->name);

        $doted = $this->dataSource->getValues($modelId);
        $img = $this->dataSource->wimages->getPicturesUrl($this->wakapdf->images);
        //trace_log($img);
        $fnc = $this->dataSource->getFunctionsCollections($modelId, $this->wakapdf->model_functions);
        $css = null;
        if ($this->wakapdf->pdf_layout) {
            //trace_log($this->wakapdf->pdf_layout);
            $css = \File::get(plugins_path() . $this->wakapdf->pdf_layout->wconfig_layout);
        }
        $model = [
            $varName => $doted,
            'IMG' => $img,
            'FNC' => $fnc,
            'css' => $css,
        ];

        $htmlLayout = $this->renderHtml($model, $varName);

        $slugName = $doted['name'] ?? null;
        $slugName = str_slug($slugName);
        $pdfSlug = str_slug($this->wakapdf->name);

        return [
            "fileName" => $fileName = $pdfSlug . '_' . $slugName . '.pdf',
            "html" => $htmlLayout,
        ];
    }

    public function createPdf($html)
    {
        $pdf = \PDF::loadHtml($html);
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('viewport-size', '1280x1024');
        //$pdf->setOption('zoom', '1.5');
        // $pdf->setOption('enable-javascript', true);
        // $pdf->setOption('javascript-delay', 5000);
        //$pdf->setOption('enable-smart-shrinking', true);
        // $pdf->setOption('no-stop-slow-scripts', true);
        return $pdf;
    }

    public function renderHtml($model, $varName)
    {
        $this->startTwig();
        $html = $this->wakapdf->template;
        $htmlContent = \Twig::parse($html, $model);
        $data = [
            $varName => $model,
            'content' => $htmlContent,
            'baseCss' => \File::get(plugins_path() . $this->wakapdf->layout->baseCss),
            'AddCss' => $this->wakapdf->layout->Addcss,
        ];
        $htmlLayout = \Twig::parse($this->wakapdf->layout->contenu, $data);
        $this->stopTwig();
        return $htmlLayout;

    }

    /**
     * Temporarily registers mail based token parsers with Twig.
     * @return void
     */
    protected function startTwig()
    {
        if ($this->isTwigStarted) {
            return;
        }

        $this->isTwigStarted = true;

        $markupManager = \System\Classes\MarkupManager::instance();
        $markupManager->beginTransaction();
        $markupManager->registerTokenParsers([
            new \System\Twig\MailPartialTokenParser,
        ]);
    }

    /**
     * Indicates that we are finished with Twig.
     * @return void
     */
    protected function stopTwig()
    {
        if (!$this->isTwigStarted) {
            return;
        }

        $markupManager = \System\Classes\MarkupManager::instance();
        $markupManager->endTransaction();

        $this->isTwigStarted = false;
    }

}
