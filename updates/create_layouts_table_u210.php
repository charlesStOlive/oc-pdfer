<?php namespace Waka\Pdfer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateLayoutsTableU210 extends Migration
{
    public function up()
    {
        Schema::table('waka_pdfer_layouts', function (Blueprint $table) {
            $table->boolean('in_theme')->nullable()->default(false);
            $table->string('theme_page_path')->nullable();
        });
    }

    public function down()
    {
        Schema::table('waka_pdfer_layouts', function (Blueprint $table) {
            $table->dropColumn('in_theme');
            $table->dropColumn('theme_page_path');
        });
    }
}