<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // Ini daftar kolom yang boleh diisi di database
    protected $fillable = [
        'nis',
        'name',
        'class',
        'email',
    ];
}