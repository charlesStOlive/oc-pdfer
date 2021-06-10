<?php namespace Waka\Pdfer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
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
