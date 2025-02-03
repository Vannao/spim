<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    @include('Template.head')
    <title>SPI Navigator - Temuan</title>
    <style>
        .form-container {
            display: none;
            margin-top: 20px;
        }
    </style>
</head>

<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-col="2-columns">

    {{-- Navbar --}}
    <!-- fixed-top-->
    @include('Template.nav')
    {{-- Navbar --}}

    {{-- Side Menu --}}
    @include('Template.side-menu')
    {{-- Side Menu --}}

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-8 col-12 mb-2 breadcrumb-new">
                    <h3 class="content-header-title mb-0 d-inline-block">Temuan</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('audit.index') }}">Laporan Hasil Audit</a>
                                </li>
                                <li class="breadcrumb-item active">Temuan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Temuan -->
                <section id="dom">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <a href="{{ route('audit.temuan.create', $auditId) }}"
                                        class="btn btn-primary mr-1">Tambah
                                        Temuan</a>
                                </div>
                                <div class="card-content collapse show">
                                    <div class="card-body card-dashboard">
                                        @if (Session::get('success'))
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                {{ Session::get('success') }}
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        @endif
                                        @if (Session::get('error'))
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                {{ Session::get('danger') }}
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        @endif
                                        <div class="table-responsive mt-3">
                                            {{ $dataTable->table(['class' => 'table table-striped table-bordered dom-jQuery-events', 'id' => 'table-id']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="modal fade" id="detailTemuan" data-backdrop="static" data-keyboard="false" tabindex="-1"
                    aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel"><strong>Detail Temuan</strong></h5>
                            </div>
                            <div class="modal-body">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" onclick="handlerDelete()"><i
                                        class="fa fa-trash"></i> Hapus
                                    Data</button>
                                <button type="button" class="btn btn-warning" onclick="handlerEdit()"><i
                                        class="fa fa-edit"></i> Ubah
                                    Data</button>
                                <button type="button" class="btn grey btn-outline-secondary"
                                    onclick="handlerCloseModal()">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ////////////////////////////////////////////////////////////////////////////-->

    {{-- Footer --}}
    @include('Template.footer')

    {{-- JS --}}
    @include('Template.js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script>
        const detailData = (id) => {
            $("#detailTemuan .modal-body").html();
            $.ajax({
                url: "{{ url('audit/temuan') }}" + `/${id}/show`,
                type: 'GET',
                dataType: 'JSON',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.data) {
                        let criterias = '';
                        let reasons = '';
                        let markFinding = '';

                        criterias += '<ol>';
                        if (response.data.criterias) {
                            response.data.criterias.map((value) => {
                                criterias += `<li>${value}</li>`
                            })
                        }
                        criterias += '</ol>';

                        reasons += '<ol>';
                        if (response.data.reasons) {
                            response.data.reasons.map((value) => {
                                reasons += `<li>${value}</li>`
                            })
                        }
                        reasons += '</ol>';

                        if (response.data.markFinding) {
                            markFinding += `<tr>
                                                <th>Nilai Temuan</th>
                                                <th class="text-center">:</th>
                                                <td >${response.data.markFinding}</td>
                                            </tr>`
                        }
                        $("#detailTemuan").modal('show');
                        $("#detailTemuan .modal-body").html(`
                            <input type="hidden" class="form-control" name="dataId" id="dataId" value="${response.data.id}"/>
                            <table class="table w-100">
                                <tr>
                                    <th width="25%">Temuan</th>
                                    <th width="2%" class="text-center">:</th>
                                    <td width="71%">${response.data.title}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Temuan</th>
                                    <th class="text-center">:</th>
                                    <td >${response.data.typeFinding}</td>
                                </tr>
                                ${markFinding}
                                <tr>
                                    <th>Penyebab</th>
                                    <th class="text-center">:</th>
                                    <td >${response.data.consequence}</td>
                                </tr>
                                <tr>
                                    <th>Kriteria</th>
                                    <th class="text-center">:</th>
                                    <td >${criterias}</td>
                                </tr>
                                <tr>
                                    <th>Akibat</th>
                                    <th class="text-center">:</th>
                                    <td>${reasons}</td>
                                </tr>
                            </table>
                        `);
                    }
                },
                error: function(xhr, error, status) {
                    alert(error)
                },
            });
        }

        const handlerEdit = () => {
            const dataId = $("#dataId").val();
            location.href = "{{ url('audit/temuan/' . $auditId) }}" + `/${dataId}/edit`;
        }

        const handlerCloseModal = () => {
            $("#detailTemuan").modal('hide');
            $("#detailTemuan .modal-body").html('')
        }

        const handlerDelete = () => {
            const dataId = $("#dataId").val();
            let confirmation = confirm("Yakin ingin menghapus data?");
            if (confirmation) {
                $.ajax({
                    url: "{{ url('audit/temuan/destroy') }}",
                    type: 'POST',
                    data: {
                        dataId,
                        '_method': 'delete'
                    },
                    dataType: 'JSON',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert(response.message)
                        if (response.status) {
                            setTimeout(() => location.reload(), 1000);
                        }

                    },
                    error: function(xhr, error, status) {
                        alert(error)
                    },
                });
            }
        }
    </script>
    {{-- JS --}}

</body>

</html>
