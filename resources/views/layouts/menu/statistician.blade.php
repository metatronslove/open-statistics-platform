<li class="nav-item">
    <a href="{{ route('statistician.dashboard') }}" class="nav-link {{ request()->routeIs('statistician.dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('statistician.datasets.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('statistician.datasets.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-database"></i>
        <p>
            Veri Setlerim
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('statistician.datasets.index') }}" class="nav-link {{ request()->routeIs('statistician.datasets.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Tüm Veri Setleri</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('statistician.datasets.create') }}" class="nav-link {{ request()->routeIs('statistician.datasets.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Yeni Veri Seti</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item {{ request()->routeIs('statistician.rules.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('statistician.rules.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-calculator"></i>
        <p>
            Hesaplama Kuralları
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('statistician.rules.index') }}" class="nav-link {{ request()->routeIs('statistician.rules.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Tüm Kurallar</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('statistician.rules.create') }}" class="nav-link {{ request()->routeIs('statistician.rules.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Yeni Kural</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ route('statistician.calculations.index') }}" class="nav-link {{ request()->routeIs('statistician.calculations.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-chart-line"></i>
        <p>Hesaplamalar</p>
    </a>
</li>
