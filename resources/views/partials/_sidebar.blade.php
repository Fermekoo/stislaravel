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
                    @if(auth()->user()->user_type == 'admin')
                    @can('mst-perusahaan-read')
                    <li class="{{ request()->is('master/company*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.company') }}">Perusahaan</a></li>
                    @endcan
                    @endif
                    <li class="{{ request()->is('master/division*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.division') }}">Divisi</a></li>
                    <li class="{{ request()->is('master/position*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.position') }}">Jabatan</a></li>
                    <li class="{{ request()->is('master/leave-type*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.leave-type') }}">Jenis Cuti</a></li>
                    <li class="{{ request()->is('master/employee-type*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.employee-type') }}">Status Karyawan</a></li>
                    <li class="{{ request()->is('master/employee-level*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.employee-level') }}">Golongan Karyawan</a></li>
                </ul>
            </li>
            <li class="{{ request()->is('employee*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('employee') }}"><i class="fas fa-users"></i> <span>Data Karyawan</span></a></li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-calendar-check"></i> <span>Absen Dan Cuti</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->is('master/company*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.company') }}">Absensi</a></li>
                    <li class="{{ request()->is('master/company*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.company') }}">Cuti Karyawan</a></li>
                    <li class="{{ request()->is('master/division*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.division') }}">Setting Absensi</a></li>
                    <li class="{{ request()->is('master/position*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.position') }}">Jatah Cuti</a></li>
                </ul>
            </li>
            <li class="{{ request()->is('user*') ? 'active' : ''}}"><a class="nav-link" href="{{ route('user') }}"><i class="fas fa-user"></i> <span>User</span></a></li>
            <li class="{{ request()->is('roles*') ? 'active' : ''}}"><a class="nav-link" href="{{ route('roles') }}"><i class="fas fa-cog"></i> <span>Roles</span></a></li>
        </ul>
    </aside>
</div>