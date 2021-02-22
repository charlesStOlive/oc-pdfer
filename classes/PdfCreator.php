<?php namespace Waka\Pdfer\Classes;

use Waka\Pdfer\Models\WakaPdf;
use Waka\Utils\Classes\DataSource;

class PdfCreator extends \October\Rain\Extension\Extendable
{
    public static $wakapdf;
    public $ds;
    public $modelId;

    private $isTwigStarted;

    public static function find($pdf_id)
    {
        $wakapdf = WakaPdf::find($pdf_id);
        self::$wakapdf = $wakapdf;
        return new self;
    }

    public static function getProductor()
    {
        return self::$wakapdf;
    }

    public function renderPdf($modelId, $inline = false)
    {
        $data = $this->prepareCreatorVars($modelId);
        $pdf = $this->createPdf($data);
        if ($inline) {
            return $pdf->inline($data['fileName']);
        } else {
            return $pdf->download($data['fileName']);
        }

    }

    public function renderTemp($modelId, $inline = false)
    {
        $data = $this->prepareCreatorVars($modelId);
        $pdf = $this->createPdf($data);
        $pdfContent = $pdf->output();
        \Storage::put('temp/' . $data['fileName'], $pdfContent);
        return 'temp/' . $data['fileName'];
    }

    public function prepareCreatorVars($modelId)
    {
        $this->ds = new DataSource($this->getProductor()->data_source);
        $varName = strtolower($this->ds->name);

        $doted = $this->ds->getValues($modelId);
        $img = $this->ds->wimages->getPicturesUrl($this->getProductor()->images);
        //trace_log($img);
        $fnc = $this->ds->getFunctionsCollections($modelId, $this->getProductor()->model_functions);
        $css = null;

        $model = [
            $varName => $doted,
            'IMG' => $img,
            'FNC' => $fnc,
            'css' => $css,
        ];

        $htmlLayout = $this->renderHtml($model, $varName);
        $slugName = $this->createTwigStrName($doted);

        //trace_log($slugName);

        return [
            "fileName" => $slugName . '.pdf',
            "html" => $htmlLayout,
            "options" => $this->getProductor()->layout->options,
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
        return $pdf;
    }

    public function renderHtml($model, $varName)
    {
        $this->startTwig();
        $html = $this->getProductor()->template;
        $htmlContent = \Twig::parse($html, $model);
        $data = [
            $varName => $model,
            'content' => $htmlContent,
            'baseCss' => \File::get(plugins_path() . $this->getProductor()->layout->baseCss),
            'AddCss' => $this->getProductor()->layout->Addcss,
        ];
        $htmlLayout = \Twig::parse($this->getProductor()->layout->contenu, $data);
        //trace_log($htmlLayout);
        $this->stopTwig();
        return $htmlLayout;

    }

    public function createTwigStrName($data)
    {
        $modelName = strtolower($this->ds->name);
        $vars = [
            $modelName => $data,
        ];
        //trace_log($this->getProductor()->pdf_name);
        $nameConstruction = \Twig::parse($this->getProductor()->pdf_name, $vars);
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
