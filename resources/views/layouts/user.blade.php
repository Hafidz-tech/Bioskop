<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="#">Bioskop</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Menu di sebelah kiri -->
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('user.dashboard') }}">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('user.jadwal.index') }}">Jadwal</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('user.pemesanan.index') }}">Pemesanan</a>
            </li>
        </ul>

        <!-- Info user dan tombol logout di sebelah kanan -->
        <div class="d-flex align-items-center">
            <span class="text-white me-3">Halo, {{ Auth::user()->name }}</span>
            <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout
            </a>
        </div>
    </div>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</nav>

<div class="container mt-4">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
