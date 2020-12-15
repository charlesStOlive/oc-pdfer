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
            $table->string('slug');
            $table->string('pdf_name');
            $table->integer('data_source_id');
            $table->integer('layout_id')->unsigned()->nullable();
            $table->text('template')->nullable();
            $table->text('model_functions')->nullable();
            $table->text('images')->nullable();
            $table->boolean('is_scope')->nullable()->default(false);
            $table->text('scopes')->nullable();
            $table->string('test_id')->nullable();
            //reorder
            $table->integer('sort_order')->default(0);
            //softDelete
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_pdfer_waka_pdfs');
    }
}