<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'province_id',
        'name'
    ];

    public $incrementing = false;

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
