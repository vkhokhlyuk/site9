<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTeamsAddColumnCountryId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('teams', 'country_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->integer('country_id')->after('name');
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
        if (Schema::hasColumn('teams', 'country_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropColumn('country_id');
            });
        }
    }
}
