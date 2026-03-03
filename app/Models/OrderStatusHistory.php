<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_status_history';

    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'old_payment_status',
        'new_payment_status',
        'changed_by',
        'notes',
    ];

    /**
     * Get the order that owns the status history.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user who made the change.
     */
    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Scope a query to only include order status changes.
     */
    public function scopeOrderStatusChanges($query)
    {
        return $query->whereNotNull('old_status')->whereNotNull('new_status');
    }

    /**
     * Scope a query to only include payment status changes.
     */
    public function scopePaymentStatusChanges($query)
    {
        return $query->whereNotNull('old_payment_status')->whereNotNull('new_payment_status');
    }

    /**
     * Get the formatted change description.
     */
    public function getChangeDescriptionAttribute()
    {
        $description = [];

        if ($this->old_status && $this->new_status) {
            $description[] = "Estado de orden: {$this->getStatusLabel($this->old_status)} → {$this->getStatusLabel($this->new_status)}";
        }

        if ($this->old_payment_status && $this->new_payment_status) {
            $description[] = "Estado de pago: {$this->getPaymentStatusLabel($this->old_payment_status)} → {$this->getPaymentStatusLabel($this->new_payment_status)}";
        }

        return implode(' | ', $description);
    }

    /**
     * Get status label in Spanish.
     */
    protected function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get payment status label in Spanish.
     */
    protected function getPaymentStatusLabel($status)
    {
        $labels = [
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'failed' => 'Fallido',
            'refunded' => 'Reembolsado',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get the old status badge HTML.
     */
    public function getOldStatusBadgeAttribute()
    {
        if (!$this->old_status) return null;
        return $this->getStatusBadge($this->old_status);
    }

    /**
     * Get the new status badge HTML.
     */
    public function getNewStatusBadgeAttribute()
    {
        if (!$this->new_status) return null;
        return $this->getStatusBadge($this->new_status);
    }

    /**
     * Get the old payment status badge HTML.
     */
    public function getOldPaymentStatusBadgeAttribute()
    {
        if (!$this->old_payment_status) return null;
        return $this->getPaymentStatusBadge($this->old_payment_status);
    }

    /**
     * Get the new payment status badge HTML.
     */
    public function getNewPaymentStatusBadgeAttribute()
    {
        if (!$this->new_payment_status) return null;
        return $this->getPaymentStatusBadge($this->new_payment_status);
    }

    /**
     * Get status badge HTML by status.
     */
    protected function getStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge bg-warning text-dark">Pendiente</span>',
            'processing' => '<span class="badge bg-info">Procesando</span>',
            'shipped' => '<span class="badge bg-primary">Enviado</span>',
            'delivered' => '<span class="badge bg-success">Entregado</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelado</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Desconocido</span>';
    }

    /**
     * Get payment status badge HTML by status.
     */
    protected function getPaymentStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge bg-warning text-dark">Pendiente</span>',
            'paid' => '<span class="badge bg-success">Pagado</span>',
            'failed' => '<span class="badge bg-danger">Fallido</span>',
            'refunded' => '<span class="badge bg-secondary">Reembolsado</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Desconocido</span>';
    }

    /**
     * Get formatted creation date.
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    /**
     * Static method to create a new history record.
     */
    public static function createHistory(
        $orderId,
        $oldStatus = null,
        $newStatus = null,
        $oldPaymentStatus = null,
        $newPaymentStatus = null,
        $changedBy = null,
        $notes = null
    ) {
        return self::create([
            'order_id' => $orderId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'old_payment_status' => $oldPaymentStatus,
            'new_payment_status' => $newPaymentStatus,
            'changed_by' => $changedBy ?? Auth::user()->id,
            'notes' => $notes,
        ]);
    }
}