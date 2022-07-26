<?php

namespace Respins\BaseFunctions\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Players extends Eloquent  {
      
    protected $table = 'respins_players';
    public $timestamps = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'pid',
        'nickname',
        'active',
        'ownedBy',
    ];

    protected $hidden = [
        'id',
        'secret',
    ];
    
    protected $casts = [
        'data' => 'json',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
} 