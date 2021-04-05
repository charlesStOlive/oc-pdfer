<?php namespace Waka\Pdfer\Updates;

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class CreateLayoutsTableU103 extends Migration
{
    public function up()
    {
        Schema::table('waka_pdfer_layouts', function (Blueprint $table) {
            $table->boolean('use_header')->nullable();
            $table->text('header_html')->nullable();
            $table->boolean('use_footer')->nullable();
            $table->text('footer_html')->nullable();
        });
    }

    public function down()
    {
        Schema::table('waka_pdfer_layouts', function (Blueprint $table) {
            $table->dropColumn('use_header');
            $table->dropColumn('header_html');
            $table->dropColumn('use_footer');
            $table->dropColumn('footer_html');
        });
    }
}