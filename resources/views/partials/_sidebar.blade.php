<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">Stisla</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('dashboard') }}">St</a>
        </div>
        <ul class="sidebar-menu">
            <li class="{{ request()->is('/') || request()->is('dashboard*') ? 'active' : ''}}"><a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-fire"></i> <span>Dashboard</span></a></li>
            <li class="nav-item dropdown {{ request()->is('master*')  ? 'active' : ''}}">
                <a href="#" class="nav-link has-dropdown"><i class="fa fa-coins"></i> <span>Master Data</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->is('master/company*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.company') }}">Perusahaan</a></li>
                    <li class="{{ request()->is('master/division*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.division') }}">Divisi</a></li>
                    <li class="{{ request()->is('master/position*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.position') }}">Jabatan</a></li>
                    <li><a class="nav-link" href="bootstrap-alert.html">Jenis Cuti</a></li>
                </ul>
            </li>
        </ul>
    </aside>
</div>