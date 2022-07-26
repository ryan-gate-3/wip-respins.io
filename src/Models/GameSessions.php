<?php

namespace Respins\BaseFunctions\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class GameSessions extends Eloquent  {
    protected $table = 'respins_gamesessions';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'token_internal',
        'player_id',
        'game_id',
        'currency',
        'state',
        'operator_id',
        'token_original',
        'games_amount',
        'expired_bool',
        'created_at',
        'updated_at'
    ];  

    protected $casts = [
        'extra_meta' => 'json'
    ];

}