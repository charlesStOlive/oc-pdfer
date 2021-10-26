<?php namespace Waka\Pdfer\Classes;

use Waka\Pdfer\Models\WakaPdf;
use Waka\Utils\Classes\DataSource;
use Waka\Utils\Classes\TmpFiles;
use PDF;

class PdfCreator extends \Winter\Storm\Extension\Extendable
{
    public static $wakapdf;
    public $ds;
    public $modelId;

    public $askResponse = [];

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
        $this->ds =  \DataSources::find($dataSourceId);
        $this->ds->instanciateModel($modelId);
        return $this;
    }

    public function setModelTest()
    {
        $this->modelId = $this->getProductor()->test_id;
        if(!$this->modelId) {
             throw new \ValidationException(['test_id' => \Lang::get('waka.pdfer::wakapdf.e.test_id')]);
        }
        $dataSourceId = $this->getProductor()->data_source;
        $this->ds =  \DataSources::find($dataSourceId);
        $this->ds->instanciateModel($this->modelId);
        return $this;
    }

    public function setRuleAsksResponse($datas = [])
    {
        $askArray = [];
        $srcmodel = $this->ds->getModel($this->modelId);
        $asks = $this->getProductor()->rule_asks()->get();
        foreach($asks as $ask) {
            $key = $ask->getCode();
            //trace_log($key);
            $askResolved = $ask->resolve($srcmodel, 'twig', $datas);
            $askArray[$key] = $askResolved;
        }
        //trace_log($askArray); // les $this->askResponse sont prioritaire
        return array_replace($askArray,$this->askResponse);
        
    }

    //BEBAVIOR AJOUTE LES REPOSES ??
    public function setAsksResponse($datas = [])
    {
        $this->askResponse = $this->ds->getAsksFromData($datas, $this->getProductor()->asks);
        return $this;
    }

    public function setRuleFncsResponse()
    {
        $fncArray = [];
        $srcmodel = $this->ds->getModel($this->modelId);
        $fncs = $this->getProductor()->rule_fncs()->get();
        foreach($fncs as $fnc) {
            $key = $fnc->getCode();
            //trace_log('key of the function');
            $fncResolved = $fnc->resolve($srcmodel,$this->ds->code);
            $fncArray[$key] = $fncResolved;
        }
        //trace_log($fncArray);
        return $fncArray;
        
    }

    public function setdefaultAsks($datas = [])
    {
        if($this->ds) {
             $this->askResponse = $this->ds->getAsksFromData($datas, $this->getProductor()->asks);
        } else {
            $this->askResponse = [];
        }
        return $this;
    }

    

    public function checkConditions()//Ancienement checkScopes
    {
        $conditions = new \Waka\Utils\Classes\Conditions($this->getProductor(), $this->ds->model);
        return $conditions->checkConditions();
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
        $path = [];
        if ($lot) {
            $path = 'lots';
        } else {
            $folderOrg = new \Waka\Cloud\Classes\FolderOrganisation();
            $path = $folderOrg->getPath($this->ds->model);
        }
        $cloudSystem->put($path.'/'.$data['fileName'], $pdfContent);
    }

    public function prepareCreatorVars()
    {
        $this->ds =  \DataSources::find($this->getProductor()->data_source);
        //$varName = strtolower($this->ds->code);

        $doted = $this->ds->getValues($this->modelId);
        //$img = $this->ds->wimages->getPicturesUrl($this->getProductor()->images);
        //trace_log($img);
        //$fnc = $this->ds->getFunctionsCollections($this->modelId, $this->getProductor()->model_functions);
        $css = null;

        $model = [
            'ds' => $doted,
            // 'IMG' => $img,
            // 'FNC' => $fnc,
            'css' => $css,
        ];
        
        //Nouveau bloc pour nouveaux asks
        if($this->getProductor()->rule_asks()->count()) {
            $this->askResponse = $this->setRuleAsksResponse($model);
        } else {
            //Injection des asks s'ils existent dans le model;
            if(!$this->askResponse) {
                $this->setAsksResponse($model);
            }
        }

        //Nouveau bloc pour les new Fncs
        if($this->getProductor()->rule_fncs()->count()) {
            $fncs = $this->setRuleFncsResponse($model);
            $model = array_merge($model, [ 'fncs' => $fncs]);
        }
        //trace_log("ASK RESPONSE");
        //trace_log($this->askResponse);
        $model = array_merge($model, [ 'asks' => $this->askResponse]);

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
        $pdf = PDF::loadHtml($data['html']);
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
        // $modelName = strtolower($this->ds->code);
        // $vars = [
        //     $modelName => $data,
        // ];
        //trace_log($this->getProductor()->pdf_name);
        $nameConstruction = \Twig::parse($this->getProductor()->pdf_name, ['ds' => $data]);
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
