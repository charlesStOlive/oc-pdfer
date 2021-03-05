<?php namespace Waka\Pdfer\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateLayoutsTableU102 extends Migration
{
    public function up()
    {
        Schema::table('waka_pdfer_layouts', function (Blueprint $table) {
            $table->text('options')->nullable();
        });
    }

    public function down()
    {
        Schema::table('waka_pdfer_layouts', function (Blueprint $table) {
            $table->dropColumn('options');
        });
    }
}
