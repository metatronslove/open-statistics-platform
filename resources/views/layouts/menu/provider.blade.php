<li class="nav-item">
    <a href="{{ route('provider.dashboard') }}" class="nav-link {{ request()->routeIs('provider.dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('provider.data-entry.index') }}" class="nav-link {{ request()->routeIs('provider.data-entry.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-edit"></i>
        <p>Veri Girişi</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('provider.profile') }}" class="nav-link {{ request()->routeIs('provider.profile*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-user"></i>
        <p>Profilim</p>
    </a>
</li>
