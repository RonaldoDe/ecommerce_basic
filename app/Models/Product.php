<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'short_description',
        'long_description',
        'cost_price',
        'selling_price',
        'stock',
        
        // SEO
        'meta_title',
        'meta_description',
        'meta_keywords',
        
        // Precios
        'cost_price',
        'selling_price',
        'discount_percentage',
        'discount_price',
        'discount_start_date',
        'discount_end_date',
        
        // Inventario
        'stock',
        'stock_alert',
        'manage_stock',
        'stock_status',
        
        // Dimensiones
        'weight',
        'dimensions',
        
        // Estado
        'status',
        'featured',
        'is_new',
        'visibility',
        
        // Ratings
        'rating',
        'reviews_count',
        'views_count',
        'sales_count',
        'wishlist_count',
        
        // Variantes
        'has_variants',
        'parent_id',
        
        // Información adicional
        'warranty',
        'return_policy',
        'shipping_info',
        'specifications',
        'tags',
        'search_keywords',
        
        // Publicación
        'published_at',
    ];

     protected $casts = [
        // 'images' => 'array',
        'dimensions' => 'array',
        'specifications' => 'array',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'weight' => 'decimal:2',
        'rating' => 'decimal:2',
        'status' => 'boolean',
        'featured' => 'boolean',
        'is_new' => 'boolean',
        'manage_stock' => 'boolean',
        'has_variants' => 'boolean',
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
        'published_at' => 'datetime',
    ];

     protected $appends = [
        'final_price',
        'is_on_sale',
        'discount_amount',
        'is_in_stock',
    ];

    protected $dates = ['deleted_at'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function favoriteProducts()
    {
        return $this->hasMany(FavoriteProduct::class);
    }
    
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    // public function variants()
    // {
    //     return $this->hasMany(Product::class, 'parent_id');
    // }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->belongsToMany(User::class, 'wishlists')
                    ->withTimestamps();
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('order');
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('status', true)->orderBy('order');
    }

    // Si tiene variantes, el stock total es la suma de las variantes
    public function getTotalStockAttribute(): int
    {
        if ($this->has_variants) {
            return $this->variants->sum('stock');
        }
        return $this->stock;
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = strtoupper(Str::random(8));
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }


    /**
     * Precio final (con descuento si aplica)
     */
    public function getFinalPriceAttribute()
    {
        if ($this->is_on_sale) {
            return $this->discount_price ?? $this->selling_price;
        }
        return $this->selling_price;
    }

    /**
     * Verifica si el producto está en oferta
     */
    public function getIsOnSaleAttribute()
    {
        if (!$this->discount_percentage || $this->discount_percentage <= 0) {
            return false;
        }

        $now = now();
        
        // Si hay fechas de descuento, verificar que estén en el rango
        if ($this->discount_start_date && $this->discount_end_date) {
            return $now->between($this->discount_start_date, $this->discount_end_date);
        }
        
        // Si solo hay fecha de inicio
        if ($this->discount_start_date) {
            return $now->gte($this->discount_start_date);
        }
        
        // Si solo hay fecha de fin
        if ($this->discount_end_date) {
            return $now->lte($this->discount_end_date);
        }
        
        // Si no hay fechas, el descuento está activo
        return true;
    }

    /**
     * Cantidad de descuento en dinero
     */
    public function getDiscountAmountAttribute()
    {
        if ($this->is_on_sale) {
            return $this->selling_price - $this->final_price;
        }
        return 0;
    }

    /**
     * Verifica si el producto está en stock
     */
    public function getIsInStockAttribute()
    {
        if (!$this->manage_stock) {
            return true;
        }
        
        return $this->stock > 0 && $this->stock_status === 'in_stock';
    }

    /**
     * Obtiene la primera imagen o la principal
     */
    // public function getImageAttribute()
    // {
    //     if ($this->main_image) {
    //         return $this->main_image;
    //     }
        
    //     if ($this->images && count($this->images) > 0) {
    //         return $this->images[0];
    //     }
        
    //     return asset('assets/img/default-product.png');
    // }

    // ==================== SCOPES ====================

    /**
     * Productos activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', true)
                    ->where('visibility', 'public');
    }

    /**
     * Productos destacados
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Productos nuevos
     */
    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    /**
     * Productos en stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0)
                    ->where('stock_status', 'in_stock');
    }

    /**
     * Productos en oferta
     */
    public function scopeOnSale($query)
    {
        $now = now();
        
        return $query->where('discount_percentage', '>', 0)
                    ->where(function($q) use ($now) {
                        $q->whereNull('discount_start_date')
                          ->orWhere('discount_start_date', '<=', $now);
                    })
                    ->where(function($q) use ($now) {
                        $q->whereNull('discount_end_date')
                          ->orWhere('discount_end_date', '>=', $now);
                    });
    }

    /**
     * Buscar productos
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%")
              ->orWhere('long_description', 'like', "%{$search}%")
              ->orWhere('tags', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%");
        });
    }

    /**
     * Filtrar por categoría
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Filtrar por marca
     */
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Filtrar por rango de precio
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('selling_price', [$min, $max]);
    }

    /**
     * Ordenar por popularidad
     */
    public function scopePopular($query)
    {
        return $query->orderBy('sales_count', 'desc')
                    ->orderBy('views_count', 'desc');
    }

    /**
     * Ordenar por rating
     */
    public function scopeTopRated($query)
    {
        return $query->orderBy('rating', 'desc')
                    ->where('rating', '>', 0);
    }

    // ==================== MÉTODOS ====================

    /**
     * Incrementar contador de vistas
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Calcular y actualizar el precio con descuento
     */
    public function calculateDiscountPrice()
    {
        if ($this->discount_percentage > 0) {
            $discount = ($this->selling_price * $this->discount_percentage) / 100;
            $this->discount_price = $this->selling_price - $discount;
            $this->save();
        }
    }

    /**
     * Verificar si necesita restock
     */
    public function needsRestock()
    {
        return $this->manage_stock && $this->stock <= $this->stock_alert;
    }

    /**
     * Reducir stock
     */
    public function reduceStock($quantity)
    {
        if ($this->manage_stock) {
            $this->decrement('stock', $quantity);
            
            if ($this->stock <= 0) {
                $this->update(['stock_status' => 'out_of_stock']);
            }
        }
    }

    /**
     * Aumentar stock
     */
    public function increaseStock($quantity)
    {
        if ($this->manage_stock) {
            $this->increment('stock', $quantity);
            
            if ($this->stock > 0) {
                $this->update(['stock_status' => 'in_stock']);
            }
        }
    }

    /**
     * Actualizar rating promedio
     */
    public function updateRating()
    {
        $avgRating = $this->reviews()->avg('rating') ?? 0;
        $reviewsCount = $this->reviews()->count();
        
        $this->update([
            'rating' => round($avgRating, 2),
            'reviews_count' => $reviewsCount,
        ]);
    }
}
