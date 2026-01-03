@extends('layouts.web')

@section('content')
<!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Carrito</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="{{ route('web.index') }}">Inicio</a></li>
            <li class="current">Carrito</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Cart Section -->
    <section id="cart" class="cart section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row">
          <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
            <div class="cart-items">
              <div class="cart-header d-none d-lg-block">
                <div class="row align-items-center">
                  <div class="col-lg-6">
                    <h5>Producto</h5>
                  </div>
                  <div class="col-lg-2 text-center">
                    <h5>Precio</h5>
                  </div>
                  <div class="col-lg-2 text-center">
                    <h5>Cantidad</h5>
                  </div>
                  <div class="col-lg-2 text-center">
                    <h5>Subtotal</h5>
                  </div>
                </div>
              </div>
              @php
                $total = 0;
              @endphp

              @foreach ($cart as $item)
                    <!-- Cart Item 1 -->
                    <div class="cart-item border-bottom">
                        <div class="row align-items-center">
                        <div class="col-lg-6 col-12 mt-3 mt-lg-0 mb-lg-0 mb-3">
                            <div class="product-info d-flex align-items-center">
                            <div class="product-image">
                                <a href="{{ route('web.product.show', $item->product->id) }}"><img src="{{ asset('storage/' . ($item->product->images->first()?->image ?? 'products/default_ot_image.png')) }}" alt="Product" class="img-fluid" loading="lazy"></a>
                            </div>
                            <div class="product-details">
                                <a href="{{ route('web.product.show', $item->product->id) }}"><h6 class="product-title">{{ $item->product->name }}</h6></a>
                                <span class="badge bg-danger">{{ $item->product->stock }} disponibles</span>
                                <form action="{{ route('web.cart.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete(this.form, '¿Estás seguro de eliminar el producto del carrito?')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center">
                            <div class="price-tag">
                            <span class="current-price">{{$settings->badge.$item->product->selling_price }}</span>
                            </div>
                        </div>
                        <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center">
                            <form action="{{ route('web.cart.update', $item->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="quantity-selector">
                              <button type="submit" class="quantity-btn decrease">
                                  <i class="bi bi-dash"></i>
                              </button>
                              <input type="number" class="quantity-input" value="{{ $item->quantity }}" min="1" max="5" name="quantity">
                              <button type="submit" class="quantity-btn increase">
                                  <i class="bi bi-plus"></i>
                              </button>
                              </div>
                            </form>
                        </div>
                        <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center">
                            <div class="item-total">
                                @php
                                    $subtotal = $item->product->selling_price * $item->quantity;
                                    $total += $subtotal;
                                @endphp
                            <span>{{$settings->badge.$subtotal }}</span>
                            </div>
                        </div>
                        </div>
                    </div><!-- End Cart Item -->
                  
              @endforeach

              <div class="cart-actions">
                <div class="row">
                  <div class="col-lg-6 mb-3 mb-lg-0">
                    <div class="coupon-form">
                      <div class="input-group">
                        <input type="text" class="form-control" placeholder="Coupon code">
                        <button class="btn btn-outline-accent" type="button">Aplicar cupón</button>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 text-md-end">
                    <form action="{{ route('web.cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-remove" onclick="confirmDelete(this.form, '¿Estás seguro de limpiar el carrito?')">
                        <i class="bi bi-trash"></i> Limpiar carrito
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="300">
            <div class="cart-summary">
              <h4 class="summary-title">Resumen del pedido</h4>

              <div class="summary-item">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value">{{ $settings->badge.$total }}</span>
              </div>

              <div class="summary-item shipping-item">
                <span class="summary-label">Envío</span>
                <div class="shipping-options">
                  <div class="form-check text-end">
                    <input class="form-check-input" type="radio" name="shipping" id="standard" checked="">
                    <label class="form-check-label" for="standard">
                      Standard Delivery - $4.99
                    </label>
                  </div>
                  <div class="form-check text-end">
                    <input class="form-check-input" type="radio" name="shipping" id="express">
                    <label class="form-check-label" for="express">
                      Express Delivery - $12.99
                    </label>
                  </div>
                  <div class="form-check text-end">
                    <input class="form-check-input" type="radio" name="shipping" id="free">
                    <label class="form-check-label" for="free">
                      Free Shipping (Orders over $300)
                    </label>
                  </div>
                </div>
              </div>

              <div class="summary-item">
                <span class="summary-label">Impuesto</span>
                <span class="summary-value">$27.00</span>
              </div>

              <div class="summary-item discount">
                <span class="summary-label">Descuento</span>
                <span class="summary-value">-$0.00</span>
              </div>

              <div class="summary-total">
                <span class="summary-label">Total</span>
                <span class="summary-value">{{ $settings->badge.$total }}</span>
              </div>

              <div class="checkout-button">
                <a href="#" class="btn btn-accent w-100">
                  Proceder al pago <i class="bi bi-arrow-right"></i>
                </a>
              </div>

              <div class="continue-shopping">
                <a href="{{ route('web.index') }}" class="btn btn-link w-100">
                  <i class="bi bi-arrow-left"></i> Seguir comprando
                </a>
              </div>

              <div class="payment-methods">
                <p class="payment-title">Formas de pago</p>
                <div class="payment-icons">
                  <i class="bi bi-credit-card"></i>
                  <i class="bi bi-paypal"></i>
                  <i class="bi bi-wallet2"></i>
                  <i class="bi bi-bank"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /Cart Section -->
    <script>
        function confirmDelete(form, message) {
          event.preventDefault();
            Swal.fire({
                title: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endsection