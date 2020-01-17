<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    protected $table = 'matches';
    protected $connection = 'mysql';
    protected $fillable = [
        'tournament_id',
        'homeTeam_id',
        'guestTeam_id',
        'date',
        'result',
        'link'
    ];
}
