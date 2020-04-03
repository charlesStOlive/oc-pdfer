<?php namespace Waka\Pdfer\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateWakaPdfsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_pdfer_waka_pdfs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->text('template')->nullable();

            $table->integer('data_source_id')->unsigned()->nullable();
            $table->text('scopes')->nullable();
            $table->text('model_functions')->nullable();
            $table->text('images')->nullable();

            $table->integer('sort_order')->default(0);

            $table->softDeletes();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_pdfer_waka_pdfs');
    }
}
