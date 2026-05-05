<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GolkrieMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'title',
        'match_name',
        'date_time',
        'end_time',
        'location',
        'location_url',
        'quota',
        'quota_gk',
        'quota_df',
        'quota_mf',
        'quota_fw',
        'price',
        'media_url',
        'status',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'match_id');
    }
}
