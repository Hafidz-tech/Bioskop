<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            width: 200px;
            transition: all 0.3s ease;
        }

        .sidebar a {
            color: white;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar a.text-danger {
            color: red;
        }

        .sidebar a.text-danger i {
            color: red;
        }

        .sidebar a:hover.text-danger {
            background-color: #f8d7da;
        }

        .sidebar a.active {
            background-color: #495057;
            font-weight: bold;
        }

        .main-content {
            margin-left: 200px;
            padding: 0;
            width: 100%;
            transition: all 0.3s ease;
        }

        .navbar-custom {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
        }

        .navbar-toggle {
            cursor: pointer;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .sidebar-hidden .sidebar {
            margin-left: -200px;
        }

        .sidebar-hidden .main-content {
            margin-left: 0;
        }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    <div class="sidebar">
        <h4 class="p-3">Admin</h4>
        <a href="/admin/dashboard" id="dashboard-link">
            <i class="bi bi-house-door"></i> Dashboard
        </a>
        <a href="/admin/genre" id="genre-link">
            <i class="bi bi-film"></i> Genre Film
        </a>
        <a href="/admin/film" id="film-link">
            <i class="bi bi-camera-reels"></i> Kelola Film
        </a>
        <a href="/admin/studio" id="studio-link">
            <i class="bi bi-building"></i> Kelola Studio
        </a>
        <a href="/admin/jadwal" id="jadwal-link">
            <i class="bi bi-calendar"></i> Kelola Jadwal
        </a>
        <a href="/admin/kursi" id="kursi-link">
            <i class="bi bi-person-wheelchair"></i> Kelola Kursi
        </a>
        <a href="/admin/pemesanan" id="pemesanan-link">
            <i class="bi bi-ticket-perforated"></i> Kelola Tiket
        </a>
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="text-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>

    <div class="main-content">
        {{-- Navbar --}}
        <nav class="navbar navbar-custom">
            <span class="navbar-toggle" id="toggleSidebar">☰</span>
        </nav>

        {{-- Konten --}}
        <div class="p-4">
            @yield('content')
        </div>
    </div>
</div>

<script>
    // Toggle sidebar visibility
    const toggleBtn = document.getElementById('toggleSidebar');
    const wrapper = document.getElementById('wrapper');

    toggleBtn.addEventListener('click', () => {
        wrapper.classList.toggle('sidebar-hidden');
    });

    // Get the current URL path
    const currentPath = window.location.pathname;

    // Get all sidebar links
    const sidebarLinks = document.querySelectorAll('.sidebar a');

    // Loop through all links and add 'active' class to the matching link
    sidebarLinks.forEach(link => {
        const href = link.getAttribute('href');
        
        // Check if the current path matches the link's href or starts with it (useful for sub-routes)
        if (currentPath.startsWith(href)) {
            link.classList.add('active');
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Tambahkan ini di head atau sebelum penutup body di layout admin -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Semua skrip halaman yang di-push -->
    @stack('scripts')

</body>
</html>
