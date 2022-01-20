<?php namespace Waka\Pdfer\Updates;

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Schema;

class CreateWakaPdfsTableU140 extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('waka_pdfer_waka_pdfs', 'state')){
            Schema::table('waka_pdfer_waka_pdfs', function (Blueprint $table) {
                $table->string('state')->default('Actif');
                $table->dropColumn('has_asks');
                $table->dropColumn('asks');
                $table->dropColumn('model_functions');
                $table->dropColumn('images');
                $table->dropColumn('is_scope');
                $table->dropColumn('scopes');
            });
        }
    }

    public function down()
    {
        Schema::table('waka_pdfer_waka_pdfs', function (Blueprint $table) {
            $table->dropColumn('state');
            $table->boolean('has_asks')->nullable();
            $table->text('asks')->nullable();
            $table->text('model_functions')->nullable();
            $table->text('images')->nullable();
            $table->boolean('is_scope')->nullable()->default(false);
            $table->text('scopes')->nullable();
        });
    }
}