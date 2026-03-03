<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\ReviewImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class ReviewController extends Controller
{
    /**
     * Mostrar todas las reseñas (Admin)
     */
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('rating') && $request->rating != '') {
            $query->where('rating', $request->rating);
        }

        if ($request->has('verified') && $request->verified != '') {
            $query->where('verified_purchase', $request->verified);
        }

        if($request->has('id') && $request->id != ''){
            $query->where('id', $request->id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%")
                  ->orWhereHas('product', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $reviews = $query->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Mostrar reseñas de un producto específico
     */
    public function productReviews(Product $product, Request $request)
    {
        $query = $product->reviews()
            ->approved()
            ->with(['user', 'images'])
            ->withCount(['helpfulness as user_vote' => function($q) {
                if (Auth::check()) {
                    $q->where('user_id', Auth::id());
                }
            }]);

        // Filtros
        $sort = $request->get('sort', 'recent');
        
        switch ($sort) {
            case 'helpful':
                $query->mostHelpful();
                break;
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        if ($request->has('rating') && $request->rating != '') {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->paginate(10);

        // Estadísticas de ratings
        $ratingStats = [
            5 => $product->reviews()->approved()->where('rating', 5)->count(),
            4 => $product->reviews()->approved()->where('rating', 4)->count(),
            3 => $product->reviews()->approved()->where('rating', 3)->count(),
            2 => $product->reviews()->approved()->where('rating', 2)->count(),
            1 => $product->reviews()->approved()->where('rating', 1)->count(),
        ];

        $totalReviews = array_sum($ratingStats);

        return view('web.product.reviews', compact('product', 'reviews', 'ratingStats', 'totalReviews'));
    }

    /**
     * Formulario para crear reseña
     */
    public function create(Product $product)
    {
        // Verificar si el usuario puede dejar una reseña
        if (!Auth::check()) {
            return redirect()->route('web.login')->with([
                'status' => 401,
                'icon' => 'error',
                'message' => 'Debes iniciar sesión para dejar una reseña.',
            ]);
        }

        $user = User::find(Auth::id());

        // Verificar si ya dejó una reseña
        if ($user->reviews()->where('product_id', $product->id)->exists()) {
            return redirect()->back()->with([
                'status' => 400,
                'icon' => 'error',
                'message' => 'Ya has dejado una reseña para este producto.',
            ]);
        }

        // Verificar si compró el producto
        $hasPurchased = $user->canReviewProduct($product->id);

        return view('web.reviews.create', compact('product', 'hasPurchased'));
    }

    /**
     * Guardar nueva reseña
     */
    public function store(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para dejar una reseña.',
            ], 401);
        }

        $user = User::find(Auth::id());

        // Verificar si ya dejó una reseña
        if ($user->reviews()->where('product_id', $product->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has dejado una reseña para este producto.',
            ], 400);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Verificar si el usuario compró el producto
            $verifiedPurchase = $user->canReviewProduct($product->id);

            // Crear la reseña
            $review = Review::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'],
                'rating' => $validated['rating'],
                'status' => 'pending', // Por defecto pendiente de aprobación
                'verified_purchase' => $verifiedPurchase,
            ]);

            // Guardar imágenes si hay
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('reviews', 'public');
                    
                    ReviewImage::create([
                        'review_id' => $review->id,
                        'image' => $path,
                        'order' => $index,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gracias por tu reseña. Será revisada antes de publicarse.',
                'review' => $review,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la reseña: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Review $review)
    {
        // Verificar que el usuario sea el dueño
        if ($review->user_id !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        // Verificar si puede editar (30 días)
        if (!$review->canBeEditedBy(Auth::id())) {
            return redirect()->back()->with([
                'status' => 403,
                'icon' => 'error',
                'message' => 'Solo puedes editar tu reseña dentro de 30 días.',
            ]);
        }

        return view('web.reviews.edit', compact('review'));
    }

    /**
     * Actualizar reseña
     */
    public function update(Request $request, Review $review)
    {
        // Verificar que el usuario sea el dueño
        if ($review->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado',
            ], 403);
        }

        // Verificar si puede editar
        if (!$review->canBeEditedBy(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes editar tu reseña dentro de 30 días.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'exists:review_images,id',
        ]);

        try {
            DB::beginTransaction();

            // Actualizar la reseña
            $review->update([
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'],
                'rating' => $validated['rating'],
                'status' => 'pending', // Volver a pendiente después de editar
            ]);

            // Eliminar imágenes marcadas
            if ($request->has('remove_images')) {
                $imagesToRemove = ReviewImage::whereIn('id', $request->remove_images)
                    ->where('review_id', $review->id)
                    ->get();

                foreach ($imagesToRemove as $img) {
                    Storage::disk('public')->delete($img->image);
                    $img->delete();
                }
            }

            // Agregar nuevas imágenes
            if ($request->hasFile('images')) {
                $currentCount = $review->images()->count();
                
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('reviews', 'public');
                    
                    ReviewImage::create([
                        'review_id' => $review->id,
                        'image' => $path,
                        'order' => $currentCount + $index,
                    ]);
                }
            }

            // Actualizar rating del producto
            $review->product->updateRating();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reseña actualizada correctamente.',
                'review' => $review->fresh(['images']),
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la reseña: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Eliminar reseña
     */
    public function destroy(Review $review)
    {
        $user = User::find(Auth::id());

        // Verificar que el usuario sea el dueño o admin
        if ($review->user_id !== Auth::id() && !$user->hasRole(['SUPER ADMINISTRADOR', 'ADMINISTRADOR'])) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado',
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Eliminar imágenes del storage
            foreach ($review->images as $image) {
                Storage::disk('public')->delete($image->image);
            }

            $productId = $review->product_id;
            
            // Eliminar la reseña
            $review->delete();

            // Actualizar rating del producto
            Product::find($productId)->updateRating();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reseña eliminada correctamente.',
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la reseña: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Aprobar reseña (Admin)
     */
    public function approve(Review $review)
    {
        try {
            $review->approve();

            return response()->json([
                'success' => true,
                'message' => 'Reseña aprobada correctamente.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar la reseña: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Rechazar reseña (Admin)
     */
    public function reject(Request $request, Review $review)
    {
        $validated = $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        try {
            $review->reject($validated['admin_note'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Reseña rechazada.',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar la reseña: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Marcar reseña como útil
     */
    public function markHelpful(Review $review)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión.',
            ], 401);
        }

        try {
            $review->markAsHelpful(Auth::id());

            return response()->json([
                'success' => true,
                'helpful_count' => $review->fresh()->helpful_count,
                'not_helpful_count' => $review->fresh()->not_helpful_count,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Marcar reseña como no útil
     */
    public function markNotHelpful(Review $review)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión.',
            ], 401);
        }

        try {
            $review->markAsNotHelpful(Auth::id());

            return response()->json([
                'success' => true,
                'helpful_count' => $review->fresh()->helpful_count,
                'not_helpful_count' => $review->fresh()->not_helpful_count,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Responder a una reseña (Admin/Vendedor)
     */
    public function respond(Request $request, Review $review)
    {
        $validated = $request->validate([
            'response' => 'required|string|min:10|max:500',
        ]);

        try {
            $review->addResponse($validated['response'], Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Respuesta agregada correctamente.',
                'review' => $review->fresh(['respondedBy']),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al responder: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mis reseñas (Usuario autenticado)
     */
    public function myReviews()
    {
        if (!Auth::check()) {
            return redirect()->route('web.login');
        }

        $user = User::find(Auth::id());

        $reviews = $user->reviews()
            ->with(['product', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('web.dashboard.reviews', compact('reviews'));
    }

    /**
     * Estadísticas de reseñas (Admin)
     */
    public function statistics()
    {
        $stats = [
            'total' => Review::count(),
            'pending' => Review::pending()->count(),
            'approved' => Review::approved()->count(),
            'rejected' => Review::rejected()->count(),
            'verified' => Review::verified()->count(),
            'with_response' => Review::withResponse()->count(),
            'average_rating' => round(Review::approved()->avg('rating'), 2),
        ];

        $ratingDistribution = [
            5 => Review::approved()->where('rating', 5)->count(),
            4 => Review::approved()->where('rating', 4)->count(),
            3 => Review::approved()->where('rating', 3)->count(),
            2 => Review::approved()->where('rating', 2)->count(),
            1 => Review::approved()->where('rating', 1)->count(),
        ];

        return view('admin.reviews.statistics', compact('stats', 'ratingDistribution'));
    }
}