<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'machine_code',
        'village_id',
        'lokasi_penempatan',
        'status_mesin',
        'stok_beras_kg',
        'jadwal_mulai',
        'jadwal_selesai',
        'status_penjadwalan'
    ];

    public function village()
    {
        return $this->belongsTo(
            Village::class
        );
    }

    public function getStatusJadwalAttribute()
    {
        if (
            !$this->jadwal_mulai ||
            !$this->jadwal_selesai
        ) {
            return 'nonaktif';
        }

        $now = Carbon::now();

        if (
            $now >= Carbon::parse($this->jadwal_mulai) &&
            $now <= Carbon::parse($this->jadwal_selesai)
        ) {
            return 'aktif';
        }

        return 'nonaktif';
    }
    public function transactions()
    {
        return $this->hasMany(
            Transaction::class
        );
    }
}
