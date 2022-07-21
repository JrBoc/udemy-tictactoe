<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turn extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $incrementing = false;

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }
}
