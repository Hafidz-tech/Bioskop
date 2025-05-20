@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="col-md-6">
        <div class="card shadow rounded-4">
            <div class="card-header bg-primary text-white text-center rounded-top-4">
                <h4 class="my-2">{{ __('Login Akun') }}</h4>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember"
                               name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                    </div>

                    {{-- Button --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary w-50">
                            {{ __('Login') }}
                        </button>

                        @if (Route::has('password.request'))
                            <a class="text-decoration-none ms-3" href="{{ route('password.request') }}">
                                {{ __('Forgot Password?') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
