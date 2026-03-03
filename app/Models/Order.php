<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'total',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'badge',
        'payment_status',
        'status',
        'transaction_id',
        'tracking_number',
        'shipping_company',
        'address',
        'address_id',
        'admin_notes',
        'customer_notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderAddress()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    /**
     * Get the items for the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the status history for the order.
     */
    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the refunds for the order.
     */
    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    /**
     * Get pending refunds.
     */
    public function pendingRefunds()
    {
        return $this->hasMany(Refund::class)->where('status', 'pending');
    }

    /**
     * Get completed refunds.
     */
    public function completedRefunds()
    {
        return $this->hasMany(Refund::class)->where('status', 'completed');
    }

    /**
     * Get the total amount refunded.
     */
    public function getTotalRefundedAttribute()
    {
        return $this->refunds()->where('status', 'completed')->sum('amount');
    }

    /**
     * Check if order has any refunds.
     */
    public function hasRefunds()
    {
        return $this->refunds()->count() > 0;
    }

    /**
     * Check if order has pending refunds.
     */
    public function hasPendingRefunds()
    {
        return $this->pendingRefunds()->count() > 0;
    }

    /**
     * Check if order has tracking information.
     */
    public function hasTracking()
    {
        return !empty($this->tracking_number);
    }

    /**
     * Check if order has discount.
     */
    public function hasDiscount()
    {
        return $this->discount_amount > 0;
    }

    /**
     * Get formatted total.
     */
    public function getFormattedTotalAttribute()
    {
        return $this->badge . ' ' . number_format($this->total, 2);
    }

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotalAttribute()
    {
        return $this->badge . ' ' . number_format($this->subtotal, 2);
    }

    /**
     * Get formatted discount.
     */
    public function getFormattedDiscountAttribute()
    {
        return $this->badge . ' ' . number_format($this->discount_amount, 2);
    }

    /**
     * Get the status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning text-dark">Pendiente</span>',
            'processing' => '<span class="badge bg-info">Procesando</span>',
            'shipped' => '<span class="badge bg-primary">Enviado</span>',
            'delivered' => '<span class="badge bg-success">Entregado</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelado</span>',
        ];

        return $badges[strtolower($this->status)] ?? '<span class="badge bg-secondary">Desconocido</span>';
    }

    /**
     * Get the payment status badge HTML.
     */
    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning text-dark">Pendiente</span>',
            'paid' => '<span class="badge bg-success">Pagado</span>',
            'completed' => '<span class="badge bg-success">Pagado</span>', // PayPal usa COMPLETED
            'failed' => '<span class="badge bg-danger">Fallido</span>',
            'refunded' => '<span class="badge bg-secondary">Reembolsado</span>',
        ];

        return $badges[strtolower($this->payment_status)] ?? '<span class="badge bg-secondary">Desconocido</span>';
    }

    /**
     * Scope a query to only include orders with tracking.
     */
    public function scopeWithTracking($query)
    {
        return $query->whereNotNull('tracking_number');
    }

    /**
     * Scope a query to only include orders with discounts.
     */
    public function scopeWithDiscount($query)
    {
        return $query->where('discount_amount', '>', 0);
    }

    
}
