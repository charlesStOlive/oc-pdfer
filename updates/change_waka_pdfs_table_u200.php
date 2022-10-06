<?php namespace Waka\Pdfer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Waka\Pdfer\Models\WakaPdf;
use Waka\Session\Models\WakaSession;
use Schema;

class ChangeWakaPdfsTableU200 extends Migration
{
    public function up()
    {
        $pdfs = WakaPdf::get();
        foreach($pdfs as $pdf) {
            $ds = $pdf->data_source;
            $testId = $pdf->test_id;
            if($ds) {
                $wakaSession = new WakaSession();
                $wakaSession->data_source = $ds;
                $wakaSession->ds_id_test = $testId;
                $wakaSession->name = 'pdf_'.$pdf->slug;
                $wakaSession->has_ds = true;
                $wakaSession->embed_all_ds = true;
                $wakaSession->key_duration = '1y';
                $wakaSession->save();
                $pdf->waka_session()->add($wakaSession);
            }
        }
    }

    public function down()
    {

    }
}