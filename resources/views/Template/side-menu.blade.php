@if (Auth::user()->role === 'Super Admin')
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li><a class="menu-item" href="{{ route('dashboard') }}"><i class="fa fa-home"></i>
                        Dashboard</a></li>
                <li><a class="menu-item" href="/manage-pengguna"><i class="fa fa-users"></i>
                        Manage Pengguna</a></li>
                <li><a class="menu-item" href="/tambah-pengguna"><i class="fa fa-user-plus"></i>
                        Tambah Pengguna</a></li>
                <li><a class="menu-item " href="{{ route('audit.index') }}"><i class="fa fa-file-text"></i> Laporan
                        Hasil
                        Audit</a></li>
                <li><a class="menu-item" href="{{ route('tindak-lanjut.index') }}"><i class="fa fa-check-circle"></i>
                        Monitoring TL</a></li>
                <li><a class="menu-item" href="{{ route('laporan.index') }}"><i class="fa fa-folder-open"></i> Laporan
                        Kegiatan SPI</a></li>
            </ul>
        </div>
    </div>
@endif


@if (Auth::user()->role === 'Admin')
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li><a class="menu-item" href="{{ route('audit.index') }}"><i class="fa fa-file-text"></i>
                        Laporan
                        Hasil
                        Audit</a></li>
                <li><a class="menu-item" href="{{ route('tindak-lanjut.index') }}"><i class="fa fa-check-circle"></i>
                        Tindak
                        Lanjut</a></li>
                <li><a class="menu-item" href="{{ route('laporan.index') }}"><i class="fa fa-folder-open"></i> Laporan
                        Kegiatan SPI</a></li>
            </ul>
        </div>
    </div>
@endif



@if (Auth::user()->role === 'User')
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li><a class="menu-item" href="{{ route('audit.index') }}"><i class="fa fa-file-text"></i>
                        Laporan
                        Hasil
                        Audit</a></li>
                <li><a class="menu-item" href="{{ route('tindak-lanjut.index') }}"><i class="fa fa-check-circle"></i>
                        Monitoring TL
                    </a></li>
                <li><a class="menu-item" href="{{ route('laporan.index') }}"><i class="fa fa-folder-open"></i> Laporan
                        Kegiatan SPI</a></li>
            </ul>
        </div>
    </div>
@endif
