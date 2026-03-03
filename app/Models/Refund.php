<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $table = 'refunds';

    protected $fillable = [
        'order_id',
        'amount',
        'reason',
        'status',
        'requested_by',
        'processed_by',
        'processed_at',
        'admin_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the order that owns the refund.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who requested the refund.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the admin who processed the refund.
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope a query to only include pending refunds.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved refunds.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include completed refunds.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include rejected refunds.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Check if refund is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if refund is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if refund is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if refund is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the status badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning text-dark">Pendiente</span>',
            'approved' => '<span class="badge bg-info">Aprobado</span>',
            'rejected' => '<span class="badge bg-danger">Rechazado</span>',
            'completed' => '<span class="badge bg-success">Completado</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Desconocido</span>';
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        $setting = Ajuste::first();
        return ($setting->badge ?? '$') . ' ' . number_format($this->amount, 2);
    }
}