<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';

    protected $fillable = [

        'judul',

        'konten',

        'gambar',

        'status',

        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(
            Admin::class,
            'created_by'
        );
    }
}
