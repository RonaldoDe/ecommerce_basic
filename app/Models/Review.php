<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'title',
        'comment',
        'rating',
        'status',
        'admin_note',
        'verified_purchase',
        'helpful_count',
        'not_helpful_count',
        'seller_response',
        'responded_at',
        'responded_by',
    ];

    protected $casts = [
        'rating' => 'integer',
        'verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'responded_at' => 'datetime',
    ];

    protected $appends = [
        'helpful_percentage',
    ];

    // ==================== RELACIONES ====================

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function images()
    {
        return $this->hasMany(ReviewImage::class)->orderBy('order');
    }

    public function respondedBy()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function helpfulness()
    {
        return $this->hasMany(ReviewHelpfulness::class);
    }

    // ==================== ACCESSORS ====================

    /**
     * Porcentaje de personas que encontraron útil la reseña
     */
    public function getHelpfulPercentageAttribute()
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        
        if ($total === 0) {
            return 0;
        }
        
        return round(($this->helpful_count / $total) * 100, 1);
    }

    // ==================== SCOPES ====================

    /**
     * Reseñas aprobadas
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Reseñas pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Reseñas rechazadas
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Reseñas verificadas (de compras verificadas)
     */
    public function scopeVerified($query)
    {
        return $query->where('verified_purchase', true);
    }

    /**
     * Reseñas con respuesta del vendedor
     */
    public function scopeWithResponse($query)
    {
        return $query->whereNotNull('seller_response');
    }

    /**
     * Reseñas sin respuesta del vendedor
     */
    public function scopeWithoutResponse($query)
    {
        return $query->whereNull('seller_response');
    }

    /**
     * Ordenar por más útiles
     */
    public function scopeMostHelpful($query)
    {
        return $query->orderBy('helpful_count', 'desc');
    }

    /**
     * Ordenar por rating
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Filtrar por rango de rating
     */
    public function scopeRatingRange($query, $min, $max)
    {
        return $query->whereBetween('rating', [$min, $max]);
    }

    /**
     * Reseñas de un usuario específico
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Reseñas de un producto específico
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    // ==================== MÉTODOS ====================

    /**
     * Aprobar la reseña
     */
    public function approve()
    {
        $this->update(['status' => 'approved']);
        
        // Actualizar el rating del producto
        $this->product->updateRating();
        
        return $this;
    }

    /**
     * Rechazar la reseña
     */
    public function reject($adminNote = null)
    {
        $this->update([
            'status' => 'rejected',
            'admin_note' => $adminNote,
        ]);
        
        // Actualizar el rating del producto
        $this->product->updateRating();
        
        return $this;
    }

    /**
     * Marcar como útil
     */
    public function markAsHelpful($userId)
    {
        // Verificar si el usuario ya votó
        $existing = $this->helpfulness()
            ->where('user_id', $userId)
            ->first();
        
        if ($existing) {
            // Si ya votó, actualizar su voto
            if ($existing->is_helpful) {
                // Ya había marcado como útil, remover el voto
                $existing->delete();
                $this->decrement('helpful_count');
            } else {
                // Había marcado como no útil, cambiar a útil
                $existing->update(['is_helpful' => true]);
                $this->decrement('not_helpful_count');
                $this->increment('helpful_count');
            }
        } else {
            // Nuevo voto
            $this->helpfulness()->create([
                'user_id' => $userId,
                'is_helpful' => true,
            ]);
            $this->increment('helpful_count');
        }
        
        return $this;
    }

    /**
     * Marcar como no útil
     */
    public function markAsNotHelpful($userId)
    {
        // Verificar si el usuario ya votó
        $existing = $this->helpfulness()
            ->where('user_id', $userId)
            ->first();
        
        if ($existing) {
            // Si ya votó, actualizar su voto
            if (!$existing->is_helpful) {
                // Ya había marcado como no útil, remover el voto
                $existing->delete();
                $this->decrement('not_helpful_count');
            } else {
                // Había marcado como útil, cambiar a no útil
                $existing->update(['is_helpful' => false]);
                $this->decrement('helpful_count');
                $this->increment('not_helpful_count');
            }
        } else {
            // Nuevo voto
            $this->helpfulness()->create([
                'user_id' => $userId,
                'is_helpful' => false,
            ]);
            $this->increment('not_helpful_count');
        }
        
        return $this;
    }

    /**
     * Verificar si un usuario ya votó
     */
    public function hasUserVoted($userId)
    {
        return $this->helpfulness()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Obtener el voto de un usuario
     */
    public function getUserVote($userId)
    {
        return $this->helpfulness()
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Agregar respuesta del vendedor
     */
    public function addResponse($response, $userId)
    {
        $this->update([
            'seller_response' => $response,
            'responded_at' => now(),
            'responded_by' => $userId,
        ]);
        
        return $this;
    }

    /**
     * Verificar si el usuario puede editar esta reseña
     */
    public function canBeEditedBy($userId)
    {
        return $this->user_id === $userId && 
               $this->created_at->diffInDays(now()) <= 30; // Solo puede editar dentro de 30 días
    }

    /**
     * Verificar si el usuario puede eliminar esta reseña
     */
    public function canBeDeletedBy($userId)
    {
        return $this->user_id === $userId;
    }

    /**
     * Obtener las estrellas como array (para mostrar en la vista)
     */
    public function getStarsArray()
    {
        return [
            'full' => floor($this->rating),
            'half' => ($this->rating - floor($this->rating)) >= 0.5 ? 1 : 0,
            'empty' => 5 - ceil($this->rating),
        ];
    }
}