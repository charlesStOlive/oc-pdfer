<?php namespace Waka\Pdfer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateWakaPdfsTableU150 extends Migration
{
    public function up()
    {
        Schema::table('waka_pdfer_waka_pdfs', function (Blueprint $table) {
            $table->renameColumn('pdf_name', 'output_name');
        });
    }

    public function down()
    {
        Schema::table('waka_pdfer_waka_pdfs', function (Blueprint $table) {
            $table->renameColumn('output_name', 'pdf_name');
        });
    }
}