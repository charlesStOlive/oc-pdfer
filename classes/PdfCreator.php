<?php namespace Waka\Pdfer\Classes;

use Waka\Pdfer\Models\WakaPdf;
use Waka\Utils\Classes\DataSource;
use Waka\Utils\Classes\TmpFiles;
use PDF;
use Waka\Utils\Classes\ProductorCreator;
use Spatie\Browsershot\Browsershot;
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

        //trace_log($data);
        
        if ($inline =="inline_download") {
            return response()->stream(function () use ($data, $pdf) {
                echo $pdf->pdf();
            }, 200, ['Content-Type' => 'application/pdf']);
        } else if($inline =="inline_show") {
            return $pdf->bodyHtml();
        } else  {
            $pdf->save(temp_path($data['fileName']));
            return response()->download(temp_path($data['fileName']))->deleteFileAfterSend();
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
            "header" => $this->getHeader($model),
            "footer" => $this->getFooter($model),
        ];
    }

    public function createPdf($data)
    {
        $options = $data['options'] ?? [];
        $pdf = Browsershot::html($data['html'])
            ->showBackground()
            ->emulateMedia('screen')
            ->margins($options['margin-top'] ?? 10, $options['margin-right'] ?? 10, $options['margin-bottom'] ?? 10, $options['margin-left'] ?? 10)
            ->format('A4');

        $header = $data['header'] ?? null;
        $footer = $data['footer'] ?? null;
        //trace_log($header);
        //trace_log($footer);

        if(!empty($header) or !empty($footer)) {
            $pdf->showBrowserHeaderAndFooter();
            if($header) {
                $pdf->headerHtml($header);
            } else {
                $pdf->headerHtml('<span></span>');
            }
            if($footer) {
                $pdf->footerHtml($footer);
            } else {
                $pdf->footerHtml('<span></span>');
            }
        }
        $orientation = $options['orientation'] ?? null;
        //trace_log($orientation);
        if($orientation == 'Landscape') {
            $pdf->landscape();
        }
        return $pdf;
    }

    public function getHeader($model) {
        if(!$this->getProductor()->layout->use_header) {
            return null;
        }
        
        
        // $data = [
        //     'baseCss' => \File::get(plugins_path() . $this->getProductor()->layout->baseCss),
        //     'AddCss' => $this->getProductor()->layout->Addcss,
        // ];
        $data = array_merge([], $model);
        $header = \Twig::parse($this->getProductor()->layout->header_html, $data);
        
        return $header;      
    }
    public function getFooter($model) {
        if(!$this->getProductor()->layout->use_footer) {
            return null;
        }
        
        // $data = [
        //     'baseCss' => \File::get(plugins_path() . $this->getProductor()->layout->baseCss),
        //     'AddCss' => $this->getProductor()->layout->Addcss,
        // ];
        $data = array_merge([], $model);
        //trace_log( $data);
        $footer = \Twig::parse($this->getProductor()->layout->footer_html, $data);
        
        return $footer;      
    }

    public function renderHtml($model)
    {
        
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
        
        return $htmlLayout;
    }
}
