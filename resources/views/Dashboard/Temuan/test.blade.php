<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    @include('Template.head')
    <title>SPI Navigator - Laporan Hasil Audit</title>
    <style>
        .custom-file-label::after {
            content: "Pilih File";
        }

        /* Tambahan styling agar tabel memenuhi content body */
        .card {
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            margin-bottom: 0;
        }

        .content-body {
            padding: 2rem 2.5rem;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .breadcrumb-wrapper {
            margin-top: 0.5rem;
        }
    </style>
</head>

<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-col="2-columns">

    @include('Template.nav')
    @include('Template.side-menu')

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block">Temuan & Rekomendasi</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item active">PKPT</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <section id="pkpt-section">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">List Temuan</h4>
                                </div>

                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover align-middle">
                                                <thead class="thead-dark text-center">
                                                    <tr>
                                                        <th width="10%">No</th>
                                                        <th width="30%">Isi Temuan</th>
                                                        <th width="20%">Jenis Temuan</th>
                                                        <th width="20%">Akibat</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-center">
                                                    <!-- Data akan muncul di sini -->
                                                     @foreach ($temuan as $item)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $item->isi_temuan }}</td>
                                                            <td>{{ $item->jenis_temuan }}</td>
                                                            <td>{{ $item->akibat }}</td>
                                                        </tr>
                                                     @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex justify-content-center mt-3">
                                            <!-- Pagination atau lainnya -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailModalLabel">Detail Audit</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>ID PKPT:</strong> <span id="modalId"></span></p>
                    <p><strong>Nama Penugasan:</strong> <span id="modalNama"></span></p>
                    <p><strong>File:</strong> <a id="modalFile" href="" target="_blank">Lihat File</a></p>
                    <p><strong>Bulan Penugasan:</strong> <span id="modalBulan"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @include('Template.footer')
    @include('Template.js')
</body>

</html>
