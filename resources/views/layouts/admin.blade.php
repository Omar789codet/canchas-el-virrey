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

        /* Panel de perfil (esquina superior derecha) */
        .avatar-circle {
            width: 32px; height: 32px; border-radius: 50%;
            background: #22c55e; color: #0a0a0a; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: .85rem;
        }
        .perfil-toggle {
            background: transparent; border: 1px solid #333; border-radius: 999px;
            padding: .3rem .8rem .3rem .3rem; color: #ececec; display: flex; align-items: center; gap: .5rem;
        }
        .perfil-toggle:hover { border-color: #22c55e; }
        .dropdown-menu { background: #1e1e1e; border-color: #333; }
        .dropdown-item { color: #ececec; }
        .dropdown-item:hover { background: #262626; color: #22c55e; }
        .perfil-rol { font-size: .72rem; color: #a0a0a0; text-transform: capitalize; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <span class="navbar-brand mb-0">⚽ Canchas El Virrey</span>
                <a href="{{ route('admin.canchas.index') }}" class="text-decoration-none">Canchas</a>
                <a href="{{ route('admin.reservas.index') }}" class="text-decoration-none">Reservas</a>
                <a href="{{ route('admin.pagos.index') }}" class="text-decoration-none">Pagos</a>
            </div>

            @auth
            <div class="dropdown">
                <button class="perfil-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar-circle">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="text-start">
                        <span class="d-block" style="line-height:1;">{{ auth()->user()->name }}</span>
                        <span class="perfil-rol">{{ auth()->user()->role }}</span>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Editar mi perfil</a></li>
                    <li><hr class="dropdown-divider" style="border-color:#333;"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </nav>
    <div class="container pb-5">
        @if (session('ok'))
            <div class="alert alert-success">{{ session('ok') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
