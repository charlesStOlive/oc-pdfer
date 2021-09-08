<?php namespace Waka\Pdfer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateWakaPdfsTableU130 extends Migration
{
    public function up()
    {
        Schema::table('waka_pdfer_waka_pdfs', function (Blueprint $table) {
            $table->boolean('is_lot')->nullable()->default(true);
        });
    }

    public function down()
    {
        Schema::table('waka_pdfer_waka_pdfs', function (Blueprint $table) {
            $table->dropColumn('is_lot');
        });
    }
}