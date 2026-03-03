@extends('web.dashboard.layout')

@section('dashboard-content')
<div class="section-header" data-aos="fade-up">
    <h2>Mis Órdenes</h2>
    <div class="header-actions">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Buscar órdenes...">
        </div>
        <div class="dropdown">
            <button class="filter-btn" data-bs-toggle="dropdown">
                <i class="bi bi-funnel"></i>
                <span>Filtrar</span>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#">Todas las Órdenes</a></li>
                <li><a class="dropdown-item" href="#">Procesando</a></li>
                <li><a class="dropdown-item" href="#">Enviadas</a></li>
                <li><a class="dropdown-item" href="#">Entregadas</a></li>
                <li><a class="dropdown-item" href="#">Canceladas</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="orders-grid">
    @forelse($orders as $order)
    <!-- Order Card -->
    <div class="order-card" data-aos="fade-up">
        <div class="order-header">
            <div class="order-id">
                <span class="label">Orden ID:</span>
                <span class="value">#{{ $order->id }}</span>
            </div>
            <div class="order-date">{{ $order->created_at->format('M d, Y') }}</div>
        </div>
        <div class="order-content">
            <div class="product-grid">
                @foreach($order->items->take(3) as $item)
                <img src="{{ asset('storage/' . ($item->product->images->first()?->image ?? 'products/default_ot_image.png')) }}" alt="{{ $item->product->name }}" loading="lazy">
                @endforeach
                @if($order->items->count() > 3)
                <span class="more-items">+{{ $order->items->count() - 3 }}</span>
                @endif
            </div>
            <div class="order-info">
                <div class="info-row">
                    <span>Estado</span>
                    @if ($order->status === 'COMPLETED')
                        <span class="status completed">Completada</span>
                    @elseif ($order->status === 'PENDING')
                        <span class="status pending">Pendiente</span>
                    @elseif ($order->status === 'PROCESSING')
                    <span class="status processing">Procesando</span>
                    @elseif ($order->status === 'SHIPPED')
                        <span class="status shipped">Enviada</span>
                    @elseif ($order->status === 'CANCELLED')
                        <span class="status cancelled">Cancelada</span>
                    @else
                        <span class="status other">{{ ucfirst($order->status) }}</span>
                    @endif
                </div>
                <div class="info-row">
                    <span>Artículos</span>
                    <span>{{ $order->items->count() }} artículos</span>
                </div>
                <div class="info-row">
                    <span>Total</span>
                    <span class="price">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="order-footer">
            <button type="button" class="btn-track">Rastrear Orden</button>
            <a href="{{ route('web.dashboard.orders.detail', $order->id) }}" class="btn-details">Ver Detalles</a>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="bi bi-box-seam"></i>
        <h3>No tienes órdenes aún</h3>
        <p>Comienza a comprar y tus órdenes aparecerán aquí</p>
        <a href="{{ route('web.index') }}" class="btn btn-primary">Ir a Comprar</a>
    </div>
    @endforelse
</div>

<style>
.orders-grid {
    display: grid;
    gap: 20px;
}

.order-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    transition: box-shadow 0.3s;
}

.order-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f0f0f0;
}

.order-id .label {
    color: #999;
    font-size: 14px;
}

.order-id .value {
    font-weight: 600;
    color: #333;
    margin-left: 5px;
}

.order-date {
    color: #666;
    font-size: 14px;
}

.order-content {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 20px;
    margin-bottom: 15px;
}

.product-grid {
    display: flex;
    gap: 5px;
}

.product-grid img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

.more-items {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 8px;
    font-weight: 600;
    color: #666;
}

.order-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-row span:first-child {
    color: #666;
    font-size: 14px;
}

.status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status.processing {
    background: #fff3cd;
    color: #856404;
}

.status.shipped {
    background: #d1ecf1;
    color: #0c5460;
}

.status.delivered {
    background: #d4edda;
    color: #155724;
}

.status.cancelled {
    background: #f8d7da;
    color: #721c24;
}

.price {
    font-weight: 600;
    color: #007bff;
    font-size: 16px;
}

.order-footer {
    display: flex;
    gap: 10px;
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
}

.btn-track,
.btn-details {
    flex: 1;
    padding: 10px;
    border: 1px solid #6c757d;
    background: white;
    color: #6c757d;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-track:hover {
    background: #007bff;
    color: white;
}

.btn-details {
    border-color: #6c757d;
    color: #6c757d;
}

.btn-details:hover {
    background: #6c757d;
    color: white;
}

.search-box {
    position: relative;
}

.search-box input {
    padding: 8px 15px 8px 35px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    width: 250px;
}

.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.filter-btn {
    padding: 8px 15px;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 64px;
    color: #ccc;
    margin-bottom: 20px;
}

.empty-state h3 {
    margin-bottom: 10px;
    color: #333;
}

.empty-state p {
    color: #666;
    margin-bottom: 20px;
}
</style>
@endsection