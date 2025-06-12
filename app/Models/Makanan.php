<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makanan extends Model
{
    protected $table = 'makanan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama',
        'gambar',
        'email'
    ];

    public $timestamps = false;
}
