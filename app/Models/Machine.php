<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
        'is_wa_sent',
    ];

    protected $casts = [
        'jadwal_mulai' => 'datetime',
        'jadwal_selesai' => 'datetime',
    ];

    public function village()
    {
        return $this->belongsTo(
            Village::class
        );
    }

    public function getStatusPenjadwalanAttribute()
    {
        if (
            ! $this->jadwal_mulai ||
            ! $this->jadwal_selesai
        ) {
            return 'belum dijadwalkan';
        }

        $now = Carbon::now();

        if (
            $now >= $this->jadwal_mulai &&
            $now <= $this->jadwal_selesai
        ) {
            return 'aktif';
        }

        return 'nonaktif';
    }

    public function getStatusJadwalAttribute()
    {
        return $this->status_penjadwalan;
    }

    public function scopeJadwalAktif(Builder $query): Builder
    {
        $now = now();

        return $query
            ->whereNotNull('jadwal_mulai')
            ->whereNotNull('jadwal_selesai')
            ->where('jadwal_mulai', '<=', $now)
            ->where('jadwal_selesai', '>=', $now);
    }

    public function transactions()
    {
        return $this->hasMany(
            Transaction::class
        );
    }

    public function tokens()
    {
        return $this->hasMany(
            MachineToken::class
        );
    }

    public function apiLogs()
    {
        return $this->hasMany(
            ApiLog::class
        );
    }
}
