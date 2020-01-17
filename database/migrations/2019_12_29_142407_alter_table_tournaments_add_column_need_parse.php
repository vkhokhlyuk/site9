<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTournamentsAddColumnNeedParse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('tournaments', 'needParse')) {
            Schema::table('tournaments', function (Blueprint $table) {
                $table->tinyInteger('needParse')->default(1)->after('countTeams');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('tournaments', 'needParse')) {
            Schema::table('tournaments', function (Blueprint $table) {
                $table->dropColumn('needParse');
            });
        }
    }
}
