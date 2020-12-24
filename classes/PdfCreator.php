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
        $pdf = $this->createPdf($data);
        if ($inline) {
            return $pdf->inline($data['fileName']);
        } else {
            return $pdf->download($data['fileName']);

        }

    }

    public function renderCloud($dataSourceId)
    {
        $data = $this->prepareCreatorVars($dataSourceId);
        $pdf = $this->createPdf($data);
        $pdfContent = $pdf->output();

        $folderOrg = new \Waka\Cloud\Classes\FolderOrganisation();
        $folders = $folderOrg->getFolder($this->dataSource->model);

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

        $model = [
            $varName => $doted,
            'IMG' => $img,
            'FNC' => $fnc,
            'css' => $css,
        ];

        $htmlLayout = $this->renderHtml($model, $varName);

        $slugName = $this->createTwigStrName($doted);

        trace_log($slugName);

        return [
            "fileName" => $slugName . '.pdf',
            "html" => $htmlLayout,
            "options" => $this->wakapdf->layout->options,
        ];
    }

    public function createPdf($data)
    {
        $pdf = \PDF::loadHtml($data['html']);
        $options = $data['options'] ?? null;
        if ($options) {
            foreach ($options as $key => $value) {
                $pdf->setOption($key, $value);
            }
        }
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
        //trace_log($htmlLayout);
        $this->stopTwig();
        return $htmlLayout;

    }

    public function createTwigStrName($data)
    {
        $modelName = strtolower($this->dataSource->name);
        $vars = [
            $modelName => $data,
        ];
        trace_log($this->wakapdf->pdf_name);
        $nameConstruction = \Twig::parse($this->wakapdf->pdf_name, $vars);
        return str_slug($nameConstruction);
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
