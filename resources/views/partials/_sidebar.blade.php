<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">ARUNIKA GROUP</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('dashboard') }}">AG</a>
        </div>
        <ul class="sidebar-menu">
            <li class="{{ request()->is('/') || request()->is('dashboard*') ? 'active' : ''}}"><a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-fire"></i> <span>Dashboard</span></a></li>
            @can('master-read')
            <li class="nav-item dropdown {{ request()->is('master*')  ? 'active' : ''}}">
                <a href="#" class="nav-link has-dropdown"><i class="fa fa-coins"></i> <span>Master Data</span></a>
                <ul class="dropdown-menu">
                    @if(auth()->user()->user_type == 'admin')
                    @can('mst-perusahaan-read')
                    <li class="{{ request()->is('master/company*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.company') }}">Perusahaan</a></li>
                    @endcan
                    @endif
                    @can('mst-divisi-read')
                    <li class="{{ request()->is('master/division*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.division') }}">Divisi</a></li>
                    @endcan
                    @can('mst-jabatan-read')
                    <li class="{{ request()->is('master/position*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.position') }}">Jabatan</a></li>
                    @endcan 
                    @can('mst-jenis-cuti-read')
                    <li class="{{ request()->is('master/leave-type*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.leave-type') }}">Jenis Cuti</a></li>
                    @endcan 
                    @can('mst-status-karyawan-read')
                    <li class="{{ request()->is('master/employee-type*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.employee-type') }}">Status Karyawan</a></li>
                    @endcan
                    @can('mst-golongan-karyawan-read')
                    <li class="{{ request()->is('master/employee-level*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('master.employee-level') }}">Golongan Karyawan</a></li>
                    @endcan
                </ul>
            </li>
            @endcan
            @can('data-karyawan-read')
            <li class="{{ request()->is('employee*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('employee') }}"><i class="fas fa-users"></i> <span>Data Karyawan</span></a></li>
            @endcan
            @can('absen-cuti-read')
            <li class="nav-item dropdown {{ request()->is('attendance-leave*')  ? 'active' : ''}}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-calendar-check"></i> <span>Absen Dan Cuti</span></a>
                <ul class="dropdown-menu">
                    @if(auth()->user()->user_type == 'employee')
                    <li class="{{ request()->is('attendance-leave/attendance*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('al.attendance') }}">Absensi</a></li>
                    <li class="{{ request()->is('attendance-leave/request-leave*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('al.request-leave') }}">Ajukan Cuti</a></li>
                    <li class="{{ request()->is('attendance-leave/leave-request*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('al.leave-request') }}">Ajukan Izin</a></li>
                    @endif
                    @can('cuti-read')
                    <li class="{{ request()->is('attendance-leave/employee-leave*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('al.approval') }}">Cuti Karyawan</a></li>
                    @endcan
                    @can('cuti-read')
                    <li class="{{ request()->is('attendance-leave/employee-leave-request*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('al.approval-request') }}">Izin Karyawan</a></li>
                    @endcan
                    @can('setting-absensi-read')
                    <li class="{{ request()->is('attendance-leave/time-config*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('al.time-config') }}">Setting Absensi</a></li>
                    @endcan
                    @can('absensi-karyawan-read')
                    <li class="{{ request()->is('attendance-leave/history-attendance*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('al.history') }}">Absensi Karyawan</a></li>
                    @endcan
                    @can('jatah-cuti-read')
                    <li class="{{ request()->is('attendance-leave/leave-quota*')  ? 'active' : ''}}"><a class="nav-link" href="{{ route('al.leave-quota') }}">Jatah Cuti</a></li>
                    @endcan
                </ul>
            </li>
            @endcan
            @can('user-read')
            <li class="{{ request()->is('user*') ? 'active' : ''}}"><a class="nav-link" href="{{ route('user') }}"><i class="fas fa-user"></i> <span>User</span></a></li>
            @endcan
            @can('role-read')
            <li class="{{ request()->is('roles*') ? 'active' : ''}}"><a class="nav-link" href="{{ route('roles') }}"><i class="fas fa-cog"></i> <span>Roles</span></a></li>
            @endcan
            @can('api-key-read')
            <li class="{{ request()->is('api-key*') ? 'active' : ''}}"><a class="nav-link" href="{{ route('apikey') }}"><i class="fas fa-cog"></i> <span>API Key</span></a></li>
            @endcan
        </ul>
    </aside>
</div>