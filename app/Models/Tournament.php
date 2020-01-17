<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    protected $fillable = [
        'name',
        'season',
        'link',
        'countTeams',
        'needParse'
    ];
}