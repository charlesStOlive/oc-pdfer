<?php namespace Waka\Pdfer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class ChangeLayoutsTableU210 extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('waka_pdfer_layouts', 'addCss')) return;
        Schema::table('waka_pdfer_layouts', function (Blueprint $table) {
            $table->renameColumn('addCss', 'add_css');
            $table->renameColumn('baseCss', 'base_css');
        });
    }

    public function down()
    {
        Schema::table('waka_pdfer_layouts', function (Blueprint $table) {
            $table->renameColumn('add_css', 'addCss');
            $table->renameColumn('base_css', 'baseCss');
        });
    }
}