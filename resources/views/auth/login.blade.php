@extends('layouts.app')

@section('login')
<!--<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>-->
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-group mb-0">
            <div class="card p-4">
                <form class="form-inline" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="card-body">
                        <h1>Inicio de sesión</h1>                        
                        <p class="text-muted">Control de acceso al sistema</p>           
                        <div class="input-group flex-wrap mb-3">
                            <div class="input-group-addon"><i class="icon-user"></i></div>
                            <!--<input type="text" name="usuario" id="usuario" class="form-control" placeholder="Usuario">-->
                            <input id="usuario" type="text" class="form-control {{ $errors->has('usuario') ? ' is-invalid' : '' }}" name="usuario" value="{{ old('usuario') }}" required autofocus placeholder="Usuario">                                                        
                            @if ($errors->has('usuario'))
                                <div class="invalid-feedback w-100" role="alert">
                                    <strong>{{ $errors->first('usuario') }}</strong>
                                </div>
                            @endif
                        </div>
                        
                        <div class="input-group flex-wrap mb-4">
                            <div class="input-group-addon"><i class="icon-lock"></i></div>
                            <!--<input type="password" name="clave" id="clave" class="form-control" placeholder="Password">-->
                            <input id="password" type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="Contraseña">                            
                            @if ($errors->has('password'))
                                <div class="invalid-feedback w-100" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary px-3"><i class="fa fa-sign-in fa-lg" aria-hidden="true"></i> Ingresar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
                <div class="card-body text-center">
                    <div>
                        <h4>Grupo Cardeña S.A.C</h4>
                        <h3>Sistema de Ventas</h3>
                        <p>Sistema de ventas, inventario, facturación, clientes, proveedores y reportes.</p>
                        <a href="#" target="_blank" class="btn btn-primary active mt-3">Ayuda <i class="fa fa-question-circle fa-lg" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
