<?php namespace Waka\Pdfer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateLayoutsTable extends Migration
{
    public function up()
    {
        Schema::create('waka_pdfer_layouts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('layout_path')->nullable();
            $table->boolean('layout_require_code')->nullable()->default(true);
            $table->text('add_css');
            $table->boolean('use_header')->nullable();
            $table->text('header_html')->nullable();
            $table->boolean('use_footer')->nullable();
            $table->text('footer_html')->nullable();
            $table->text('options')->nullable();
            //reorder
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waka_pdfer_layouts');
    }
}