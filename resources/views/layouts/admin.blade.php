<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Panel') - Canchas El Virrey</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bs-body-bg: #121212;
            --bs-body-color: #ececec;
            --bs-border-color: #333333;
            --bs-primary: #22c55e;
            --bs-primary-rgb: 34, 197, 94;
        }
        body { background: #121212; }
        .navbar { background: #1a1a1a !important; border-bottom: 1px solid #333; }
        .card, table, .table { background: #1e1e1e; }
        .table { --bs-table-bg: #1e1e1e; --bs-table-color: #ececec; --bs-table-border-color: #333; }
        .bg-white { background: #1e1e1e !important; }
        .btn-primary { background: #22c55e; border-color: #22c55e; color: #0a0a0a; font-weight: 600; }
        .btn-primary:hover { background: #16a34a; border-color: #16a34a; }
        .btn-outline-secondary, .btn-outline-danger { border-color: #444; }
        .form-control, .form-select { background: #262626; color: #ececec; border-color: #333; }
        .form-control:focus, .form-select:focus { background: #262626; color: #ececec; border-color: #22c55e; box-shadow: 0 0 0 .2rem rgba(34,197,94,.25); }
        a { color: #22c55e; }
        .badge.bg-success { background-color: #22c55e !important; color: #0a0a0a; }
        .badge.bg-warning { background-color: #f59e0b !important; color: #0a0a0a; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container">
            <span class="navbar-brand">⚽ Canchas El Virrey — Administración</span>
            <div class="d-flex gap-3">
                <a href="{{ route('admin.canchas.index') }}" class="text-decoration-none">Canchas</a>
                <a href="{{ route('admin.reservas.index') }}" class="text-decoration-none">Reservas</a>
                <a href="{{ route('admin.pagos.index') }}" class="text-decoration-none">Pagos</a>
            </div>
        </div>
    </nav>
    <div class="container pb-5">
        @if (session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
