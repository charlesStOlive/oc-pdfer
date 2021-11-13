<?php namespace Waka\Mailer\Updates;

use Seeder;
use Waka\Mailer\Models\Layout;

class SeedRegexWakaPdf extends Seeder
{
    public function run()
    {
        $pdfs = \Waka\Pdfer\Models\WakaPdf::get();
        foreach($pdfs as $pdf) {
            $html = $pdf->template;
            $html = $this->transformFnc($html);
            $html = $this->addDatas($html);
            $pdf->template = $html;
            $pdf->save();
        }
        
    }

    public function transformFnc($content) {
        return preg_replace('/(FNC.)/m', 'fncs.', $content );
    }

    public function addDatas($content) {
         return preg_replace('/(fncs.\w+)/m', '${1}.datas', $content );
    }
}
