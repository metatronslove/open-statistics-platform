<li class="nav-item">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Dashboard</p>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.users.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-users"></i>
        <p>
            Kullanıcı Yönetimi
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Tüm Kullanıcılar</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.users.create') }}" class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Yeni Kullanıcı</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item {{ request()->routeIs('admin.datasets.*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ request()->routeIs('admin.datasets.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-database"></i>
        <p>
            Veri Setleri
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ route('admin.datasets.index') }}" class="nav-link {{ request()->routeIs('admin.datasets.index') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Tüm Veri Setleri</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.datasets.create') }}" class="nav-link {{ request()->routeIs('admin.datasets.create') ? 'active' : '' }}">
                <i class="far fa-circle nav-icon"></i>
                <p>Yeni Veri Seti</p>
            </a>
        </li>
    </ul>
</li>

<li class="nav-item">
    <a href="{{ route('admin.validations.index') }}" class="nav-link {{ request()->routeIs('admin.validations.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-check-circle"></i>
        <p>Doğrulama Geçmişi</p>
    </a>
</li>

<li class="nav-item">
    <a href="#" class="nav-link">
        <i class="nav-icon fas fa-cog"></i>
        <p>Sistem Ayarları</p>
    </a>
</li>
