<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'match_id',
        'member_id',
        'player_name',
        'position',
        'is_accepted',
        'is_paid',
        'team_name',
    ];

    public function match()
    {
        return $this->belongsTo(GolkrieMatch::class, 'match_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
