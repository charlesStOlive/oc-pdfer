<?php namespace Waka\Pdfer\Classes;

use Waka\Pdfer\Models\WakaPdf;
use Waka\Utils\Classes\DataSource;
use Waka\Utils\Classes\TmpFiles;
use PDF;
use Waka\Utils\Classes\ProductorCreator;
use Spatie\Browsershot\Browsershot;
class PdfCreator extends ProductorCreator
{

    private $layout;
    private $pdfData;
    private $header;
    private $footer;
    private $fileName;

    public static function find($pdf_id)
    {
        $productor = WakaPdf::find($pdf_id);
        self::$productor = $productor;
        return new self;
    }

    

    public function renderPdf($inline = false)
    {
        $this->prepareCreatorVars();
        $pdf = $this->createPdf();
        
        if ($inline =="inline_download") {
            return response()->stream(function () use ($pdf) {
                echo $pdf->pdf();
            }, 200, ['Content-Type' => 'application/pdf']);
        } else if($inline =="inline_show") {
            return $pdf->bodyHtml();
        } else  {
            $pdf->save(temp_path($this->fileName));
            return response()->download(temp_path($this->fileName))->deleteFileAfterSend();
        }
    }

    public function renderTemp($inline = false)
    {
        $this->prepareCreatorVars();
        $pdf = $this->createPdf();
        $pdfContent = $pdf->output();
        return TmpFiles::createDirectory()->putFile($this->fileName, $pdfContent);
    }

    public function renderCloud($lot = false)
    {
        $this->prepareCreatorVars();
        $pdf = $this->createPdf();
        $pdfContent = $pdf->output();
        $cloudSystem = \App::make('cloudSystem');
        $path = [];
        if ($lot) {
            $path = 'lots';
        } else {
            $folderOrg = new \Waka\Cloud\Classes\FolderOrganisation();
            $path = $folderOrg->getPath($this->getDs()->model);
        }
        $cloudSystem->put($path.'/'.$this->fileName, $pdfContent);
    }


    public function prepareCreatorVars()
    {
        $this->pdfData =  $this->getProductorVars();
        $this->layout = $this->getProductor()->layout;
        $this->fileName = $this->createTwigStrName(). '.pdf';
        $this->header = $this->getHeader();
        $this->footer = $this->getFooter();
    }

    public function createPdf()
    {

        $options = $this->layout->options;

        $url = url($this->layout->layout_path);
        if($this->layout->layout_require_code) {
            $url .= '/'.$this->userKey->name;
        }
        trace_log($url);
        $pdf = Browsershot::url($url)
        ->showBackground()
        ->emulateMedia('screen')
        ->margins($options['margin-top'] ?? 10, $options['margin-right'] ?? 10, $options['margin-bottom'] ?? 10, $options['margin-left'] ?? 10)
        ->format('A4');
        
        $header = $this->header ?? null;
        $footer = $this->footer ?? null;

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

    public function getHeader() {
        if(!$this->layout->use_header) {
            return null;
        }
        $data = array_merge([], $this->pdfData);
        $header = \Twig::parse($this->layout->header_html, $data);
        
        return $header;      
    }
    public function getFooter() {
        if(!$this->layout->use_footer) {
            return null;
        }
        $data = array_merge([], $this->pdfData);
        $footer = \Twig::parse($this->layout->footer_html, $data);
        
        return $footer;      
    }
}
