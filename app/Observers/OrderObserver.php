<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order)
    {
        $dirty = $order->getDirty();

        $statusChanged = array_key_exists('status', $dirty);
        $paymentStatusChanged = array_key_exists('payment_status', $dirty);

        if (!$statusChanged && !$paymentStatusChanged) {
            return;
        }

        $oldStatus = $order->getOriginal('status');
        $newStatus = $statusChanged ? $dirty['status'] : $oldStatus;

        $oldPaymentStatus = $order->getOriginal('payment_status');
        $newPaymentStatus = $paymentStatusChanged
            ? $dirty['payment_status']
            : $oldPaymentStatus;

        OrderStatusHistory::createHistory(
            $order->id,
            $oldStatus,
            $newStatus,
            $oldPaymentStatus,
            $newPaymentStatus,
            Auth::user()->id,
            'Cambio automático de estado'
        );
    }
}
