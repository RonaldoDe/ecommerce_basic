<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        // Actualizar el rating del producto solo si está aprobado
        if ($review->status === 'approved') {
            $review->product->updateRating();
        }
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        // Si cambió el status o el rating, actualizar el producto
        if ($review->isDirty('status') || $review->isDirty('rating')) {
            $review->product->updateRating();
        }
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        // Actualizar el rating del producto
        $review->product->updateRating();
    }
}