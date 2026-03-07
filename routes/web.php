<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\AdminController::class, 'index'])->name('home')->middleware('auth');

Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin')->middleware('auth.redirect');

// AJUSTES
Route::get('/admin/settings', [App\Http\Controllers\AjusteController::class, 'index'])->name('admin.settings.index')->middleware(['auth.redirect', 'can:settings.index']);
Route::post('/admin/settings/create', [App\Http\Controllers\AjusteController::class, 'store'])->name('admin.settings.store')->middleware(['auth.redirect', 'can:settings.create']);


// ROLES
Route::get('/admin/roles', [App\Http\Controllers\RoleController::class, 'index'])->name('admin.roles.index')->middleware(['auth.redirect', 'can:roles.index']);
Route::get('/admin/roles/create', [App\Http\Controllers\RoleController::class, 'create'])->name('admin.roles.create')->middleware(['auth.redirect', 'can:roles.create']);
Route::post('/admin/roles/create', [App\Http\Controllers\RoleController::class, 'store'])->name('admin.roles.store')->middleware(['auth.redirect', 'can:roles.create']);
Route::get('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'show'])->name('admin.roles.show')->middleware(['auth.redirect', 'can:roles.show']);
Route::get('/admin/roles/{id}/edit', [App\Http\Controllers\RoleController::class, 'edit'])->name('admin.roles.edit')->middleware(['auth.redirect', 'can:roles.edit']);
Route::put('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'update'])->name('admin.roles.update')->middleware(['auth.redirect', 'can:roles.edit']);
Route::delete('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('admin.roles.destroy')->middleware(['auth.redirect', 'can:roles.destroy']);


// USUARIOS
Route::get('/admin/users', [App\Http\Controllers\UserController::class, 'index'])->name('admin.users.index')->middleware(['auth.redirect', 'can:users.index']);
Route::get('/admin/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('admin.users.create')->middleware(['auth.redirect', 'can:users.create']);
Route::post('/admin/users/create', [App\Http\Controllers\UserController::class, 'store'])->name('admin.users.store')->middleware(['auth.redirect', 'can:users.create']);
Route::get('/admin/users/{id}', [App\Http\Controllers\UserController::class, 'show'])->name('admin.users.show')->middleware(['auth.redirect', 'can:users.show']);
Route::get('/admin/users/{id}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('admin.users.edit')->middleware(['auth.redirect', 'can:users.edit']);
Route::put('/admin/users/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('admin.users.update')->middleware(['auth.redirect', 'can:users.edit']);
Route::delete('/admin/users/{id}', [App\Http\Controllers\UserController::class, 'destroy'])->name('admin.users.destroy')->middleware(['auth.redirect', 'can:users.destroy']);
Route::post('/admin/users/{id}/restore', [App\Http\Controllers\UserController::class, 'restore'])->name('admin.users.restore')->middleware(['auth.redirect', 'can:users.destroy']);

// CATEGORIAS

Route::prefix('admin')->name('admin.')->middleware('auth.redirect')->group(function () {

    Route::post('categories/{category}/set-parent', [CategoryController::class, 'setParent'])
     ->name('admin.categories.set-parent');
    
    Route::resource('categories', CategoryController::class)->middleware([
            'index'   => 'can:categories.index',
            'create'  => 'can:categories.create',
            'store'   => 'can:categories.create',
            'show'    => 'can:categories.show',
            'edit'    => 'can:categories.edit',
            'update'  => 'can:categories.edit',
            'destroy' => 'can:categories.destroy',
        ]);
    });

// Rutas públicas de reseñas
Route::get('/product/{product}/reviews', [ReviewController::class, 'productReviews'])->name('product.reviews');


// Rutas autenticadas
Route::middleware(['auth', 'can:reviews.create'])->group(function () {
    // Crear reseña
    Route::get('/product/{product}/review/create', [ReviewController::class, 'create'])->name('review.create');
    Route::post('/product/{product}/review', [ReviewController::class, 'store'])->name('review.store');
    
    // Editar/Eliminar reseña
    Route::get('/review/{review}/edit', [ReviewController::class, 'edit'])->name('review.edit')->middleware('can:reviews.edit');
    Route::put('/review/{review}', [ReviewController::class, 'update'])->name('review.update')->middleware('can:reviews.edit');
    Route::delete('/review/{review}', [ReviewController::class, 'destroy'])->name('review.destroy')->middleware('can:reviews.destroy');
    
    // Marcar como útil
    Route::post('/review/{review}/helpful', [ReviewController::class, 'markHelpful'])->name('review.helpful')->middleware('can:reviews.create');
    Route::post('/review/{review}/not-helpful', [ReviewController::class, 'markNotHelpful'])->name('review.notHelpful')->middleware('can:reviews.create');
    
    // Mis reseñas
    Route::get('/my-reviews', [ReviewController::class, 'myReviews'])->name('my.reviews')->middleware('can:reviews.show');
});

// Rutas de administración
Route::prefix('admin')->name('admin.')->middleware(['auth.redirect', 'role:SUPER ADMINISTRADOR|ADMINISTRADOR'])->group(function () {
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index')->middleware('can:reviews.index');
    Route::get('/reviews/statistics', [ReviewController::class, 'statistics'])->name('reviews.statistics')->middleware('can:reviews.create');
    Route::post('/review/{review}/approve', [ReviewController::class, 'approve'])->name('review.approve')->middleware('can:reviews.create');
    Route::post('/review/{review}/reject', [ReviewController::class, 'reject'])->name('review.reject')->middleware('can:reviews.create');
    Route::post('/review/{review}/respond', [ReviewController::class, 'respond'])->name('review.respond')->middleware('can:reviews.create');
});

// PRODUCTOS
Route::prefix('admin')->name('admin.')->middleware('auth.redirect')->group(function () {Route::resource('products', ProductController::class)
    ->middleware([
            'index'   => 'can:products.index',
            'create'  => 'can:products.create',
            'store'   => 'can:products.create',
            'show'    => 'can:products.show',
            'edit'    => 'can:products.edit',
            'update'  => 'can:products.edit',
            'destroy' => 'can:products.destroy',
        ]);
        Route::prefix('products/{product}/variants')->name('products.variants.')->group(function () {
            Route::post('/',                      [ProductController::class, 'storeVariant'])->name('store');
            Route::put('/{variant}',             [ProductController::class, 'updateVariant'])->name('update');
            Route::delete('/{variant}',          [ProductController::class, 'destroyVariant'])->name('destroy');
        });
    
    });
Route::get('/admin/products/{product}/images', [App\Http\Controllers\ProductController::class, 'images'])->name('admin.products.images')->middleware(['auth.redirect', 'can:products.show']);
Route::post('/admin/products/{id}/upload_image', [App\Http\Controllers\ProductController::class, 'uploadImage'])->name('admin.products.uploadImage')->middleware(['auth.redirect', 'can:products.edit']);
Route::delete('/admin/products/image/{id}/remove_image', [App\Http\Controllers\ProductController::class, 'removeImage'])->name('admin.products.removeImage')->middleware(['auth.redirect', 'can:products.edit']);
// Acciones adicionales
Route::prefix('admin')->name('admin.')->middleware('auth.redirect')->group(function () {
    Route::post('products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.updateStock');
    Route::post('products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
    Route::get('products-export', [ProductController::class, 'export'])->name('products.export');
});
// ORDENES
Route::get('/admin/orders/statistics', [OrderController::class, 'statistics'])->name('admin.orders.statistics')->middleware('auth.redirect');
// Rutas para actualización rápida de estados
Route::patch('/admin/orders/{id}/update-status', [OrderController::class, 'updateStatus'])->name('admin.orders.update-status')->middleware(['auth.redirect', 'can:orders.edit']);
Route::patch('/admin/orders/{id}/update-payment-status', [OrderController::class, 'updatePaymentStatus'])->name('admin.orders.update-payment-status')->middleware(['auth.redirect', 'can:orders.edit']);

// Ruta para actualizar tracking (NUEVO)
Route::patch('/admin/orders/{id}/update-tracking', [OrderController::class, 'updateTracking'])->name('admin.orders.update-tracking')->middleware(['auth.redirect', 'can:orders.edit']);
// Ruta para generar factura
Route::get('/admin/orders/{id}/invoice', [OrderController::class, 'invoice'])->name('admin.orders.invoice')->middleware(['auth.redirect', 'can:orders.create']);

Route::prefix('admin')->name('admin.')->middleware('auth.redirect')->group(function () {Route::resource('orders', OrderController::class)->middleware([
            'index'   => 'can:orders.index',
            'create'  => 'can:orders.create',
            'store'   => 'can:orders.create',
            'show'    => 'can:orders.show',
            'edit'    => 'can:orders.edit',
            'update'  => 'can:orders.edit',
            'destroy' => 'can:orders.destroy',
        ]);});

Route::prefix('admin')->name('admin.')->middleware(['auth.redirect'])->group(function () {
    
    // Rutas de cupones
    Route::resource('coupons', CouponController::class)->middleware([
            'index'   => 'can:coupons.index',
            'create'  => 'can:coupons.create',
            'store'   => 'can:coupons.create',
            'show'    => 'can:coupons.show',
            'edit'    => 'can:coupons.edit',
            'update'  => 'can:coupons.edit',
            'destroy' => 'can:coupons.destroy',
        ]);;
    
    // Rutas adicionales
    Route::put('/coupons/{id}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status')->middleware('can:coupons.edit');
    Route::put('/coupons/{id}/reset-usage', [CouponController::class, 'resetUsage'])->name('coupons.reset-usage')->middleware('can:coupons.edit');
    
});

Route::prefix('admin')->name('admin.pages.')->middleware(['auth.redirect'])->group(function () {
    
    // Nosotros
    Route::get ('about',         [AboutController::class, 'edit'])   ->name('about.edit');
    Route::put ('about',         [AboutController::class, 'update']) ->name('about.update');

    // Equipo (miembros)
    Route::post  ('about/members',         [AboutController::class, 'storeMember'])   ->name('about.members.store');
    Route::put   ('about/members/{member}',[AboutController::class, 'updateMember'])  ->name('about.members.update');
    Route::delete('about/members/{member}',[AboutController::class, 'destroyMember']) ->name('about.members.destroy');
    
});

// WEB
Route::get('/', [App\Http\Controllers\WebController::class, 'index'])->name('web.index');
Route::get('/product/{product}', [App\Http\Controllers\ProductController::class, 'show_web'])->name('web.product.show');
Route::get('/cart', [App\Http\Controllers\DashboardController::class, 'cart'])->name('web.cart');
Route::get('/web/login', [App\Http\Controllers\DashboardController::class, 'login'])->name('web.login');
Route::post('/web/login', [App\Http\Controllers\DashboardController::class, 'loginPost'])->name('web.login.post');
Route::get('/web/register', [App\Http\Controllers\DashboardController::class, 'register'])->name('web.register');
Route::post('/web/register', [App\Http\Controllers\DashboardController::class, 'registerPost'])->name('web.register.post');
Route::get('/web/search', [App\Http\Controllers\WebController::class, 'search'])->name('web.search');
Route::get('web/categorias', [App\Http\Controllers\WebController::class, 'categories'])->name('web.categories');
Route::get('web/categorias/{id}', [App\Http\Controllers\WebController::class, 'category'])->name('web.category');
Route::get('web/nosotros', [AboutController::class, 'index'])->name('web.about');

// DASHBOARD - Protegido con middleware auth
Route::prefix('web')->name('web.')->middleware(['auth.redirect'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/orders', [App\Http\Controllers\DashboardController::class, 'orders'])->middleware('can:orders.index')->name('dashboard.orders');
    // Dentro del grupo middleware(['auth.redirect'])
    Route::get('/dashboard/orders/{order}', [App\Http\Controllers\DashboardController::class, 'orderDetail'])->middleware('can:orders.show')->name('dashboard.orders.detail');
    Route::get('/dashboard/wishlist', [App\Http\Controllers\DashboardController::class, 'wishlist'])->name('dashboard.wishlist');
    Route::get('/dashboard/payment-methods', [App\Http\Controllers\DashboardController::class, 'paymentMethods'])->name('dashboard.payment');
    Route::get('/dashboard/reviews', [App\Http\Controllers\DashboardController::class, 'reviews'])->name('dashboard.reviews');
    Route::get('/dashboard/addresses', [App\Http\Controllers\DashboardController::class, 'addresses'])->name('dashboard.addresses');

    Route::post('/dashboard/settings/update', [App\Http\Controllers\DashboardController::class, 'updateProfile'])->name('dashboard.settings.update');
    Route::post('/dashboard/settings/password', [App\Http\Controllers\DashboardController::class, 'updatePassword'])->name('dashboard.settings.password');

    Route::get('/dashboard/settings', [App\Http\Controllers\DashboardController::class, 'settings'])->name('dashboard.settings');
    
    // Favoritos
    Route::get('/favorites', [App\Http\Controllers\FavoriteProductController::class, 'index'])->name('favorites.index');
    Route::post('/favorites', [App\Http\Controllers\FavoriteProductController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{id}', [App\Http\Controllers\FavoriteProductController::class, 'destroy'])->name('favorites.destroy');

    // Direcciones
    Route::get('/addresses', [App\Http\Controllers\AddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
    Route::put('/addresses/{id}', [App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{id}', [App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');

    Route::post('/addresses/{address}/set-default', [App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.setDefault');

    // Carrito
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

    // Rutas de cupones (NUEVAS)
    Route::post('/cart/apply-coupon', [App\Http\Controllers\CartController::class, 'applyCoupon'])->name('cart.apply-coupon');
    Route::post('/cart/remove-coupon', [App\Http\Controllers\CartController::class, 'removeCoupon'])->name('cart.remove-coupon');

    // PAYPAL
    Route::post('/paypal/payment', [App\Http\Controllers\PaypalController::class, 'payment'])->name('paypal.payment');
    Route::get('/paypal/success', [App\Http\Controllers\PaypalController::class, 'success'])->name('paypal.success');
    Route::get('/paypal/order_completed', [App\Http\Controllers\PaypalController::class, 'orderCompleted'])->name('paypal.order_completed');
    Route::get('/paypal/cancel', [App\Http\Controllers\PaypalController::class, 'cancel'])->name('paypal.cancel');
});

Route::middleware(['auth'])->group(function () {


});


Route::fallback(function () {
    if (request()->is('admin/*') || request()->is('admin')) {
        return response()->view('errors.404-admin', [], 200);
    }

    return response()->view('errors.404', [], 200);
});