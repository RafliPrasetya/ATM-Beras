<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'district_id',
        'name'
    ];

    public $incrementing = false;

    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function mustahiks()
    {
        return $this->hasMany(
            Mustahik::class
        );
    }
}
