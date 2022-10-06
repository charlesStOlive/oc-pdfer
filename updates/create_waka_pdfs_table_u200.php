<?php namespace Waka\Pdfer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateWakaPdfsTableU200 extends Migration
{
    public function up()
    {
        Schema::table('waka_pdfer_waka_pdfs', function (Blueprint $table) {
        });
    }

    public function down()
    {
        Schema::table('waka_pdfer_waka_pdfs', function (Blueprint $table) {
        });
    }
}