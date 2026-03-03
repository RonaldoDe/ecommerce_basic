<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function favoriteProducts()
    {
        return $this->hasMany(FavoriteProduct::class);
    }
    
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the refunds requested by the user.
     */
    public function requestedRefunds()
    {
        return $this->hasMany(Refund::class, 'requested_by');
    }

    /**
     * Get the refunds processed by the user (admin).
     */
    public function processedRefunds()
    {
        return $this->hasMany(Refund::class, 'processed_by');
    }

    /**
     * Get the order status changes made by the user (admin).
     */
    public function orderStatusChanges()
    {
        return $this->hasMany(OrderStatusHistory::class, 'changed_by');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reviewHelpfulness()
    {
        return $this->hasMany(ReviewHelpfulness::class);
    }

    /**
     * Verificar si el usuario puede dejar una reseña para un producto
     */
    public function canReviewProduct($productId)
    {
        // Verificar si ya dejó una reseña
        $hasReviewed = $this->reviews()
            ->where('product_id', $productId)
            ->exists();
        
        if ($hasReviewed) {
            return false;
        }
        
        // Verificar si compró el producto
        $hasPurchased = $this->orders()
            ->whereHas('items', function($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->where('status', 'delivered') // O el estado que consideres como entregado
            ->exists();
        
        return $hasPurchased;
    }
}
