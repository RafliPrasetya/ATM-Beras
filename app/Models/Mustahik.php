<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mustahik extends Model
{
    protected $fillable = [
        'nama',
        'rfid_uid',
        'nik',
        'no_hp',
        'alamat',
        'village_id',
        'jatah_beras_gram',
        'status',
    ];

    public function village()
    {
        return $this->belongsTo(
            Village::class
        );
    }

    public function transactions()
    {
        return $this->hasMany(
            Transaction::class
        );
    }
}
