<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableMatchesAddLinkColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('matches', ['homeTeam', 'guestTeam'])) {
            Schema::table('matches', function (Blueprint $table) {
                $table->integer('homeTeam_id')->nullable()->after('result');
                $table->integer('guestTeam_id')->nullable()->after('result');
                $table->dateTime('date')->nullable()->after('result');
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
        if (Schema::hasColumns('matches', ['homeTeam', 'guestTeam'])) {
            Schema::table('matches', function (Blueprint $table) {
                $table->dropColumn(['date', 'homeTeam', 'guestTeam']);
            });
        }
    }
}
