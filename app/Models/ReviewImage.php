<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'image',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Obtener la URL completa de la imagen
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }
}