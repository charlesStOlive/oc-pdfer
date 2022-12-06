<?php namespace Waka\Pdfer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateWakaPdfsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_pdfer_waka_pdfs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('state')->default('Actif');
            $table->string('slug');
            $table->string('output_name');
            $table->boolean('is_update_content')->default(false);
            $table->text('update_content')->nullable();
            $table->integer('layout_id')->unsigned()->nullable();
            $table->boolean('is_lot')->nullable()->default(true);
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