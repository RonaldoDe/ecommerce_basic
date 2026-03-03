<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - Orden #{{ $order->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .invoice-container { box-shadow: none; }
        }
        body { background: #f0f2f5; padding: 30px 0; font-family: 'Segoe UI', sans-serif; }
        .invoice-container {
            max-width: 860px; margin: 0 auto;
            background: white; border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .invoice-header {
            background: linear-gradient(135deg, #1e3a5f, #2e6fad);
            color: white; padding: 35px 40px;
        }
        .invoice-header .invoice-num { font-size: 1.1rem; opacity: 0.8; }
        .invoice-header .invoice-title { font-size: 2.2rem; font-weight: 700; letter-spacing: 2px; }
        .company-name { font-size: 1.3rem; font-weight: 600; }
        .invoice-body { padding: 35px 40px; }
        .section-label {
            font-size: 0.75rem; text-transform: uppercase;
            letter-spacing: 1px; color: #6c757d; margin-bottom: 5px;
        }
        .info-box {
            background: #f8f9fa; border-radius: 8px;
            padding: 15px 20px; height: 100%;
        }
        .status-badge { padding: 5px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .status-pending  { background: #fff3cd; color: #856404; }
        .status-paid     { background: #d1edda; color: #155724; }
        .status-shipped  { background: #cce5ff; color: #004085; }
        .status-cancelled{ background: #f8d7da; color: #721c24; }
        .table-products thead { background: #1e3a5f; color: white; }
        .table-products thead th { padding: 12px 15px; font-weight: 500; border: none; }
        .table-products tbody td { padding: 12px 15px; vertical-align: middle; }
        .table-products tbody tr:nth-child(even) { background: #f8f9fa; }
        .variant-chip {
            display: inline-block; background: #e9ecef;
            border: 1px solid #dee2e6; border-radius: 20px;
            padding: 2px 10px; font-size: 0.75rem; margin: 2px;
        }
        .total-box {
            background: #f8f9fa; border-radius: 8px;
            padding: 20px 25px; border-left: 4px solid #1e3a5f;
        }
        .total-row { display: flex; justify-content: space-between; padding: 4px 0; }
        .total-row.grand { border-top: 2px solid #1e3a5f; margin-top: 8px; padding-top: 12px; font-size: 1.1rem; }
        .tracking-bar {
            background: #e3f2fd; border-left: 4px solid #2e6fad;
            border-radius: 4px; padding: 12px 20px; margin: 20px 0;
        }
        .footer-invoice { background: #f8f9fa; padding: 20px 40px; text-align: center; }
    </style>
</head>
<body>
    {{-- Botones --}}
    <div class="no-print text-center mb-3 d-flex justify-content-center gap-2">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Imprimir / Guardar PDF
        </button>
        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="invoice-container">
        {{-- Header --}}
        <div class="invoice-header">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="invoice-title">FACTURA</div>
                    <div class="invoice-num">Orden #{{ $order->id }}</div>
                    @if($order->transaction_id)
                        <div class="invoice-num mt-1">TX: {{ $order->transaction_id }}</div>
                    @endif
                    @if($order->coupon_code)
                        <span class="badge bg-warning text-dark mt-2">
                            <i class="bi bi-ticket-perforated"></i> Cupón: {{ $order->coupon_code }}
                        </span>
                    @endif
                </div>
                <div class="col-6 text-end">
                    <div class="company-name">{{ $settings->company_name ?? 'Tu Empresa' }}</div>
                    <div class="opacity-75 small mt-1">{{ $settings->address ?? '' }}</div>
                    <div class="opacity-75 small">{{ $settings->phone ?? '' }}</div>
                    <div class="opacity-75 small">{{ $settings->email ?? '' }}</div>
                </div>
            </div>
        </div>

        <div class="invoice-body">
            {{-- Info cliente y orden --}}
            <div class="row mb-4">
                <div class="col-md-5">
                    <div class="section-label">Facturar a</div>
                    <div class="info-box">
                        <div class="fw-bold">{{ $order->user->name }}</div>
                        <div class="text-muted small">{{ $order->user->email }}</div>
                        @if($order->address)
                            <hr class="my-2">
                            <div class="small text-muted">Dirección de envío:</div>
                            <div class="small">{{ $order->address }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="section-label">Detalles</div>
                    <div class="info-box">
                        <div class="small mb-1">
                            <span class="text-muted">Fecha:</span>
                            <strong>{{ $order->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div class="small mb-1">
                            <span class="text-muted">Estado orden:</span>
                            @php
                                $statusClass = ['PENDING'=>'status-pending','PROCESSING'=>'status-pending','SHIPPED'=>'status-shipped','DELIVERED'=>'status-paid','CANCELLED'=>'status-cancelled'];
                                $statusLabel = ['PENDING'=>'Pendiente','PROCESSING'=>'Procesando','SHIPPED'=>'Enviado','DELIVERED'=>'Entregado','CANCELLED'=>'Cancelado'];
                            @endphp
                            <span class="status-badge {{ $statusClass[$order->status] ?? '' }}">
                                {{ $statusLabel[$order->status] ?? $order->status }}
                            </span>
                        </div>
                        <div class="small">
                            <span class="text-muted">Estado pago:</span>
                            @php
                                $payClass = ['PENDING'=>'status-pending','PAID'=>'status-paid','COMPLETED'=>'status-paid','FAILED'=>'status-cancelled','REFUNDED'=>'status-pending'];
                                $payLabel = ['PENDING'=>'Pendiente','PAID'=>'Pagado','COMPLETED'=>'Pagado','FAILED'=>'Fallido','REFUNDED'=>'Reembolsado'];
                            @endphp
                            <span class="status-badge {{ $payClass[$order->payment_status] ?? '' }}">
                                {{ $payLabel[$order->payment_status] ?? $order->payment_status }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="section-label">Total a pagar</div>
                    <div class="info-box text-center">
                        <div class="text-muted small">Total</div>
                        <div style="font-size:1.8rem; font-weight:700; color:#1e3a5f;">
                            {{ $settings->badge }}{{ number_format($order->total, 2) }}
                        </div>
                        @if($order->hasDiscount())
                            <div class="text-success small">
                                Ahorro: {{ $settings->badge }}{{ number_format($order->discount_amount, 2) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tracking --}}
            @if($order->hasTracking())
                <div class="tracking-bar">
                    <i class="bi bi-truck text-primary me-2"></i>
                    <strong>Envío:</strong>
                    {{ $order->shipping_company }} —
                    <span class="badge bg-primary">{{ $order->tracking_number }}</span>
                </div>
            @endif

            {{-- Tabla de productos --}}
            <table class="table table-products w-100 mb-4" style="border-radius:8px; overflow:hidden;">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th style="width:100px" class="text-center">Precio</th>
                        <th style="width:80px" class="text-center">Cant.</th>
                        <th style="width:110px" class="text-center">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $item->display_name }}</div>
                                <div class="text-muted small">
                                    @if($item->product_code) Cod: {{ $item->product_code }} @endif
                                    @if($item->product_sku) · SKU: {{ $item->product_sku }} @endif
                                </div>
                                {{-- Variantes como chips --}}
                                @if($item->variant_attributes)
                                    <div class="mt-1">
                                        @foreach($item->variant_attributes as $key => $value)
                                            <span class="variant-chip">{{ $key }}: {{ $value }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">{{ $settings->badge }}{{ number_format($item->price, 2) }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center fw-bold">{{ $settings->badge }}{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totales --}}
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <div class="total-box">
                        <div class="total-row">
                            <span class="text-muted">Subtotal:</span>
                            <span>{{ $settings->badge }}{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->hasDiscount())
                            <div class="total-row text-success">
                                <span>
                                    Descuento
                                    @if($order->coupon_code)({{ $order->coupon_code }})@endif:
                                </span>
                                <span>-{{ $settings->badge }}{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="total-row grand fw-bold">
                            <span>TOTAL:</span>
                            <span style="color:#1e3a5f;">{{ $settings->badge }}{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notas del cliente --}}
            @if($order->customer_notes)
                <div class="mt-4">
                    <div class="section-label">Notas del cliente</div>
                    <div class="info-box">{{ $order->customer_notes }}</div>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="footer-invoice">
            <small class="text-muted">
                Gracias por su compra · Documento generado el {{ now()->format('d/m/Y H:i') }}<br>
                Orden #{{ $order->id }} — {{ $settings->company_name ?? '' }}
            </small>
        </div>
    </div>
</body>
</html>