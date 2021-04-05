<?php namespace Waka\Pdfer\Classes;

use Waka\Pdfer\Models\WakaPdf;
use Waka\Utils\Classes\DataSource;
use Waka\Utils\Classes\TmpFiles;

class PdfCreator extends \October\Rain\Extension\Extendable
{
    public static $wakapdf;
    public $ds;
    public $modelId;

    private $isTwigStarted;
    private $levierData;

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

    public function setModelId($modelId)
    {
        $this->modelId = $modelId;
        $dataSourceId = $this->getProductor()->data_source;
        $this->ds = new DataSource($dataSourceId);
        $this->ds->instanciateModel($modelId);
        return $this;
    }

    public function setModelTest()
    {
        $this->modelId = $this->getProductor()->test_id;
        $dataSourceId = $this->getProductor()->data_source;
        $this->ds = new DataSource($dataSourceId);
        $this->ds->instanciateModel($this->modelId);
        return $this;
    }

    public function checkScopes()
    {
        //trace_log('checkScopes');
        if (!$this->ds || !$this->modelId) {
            //trace_log("modelId pas instancie");
            throw new \SystemException("Le modelId n a pas ete instancié");
        }
        //trace_log('nom modèle : '.$this->ds->model);
        $scope = new \Waka\Utils\Classes\Scopes($this->getProductor(), $this->ds->model);
        //trace_log('scope calcule');
        if ($scope->checkScopes()) {
            return true;
        } else {
            return false;
        }
    }

    public function renderPdf($inline = false)
    {
        if (!$this->ds || !$this->modelId) {
            //trace_log("modelId pas instancie");
            throw new \SystemException("Le modelId n a pas ete instancié");
        }
        $data = $this->prepareCreatorVars();
        $pdf = $this->createPdf($data);
        if ($inline) {
            return $pdf->inline($data['fileName']);
        } else {
            return $pdf->download($data['fileName']);
        }
    }

    public function renderTemp($inline = false)
    {
        if (!$this->ds || !$this->modelId) {
            //trace_log("modelId pas instancie");
            throw new \SystemException("Le modelId n a pas ete instancié");
        }
        $data = $this->prepareCreatorVars();
        $pdf = $this->createPdf($data);
        $pdfContent = $pdf->output();
        return TmpFiles::createDirectory()->putFile($data['fileName'], $pdfContent);
    }

    public function renderCloud($lot = false)
    {
        if (!$this->ds || !$this->modelId) {
            //trace_log("modelId pas instancie");
            throw new \SystemException("Le modelId n a pas ete instancié");
        }
        $data = $this->prepareCreatorVars();
        $pdf = $this->createPdf($data);
        $pdfContent = $pdf->output();
        $cloudSystem = \App::make('cloudSystem');
        $lastFolderDir = null;
        if ($lot) {
            $lastFolderDir = $cloudSystem->createDirFromArray(['lots']);
        } else {
            $folderOrg = new \Waka\Cloud\Classes\FolderOrganisation();
            $folders = $folderOrg->getFolder($this->ds->model);
            $lastFolderDir = $cloudSystem->createDirFromArray($folders);
        }
        \Storage::cloud()->put($lastFolderDir['path'] . '/' . $data['fileName'], $pdfContent);
    }

    public function prepareCreatorVars()
    {
        $this->ds = new DataSource($this->getProductor()->data_source);
        //$varName = strtolower($this->ds->code);

        $doted = $this->ds->getValues($this->modelId);
        $img = $this->ds->wimages->getPicturesUrl($this->getProductor()->images);
        //trace_log($img);
        $fnc = $this->ds->getFunctionsCollections($this->modelId, $this->getProductor()->model_functions);
        $css = null;

        $model = [
            'ds' => $doted,
            'IMG' => $img,
            'FNC' => $fnc,
            'css' => $css,
        ];
        $this->levierData = $model;

        $htmlLayout = $this->renderHtml($model);
        $slugName = $this->createTwigStrName($doted);
        //
        $header = null;

        return [
            "fileName" => $slugName . '.pdf',
            "html" => $htmlLayout,
            "options" => $this->getProductor()->layout->options,
            "header" => $this->getFooter($doted),
            "footer" => $this->getFooter($doted),
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

        if($data['header']) {
            //trace_log("---------------HEADER-------------------");
            $pdf->setOption('header-html',$data['header']); 
        }
        if($data['footer']) {
            //trace_log("---------------FOOTER-------------------");
            $pdf->setOption('footer-html', $data['footer']); 
        }
        
        return $pdf;
    }

    public function getHeader($model) {
        if(!$this->getProductor()->layout->use_header) {
            return null;
        }
        $this->startTwig();
        $data = [
            'ds' => $model,
            'baseCss' => \File::get(plugins_path() . $this->getProductor()->layout->baseCss),
            'AddCss' => $this->getProductor()->layout->Addcss,
        ];
        $header = \Twig::parse($this->getProductor()->layout->header_html, $data);
        $this->stopTwig();
        return $header;      
    }
    public function getFooter($model) {
        if(!$this->getProductor()->layout->use_footer) {
            return null;
        }
        $this->startTwig();
        $data = [
            'ds' => $model,
            'baseCss' => \File::get(plugins_path() . $this->getProductor()->layout->baseCss),
            'AddCss' => $this->getProductor()->layout->Addcss,
        ];
        $footer = \Twig::parse($this->getProductor()->layout->footer_html, $data);
        $this->stopTwig();
        return $footer;      
    }

    public function renderHtml($model)
    {
        $this->startTwig();
        $html = $this->getProductor()->template;
        $htmlContent = \Twig::parse($html, $model);
        $data = [
            'ds' => $model['ds'],
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
        $modelName = strtolower($this->ds->code);
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
