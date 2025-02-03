<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    @include('Template.head')
    <title>SPI Navigator - Tindak Lanjut Hasil Audit</title>
    <style>
        .form-container {
            display: none;
            margin-top: 20px;
        }

        .table td,
        .table th {
            word-wrap: break-word;
            white-space: normal;
            max-width: 200px;
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
                    <h3 class="content-header-title mb-0 d-inline-block">Tindak Lanjut Hasil Audit</h3>
                    <div class="row breadcrumbs-top d-inline-block">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active">Tindak Lanjut Hasil Audit
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Tabel Rekomendasi -->
                <section id="dom">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
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
                <!-- DOM - jQuery events table -->
                <div class="modal fade" id="detailLaporanHasilAudit" data-backdrop="static" data-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel"><strong>Ubah Status</strong></h5>
                            </div>
                            <div class="modal-body">

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-warning" onclick="handlerSave()"><i
                                        class="fa fa-check-square-o"></i> Save</button>
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
        const handlerSetStatus = (id) => {
            $("#detailLaporanHasilAudit .modal-body").html();
            $.ajax({
                url: "{{ url('tindak-lanjut') }}" + `/${id}/show`,
                type: 'GET',
                dataType: 'JSON',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.data) {
                        let findings = '';
                        let status = '';

                        if (response.data.audit.findings) {
                            response.data.audit.findings.map((value, index) => {
                                findings += `<span>${index + 1}. ${value.title}</span><br>`
                            })
                        }

                        if (response.statusData) {
                            status +=
                                `<select class="form-control" name="setStatus" id="setStatus" onchange="handlerChangeStatus(this.value)">`;
                            status += `<option value="">Pilih Status</option>`
                            response.statusData.map((value) => {
                                status += `<option value="${value.value}">${value.label}</option>`
                            })
                            status += `</select>`;
                        }
                        $("#detailLaporanHasilAudit").modal('show');
                        $("#detailLaporanHasilAudit .modal-body").html(`
                            <input type="hidden" class="form-control" name="dataId" id="dataId" value="${response.data.id}"/>
                            <table class="table w-100">
                                <tr>
                                    <th width="25%">Nomor LHA</th>
                                    <th width="2%" class="text-center">:</th>
                                    <td width="71%">${response.data.audit.code}</td>
                                </tr>
                                <tr>
                                    <th>Judul LHA</th>
                                    <th class="text-center">:</th>
                                    <td >${response.data.audit.title}</td>
                                </tr>
                                <tr>
                                    <th>Temuan</th>
                                    <th class="text-center">:</th>
                                    <td>${findings}</td>
                                </tr>
                                <tr>
                                    <th>Rekomendasi</th>
                                    <th class="text-center">:</th>
                                    <td>${response.data.title}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <th class="text-center">:</th>
                                    <td>${status}</td>
                                </tr>
                                <tr id="fieldClosedAudit" style="display: none">
                                    <th>Closed (Upload Surat/Nota Dinas)</th>
                                    <th class="text-center">:</th>
                                    <td>
                                        <div class="position-relative has-icon-left">
                                            <input type="file" name="attacment" id="attacment" class="form-control">
                                            <div class="form-control-position">
                                                <i class="fa fa-file-pdf-o"></i>
                                            </div>
                                        </div>
                                    </td>
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

        const handlerChangeStatus = (status) => {
            if (status == 3) {
                $("#fieldClosedAudit").show();
            } else {
                $("#fieldClosedAudit").hide();
                $("#fieldClosedAudit").val('');
            }
        }

        const handlerCloseModal = () => {
            $("#detailLaporanHasilAudit").modal('hide');
            $("#detailLaporanHasilAudit .modal-body").html('')
        }

        const handlerSave = () => {
            const dataId = $("#dataId").val();
            const status = $("#setStatus").val();
            const fileDinas = $("#attacment");

            if (status == '') alert('status harus diisi');
            if (status == 3 && fileDinas.val() == '') alert('uplaod file tidak boleh kosong');

            const formData = new FormData();
            formData.append('status', status);
            if (status == 3) {
                formData.append('file_dinas', fileDinas[0].files[0]);
            }
            $.ajax({
                url: "{{ url('tindak-lanjut') }}" + `/${dataId}/update`,
                type: 'POST',
                dataType: 'JSON',
                processData: false,
                contentType: false,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        handlerCloseModal();
                        location.reload(); // reload page
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, error, status) {
                    alert(error)
                },
            });
        }
    </script>
    {{-- JS --}}

</body>

</html>
