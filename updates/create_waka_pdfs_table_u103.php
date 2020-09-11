<?php namespace Waka\Pdfer\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateWakaPdfsTableU103 extends Migration
{
    public function up()
    {
        Schema::table('waka_pdfer_waka_pdfs', function (Blueprint $table) {
            $table->string('scope_type')->nullable();
        });
    }

    public function down()
    {
        Schema::table('waka_pdfer_waka_pdfs', function (Blueprint $table) {
            $table->dropColumn('scope_type');
        });
    }
}
