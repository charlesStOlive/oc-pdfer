<?php namespace Waka\Pdfer\Classes;

use Waka\Pdfer\Models\WakaPdf;
use Waka\Utils\Classes\DataSource;
use Waka\Utils\Classes\TmpFiles;
use PDF;
use Waka\Utils\Classes\ProductorCreator;
class PdfCreator extends ProductorCreator
{

    public static function find($pdf_id)
    {
        $productor = WakaPdf::find($pdf_id);
        self::$productor = $productor;
        return new self;
    }

    public function renderPdf($inline = false)
    {
        if (!self::$ds || !$this->modelId) {
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
        if (!self::$ds || !$this->modelId) {
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
        if (!self::$ds || !$this->modelId) {
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
            $path = $folderOrg->getPath(self::$ds->model);
        }
        $cloudSystem->put($path.'/'.$data['fileName'], $pdfContent);
    }

    public function prepareCreatorVars()
    {
        $model = $this->getProductorVars();
        $htmlLayout = $this->renderHtml($model);
        $slugName = $this->createTwigStrName();
        //
        $header = null;

        return [
            "fileName" => $slugName . '.pdf',
            "html" => $htmlLayout,
            "options" => $this->getProductor()->layout->options,
            "header" => $this->getFooter($model),
            "footer" => $this->getFooter($model),
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
            'baseCss' => \File::get(plugins_path() . $this->getProductor()->layout->baseCss),
            'AddCss' => $this->getProductor()->layout->Addcss,
        ];
        $data = array_merge($data, $model);
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
            'baseCss' => \File::get(plugins_path() . $this->getProductor()->layout->baseCss),
            'AddCss' => $this->getProductor()->layout->Addcss,
        ];
        $data = array_merge($data, $model);
        trace_log( $data);
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
}
