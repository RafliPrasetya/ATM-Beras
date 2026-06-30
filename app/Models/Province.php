<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'name'
    ];

    public $incrementing = false;
    public function regencies()
    {
        return $this->hasMany(Regency::class);
    }
}
