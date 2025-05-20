@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
    <div class="col-md-6">
        <div class="card shadow rounded-4">
            <div class="card-header bg-success text-white text-center rounded-top-4">
                <h4 class="my-2">{{ __('Daftar Akun Baru') }}</h4>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Nama Lengkap') }}</label>
                        <input id="name" type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required>
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

                    {{-- Confirm Password --}}
                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">{{ __('Konfirmasi Password') }}</label>
                        <input id="password-confirm" type="password"
                               class="form-control"
                               name="password_confirmation" required>
                    </div>

                    {{-- Submit Button --}}
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-success w-100">
                            {{ __('Register') }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
