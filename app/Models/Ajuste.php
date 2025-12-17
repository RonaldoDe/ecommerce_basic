<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ajuste extends Model
{
    protected $table = 'ajustes';
    protected $fillable = [
        'name',
        'description',
        'branch',
        'address',
        'phones',
        'logo',
        'image_login',
        'email',
        'badge',
        'website',
    ];
}
