@extends('layouts.web')

@section('content')
    <!-- Page Title -->
    <div class="page-title light-background">
      <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Acceso</h1>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="{{ route('web.index') }}">Inicio</a></li>
            <li class="current">Acceso</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Login Section -->
    <section id="login" class="login section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row justify-content-center">
          <div class="col-lg-8 col-md-10">
            <div class="auth-container" data-aos="fade-in" data-aos-delay="200">

              <!-- Login Form -->
              <div class="auth-form login-form active">
                <div class="form-header">
                  <h3>Bienveido de nuevo</h3>
                  <p>Ingresa tus datos para iniciar sesión</p>
                </div>

                <form class="auth-form-content" action="{{ route('web.login.post') }}" method="POST">
                  @csrf
                  <div class="input-group mb-3">
                    <span class="input-icon">
                      <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Correo electrónico" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                  </div>

                  <div class="input-group mb-3">
                    <span class="input-icon">
                      <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña" required autocomplete="current-password">
                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                    <span class="password-toggle">
                      <i class="bi bi-eye"></i>
                    </span>
                  </div>

                  <div class="form-options mb-4">
                    <div class="remember-me">
                      <input type="checkbox" id="rememberLogin">
                      <label for="rememberLogin">Recordarme</label>
                    </div>
                    <a href="#" class="forgot-password">Olvidaste tu contraseña?</a>
                  </div>

                  <button type="submit" class="auth-btn primary-btn mb-3">
                    Iniciar sesión
                    <i class="bi bi-arrow-right"></i>
                  </button>

                  {{-- <div class="divider">
                    <span>o</span>
                  </div>

                  <button type="button" class="auth-btn social-btn">
                    <i class="bi bi-google"></i>
                    Iniciar sesión con Google
                  </button> --}}

                  <div class="switch-form">
                    <span>No tienes una cuenta?</span>
                    <a href="{{ route('web.register') }}" class="switch-btn" data-target="register">Registrate</a>
                  </div>
                </form>
              </div>

              <!-- Register Form -->
              {{-- <div class="auth-form register-form">
                <div class="form-header">
                  <h3>Crear Cuenta</h3>
                  <p>Únete hoy y comienza</p>
                </div>

                <form class="auth-form-content">
                  <div class="name-row">
                    <div class="input-group">
                      <span class="input-icon">
                        <i class="bi bi-person"></i>
                      </span>
                      <input type="text" class="form-control" placeholder="Nombre" required="" autocomplete="given-name">
                    </div>
                    <div class="input-group">
                      <span class="input-icon">
                        <i class="bi bi-person"></i>
                      </span>
                      <input type="text" class="form-control" placeholder="Apellido" required="" autocomplete="family-name">
                    </div>
                  </div>

                  <div class="input-group mb-3">
                    <span class="input-icon">
                      <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" class="form-control" placeholder="Correo electrónico" required="" autocomplete="email">
                  </div>

                  <div class="input-group mb-3">
                    <span class="input-icon">
                      <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control" placeholder="Contraseña" required="" autocomplete="new-password">
                    <span class="password-toggle">
                      <i class="bi bi-eye"></i>
                    </span>
                  </div>

                  <div class="input-group mb-3">
                    <span class="input-icon">
                      <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password" class="form-control" placeholder="Confirmar contraseña" required="" autocomplete="new-password">
                    <span class="password-toggle">
                      <i class="bi bi-eye"></i>
                    </span>
                  </div>

                  <div class="terms-check mb-4">
                    <input type="checkbox" id="termsRegister" required="">
                    <label for="termsRegister">
                      I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                    </label>
                  </div>

                  <button type="submit" class="auth-btn primary-btn mb-3">
                    Crear Cuenta
                    <i class="bi bi-arrow-right"></i>
                  </button>

                  <div class="divider">
                    <span>o</span>
                  </div>

                  <button type="button" class="auth-btn social-btn">
                    <i class="bi bi-google"></i>
                    Iniciar sesión con Google
                  </button>

                  <div class="switch-form">
                    <span>Ya tienes una cuenta?</span>
                    <button type="button" class="switch-btn" data-target="login">Iniciar sesión</button>
                  </div>
                </form>
              </div> --}}

            </div>
          </div>
        </div>

      </div>

    </section><!-- /Login Section -->
@endsection