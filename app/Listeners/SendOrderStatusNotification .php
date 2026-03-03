<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Notifications\OrderStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderStatusNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event)
    {
        // // Enviar notificación al cliente
        // $event->order->user->notify(
        //     new OrderStatusChangedNotification($event->order, $event->oldStatus, $event->newStatus)
        // );
    }
}