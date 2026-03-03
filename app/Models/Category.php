<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // Relación con productos
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Subcategorías
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    // Categoría padre
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Solo categorías raíz (sin padre)
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    // Solo categorías activas
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('assets/static/images/no-image.png');
    }
}