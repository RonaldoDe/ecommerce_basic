<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{    
    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'product_code',
        'product_sku',
        'variant_attributes',
        'price',
        'quantity',
    ];

    protected $casts = [
        'variant_attributes' => 'array',
        'price'              => 'decimal:2',
    ];

    // ---- Relaciones ----

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed(); // por si el producto se elimina
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    // ---- Accessors ----

    /**
     * Subtotal del item (precio × cantidad)
     */
    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    /**
     * Nombre para mostrar: usa snapshot si el producto fue eliminado
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->product_name ?: ($this->product?->name ?? 'Producto eliminado');
    }

    /**
     * Label de la variante: "Color: Rojo / Talla: L"
     */
    public function getVariantLabelAttribute(): ?string
    {
        if (empty($this->variant_attributes)) return null;

        return collect($this->variant_attributes)
            ->map(fn($v, $k) => "{$k}: {$v}")
            ->implode(' / ');
    }

    /**
     * Imagen del item: usa la variante si tiene, si no la del producto
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->variant?->image) {
            return asset('storage/' . $this->variant->image);
        }
        $firstImage = $this->product?->images?->first();
        return $firstImage
            ? asset('storage/' . $firstImage->image)
            : asset('images/no-image.png');
    }
}