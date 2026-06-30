<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [

        'mustahik_id',

        'machine_id',

        'jumlah_ambil_gram',

        'tanggal_pengambilan'
    ];

    public function mustahik()
    {
        return $this->belongsTo(
            Mustahik::class
        );
    }

    public function machine()
    {
        return $this->belongsTo(
            Machine::class
        );
    }
}
