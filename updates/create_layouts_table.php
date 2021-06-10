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
            $table->text('contenu')->nullable();
            $table->string('baseCss')->nullable();
            $table->text('AddCss');
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