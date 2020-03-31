<?php namespace Waka\Pdfer\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateWakaPdfsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_pdfer_waka_pdfs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_pdfer_waka_pdfs');
    }
}
