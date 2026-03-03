<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'cost_price',
        'stock',
        'image',
        'status',
        'attributes',
        'order',
    ];

    protected $casts = [
        'attributes' => 'array',
        'price'       => 'decimal:2',
        'cost_price'  => 'decimal:2',
        'status'      => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Precio efectivo: si tiene precio propio lo usa, sino usa el del producto
    public function getEffectivePriceAttribute(): float
    {
        return $this->price ?? $this->product->selling_price;
    }

    // Nombre legible: "Color: Rojo / Talla: L"
    public function getLabelAttribute(): string
    {
        $attrs = $this->getAttribute('attributes') ?? [];
        
        if (empty($attrs)) {
            return 'Sin atributos';
        }

        return collect($attrs)
            ->map(fn($v, $k) => "{$k}: {$v}")
            ->implode(' / ');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return $this->product->images->first()
            ? asset('storage/' . $this->product->images->first()->image)
            : asset('assets/static/images/no-image.png');
    }

    // Stock total del producto sumando variantes
    public static function totalStockForProduct(int $productId): int
    {
        return static::where('product_id', $productId)->sum('stock');
    }
}