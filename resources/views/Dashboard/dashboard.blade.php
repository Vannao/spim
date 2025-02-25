<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    @include('Template.head')
    <title>SPI Navigator - Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar" data-open="click"
    data-menu="vertical-menu" data-col="2-columns">
    @include('Template.nav')
    @include('Template.side-menu')

    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filterTahun">Filter Tahun:</label>
                        <select id="filterTahun" class="form-control">
                            <option value="">Semua Tahun</option>
                            @for ($year = 2023; $year <= 2028; $year++)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                @php
                    $rekomendasiSesuai = App\Models\Recomended::where('kesesuaian', 'sesuai')->count();
                    $rekomendasiTidakSesuai = App\Models\Recomended::where('kesesuaian', 'tidak sesuai')->count();

                    $auditCountStatus1 = App\Models\Recomended::where('status', 1)->count();
                    $auditCountStatus2 = App\Models\Recomended::where('status', 2)->count();
                    $auditCountStatus3 = App\Models\Recomended::where('status', 3)->count();

                    $auditCount = App\Models\Audit::count();
                    $data = App\Models\Recomended::selectRaw('YEAR(created_at) as tahun, status, COUNT(*) as jumlah')
                        ->groupBy('tahun', 'status')
                        ->get()
                        ->groupBy('tahun');

                    // Hitung PKA dan Surat Tugas berdasarkan tahun
                    $hitungPKA = App\Models\Audit::whereNotNull('pka')->count();
                    $hitungSurat = App\Models\Audit::whereNotNull('file_surat_tugas')->count();

                    // Data kesesuaian berdasarkan tahun untuk chart baru
                    $dataKesesuaian = App\Models\Recomended::selectRaw(
                        'YEAR(created_at) as tahun, kesesuaian, COUNT(*) as jumlah',
                    )
                        ->groupBy('tahun', 'kesesuaian')
                        ->get()
                        ->groupBy('tahun');
                @endphp

                <script>
                    var auditData = {
                        auditCount: {{ $auditCount }},
                        tahunData: {!! json_encode($data) !!},
                        hitungPKA: {{ $hitungPKA }},
                        hitungSurat: {{ $hitungSurat }},
                        closedCount: {{ $auditCountStatus3 }},
                        hitungPKAByYear: {!! json_encode(
                            App\Models\Audit::whereNotNull('pka')->selectRaw('YEAR(created_at) as tahun, COUNT(*) as jumlah')->groupBy('tahun')->get()->keyBy('tahun'),
                        ) !!},
                        hitungSuratByYear: {!! json_encode(
                            App\Models\Audit::whereNotNull('file_surat_tugas')->selectRaw('YEAR(created_at) as tahun, COUNT(*) as jumlah')->groupBy('tahun')->get()->keyBy('tahun'),
                        ) !!},
                        closedByYear: {!! json_encode(
                            App\Models\Recomended::where('status', 3)->selectRaw('YEAR(created_at) as tahun, COUNT(*) as jumlah')->groupBy('tahun')->get()->keyBy('tahun'),
                        ) !!},
                        kesesuaianData: {!! json_encode($dataKesesuaian) !!},
                        rekomendasiSesuai: {{ $rekomendasiSesuai }},
                        rekomendasiTidakSesuai: {{ $rekomendasiTidakSesuai }},
                        rekomendasiSesuaiByYear: {!! json_encode(
                            App\Models\Recomended::where('kesesuaian', 'sesuai')->selectRaw('YEAR(created_at) as tahun, COUNT(*) as jumlah')->groupBy('tahun')->get()->keyBy('tahun'),
                        ) !!},
                        rekomendasiTidakSesuaiByYear: {!! json_encode(
                            App\Models\Recomended::where('kesesuaian', 'tidak sesuai')->selectRaw('YEAR(created_at) as tahun, COUNT(*) as jumlah')->groupBy('tahun')->get()->keyBy('tahun'),
                        ) !!}
                    };
                </script>

                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-1">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-files-o font-large-2 blue-grey"></i>
                                <h3 class="mt-2" id="auditCount">{{ $auditCount }}</h3>
                                <p class="text-muted">Laporan Hasil Audit Keseluruhan</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-1">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-clipboard font-large-2 blue"></i>
                                <h3 class="mt-2" id="hitungPKA">{{ $hitungPKA }}</h3>
                                <p class="text-muted">PKPT</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-1">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-envelope font-large-2 green"></i>
                                <h3 class="mt-2" id="hitungSurat">{{ $hitungSurat }}</h3>
                                <p class="text-muted">Surat Tugas</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-1">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-folder-open font-large-2 success"></i>
                                <h3 class="mt-2" id="terbukaCount">{{ $auditCountStatus1 }}</h3>
                                <p class="text-muted">Terbuka (Rekomendasi)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-1">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-spinner font-large-2 warning"></i>
                                <h3 class="mt-2" id="progressCount">{{ $auditCountStatus2 }}</h3>
                                <p class="text-muted">Progress (Rekomendasi)</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-1">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-folder font-large-2 danger"></i>
                                <h3 class="mt-2" id="closedCount">{{ $auditCountStatus3 }}</h3>
                                <p class="text-muted">Closed (Rekomendasi)</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-1">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-check-circle font-large-2 success"></i>
                                <h3 class="mt-2" id="sesuaiCount">{{ $rekomendasiSesuai }}</h3>
                                <p class="text-muted">Rekomendasi Sesuai</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-1">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-times-circle font-large-2 danger"></i>
                                <h3 class="mt-2" id="tidakSesuaiCount">{{ $rekomendasiTidakSesuai }}</h3>
                                <p class="text-muted">Rekomendasi Tidak Sesuai</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">PKPT vs Surat Tugas vs Closed</h4>
                                    <canvas id="auditBarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Distribusi Status Audit</h4>
                                    <canvas id="auditPieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Kesesuaian Rekomendasi</h4>
                                    <canvas id="kesesuaianPieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Helper function to calculate percentages
            function calculatePercentages(values) {
                const total = values.reduce((sum, value) => sum + value, 0);
                return values.map(value => ((value / total) * 100).toFixed(1));
            }

            function updateData(tahun) {
                // Update status rekomendasi (Terbuka, Progress, Closed)
                let terbuka = 0,
                    progress = 0,
                    closed = 0;

                // Update kesesuaian rekomendasi
                let sesuai = 0,
                    tidakSesuai = 0;

                if (tahun === "") {
                    // Jika semua tahun dipilih
                    for (let key in auditData.tahunData) {
                        auditData.tahunData[key].forEach(item => {
                            if (item.status === 1) terbuka += item.jumlah;
                            if (item.status === 2) progress += item.jumlah;
                            if (item.status === 3) closed += item.jumlah;
                        });
                    }

                    sesuai = auditData.rekomendasiSesuai;
                    tidakSesuai = auditData.rekomendasiTidakSesuai;
                } else {
                    // Jika tahun tertentu dipilih
                    if (auditData.tahunData[tahun]) {
                        auditData.tahunData[tahun].forEach(item => {
                            if (item.status === 1) terbuka += item.jumlah;
                            if (item.status === 2) progress += item.jumlah;
                            if (item.status === 3) closed += item.jumlah;
                        });
                    }

                    // Data kesesuaian berdasarkan tahun tertentu
                    sesuai = auditData.rekomendasiSesuaiByYear[tahun] ? auditData.rekomendasiSesuaiByYear[tahun]
                        .jumlah : 0;
                    tidakSesuai = auditData.rekomendasiTidakSesuaiByYear[tahun] ? auditData
                        .rekomendasiTidakSesuaiByYear[tahun].jumlah : 0;
                }

                // Update tampilan status rekomendasi
                document.getElementById("terbukaCount").innerText = terbuka;
                document.getElementById("progressCount").innerText = progress;
                document.getElementById("closedCount").innerText = closed;

                // Update tampilan kesesuaian rekomendasi
                document.getElementById("sesuaiCount").innerText = sesuai;
                document.getElementById("tidakSesuaiCount").innerText = tidakSesuai;

                // Update chart status rekomendasi
                auditStatusPieChart.data.datasets[0].data = [terbuka, progress, closed];
                // Update percentages for status pie chart
                const statusPercentages = calculatePercentages([terbuka, progress, closed]);
                auditStatusPieChart.data.labels = [
                    `Terbuka (${statusPercentages[0]}%)`,
                    `Progress (${statusPercentages[1]}%)`,
                    `Closed (${statusPercentages[2]}%)`
                ];
                auditStatusPieChart.update();

                // Update chart kesesuaian rekomendasi
                kesesuaianPieChart.data.datasets[0].data = [sesuai, tidakSesuai];
                // Update percentages for kesesuaian pie chart
                const kesesuaianPercentages = calculatePercentages([sesuai, tidakSesuai]);
                kesesuaianPieChart.data.labels = [
                    `Sesuai (${kesesuaianPercentages[0]}%)`,
                    `Tidak Sesuai (${kesesuaianPercentages[1]}%)`
                ];
                kesesuaianPieChart.update();

                // Update hitungPKA, hitungSurat, dan closed berdasarkan tahun
                let hitungPKA = 0;
                let hitungSurat = 0;
                let closedCount = 0;

                if (tahun === "") {
                    hitungPKA = auditData.hitungPKA;
                    hitungSurat = auditData.hitungSurat;
                    closedCount = auditData.closedCount;
                } else {
                    hitungPKA = auditData.hitungPKAByYear[tahun] ? auditData.hitungPKAByYear[tahun].jumlah : 0;
                    hitungSurat = auditData.hitungSuratByYear[tahun] ? auditData.hitungSuratByYear[tahun].jumlah :
                        0;
                    closedCount = auditData.closedByYear[tahun] ? auditData.closedByYear[tahun].jumlah : 0;
                }

                document.getElementById("hitungPKA").innerText = hitungPKA;
                document.getElementById("hitungSurat").innerText = hitungSurat;

                // Update PKPT vs Surat Tugas vs Closed chart
                auditDataPieChart.data.datasets[0].data = [hitungPKA, hitungSurat, closedCount];
                // Update percentages for audit data pie chart
                const auditDataPercentages = calculatePercentages([hitungPKA, hitungSurat, closedCount]);
                auditDataPieChart.data.labels = [
                    `PKPT (${auditDataPercentages[0]}%)`,
                    `Surat Tugas (${auditDataPercentages[1]}%)`,
                    `Closed (${auditDataPercentages[2]}%)`
                ];
                auditDataPieChart.update();
            }

            // Chart untuk PKPT vs Surat Tugas vs Closed
            var ctxDataPie = document.getElementById("auditBarChart").getContext("2d");

            // Calculate initial percentages for all three charts
            const auditDataValues = [auditData.hitungPKA, auditData.hitungSurat, auditData.closedCount];
            const auditDataPercentages = calculatePercentages(auditDataValues);

            const statusValues = [
                auditData.tahunData ? Object.values(auditData.tahunData).flat()
                .filter(item => item.status === 1).reduce((sum, item) => sum + item.jumlah, 0) : 0,
                auditData.tahunData ? Object.values(auditData.tahunData).flat()
                .filter(item => item.status === 2).reduce((sum, item) => sum + item.jumlah, 0) : 0,
                auditData.tahunData ? Object.values(auditData.tahunData).flat()
                .filter(item => item.status === 3).reduce((sum, item) => sum + item.jumlah, 0) : 0
            ];
            const statusPercentages = calculatePercentages(statusValues);

            const kesesuaianValues = [auditData.rekomendasiSesuai, auditData.rekomendasiTidakSesuai];
            const kesesuaianPercentages = calculatePercentages(kesesuaianValues);

            var auditDataPieChart = new Chart(ctxDataPie, {
                type: "pie",
                data: {
                    labels: [
                        `PKPT (${auditDataPercentages[0]}%)`,
                        `Surat Tugas (${auditDataPercentages[1]}%)`,
                        `Closed (${auditDataPercentages[2]}%)`
                    ],
                    datasets: [{
                        data: auditDataValues,
                        backgroundColor: ["#9b59b6", "#1abc9c", "#f39c12"],
                        borderColor: "#ffffff",
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Data Audit (Pie Chart)'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Chart untuk Status Rekomendasi
            var ctxStatusPie = document.getElementById("auditPieChart").getContext("2d");
            var auditStatusPieChart = new Chart(ctxStatusPie, {
                type: "pie",
                data: {
                    labels: [
                        `Terbuka (${statusPercentages[0]}%)`,
                        `Progress (${statusPercentages[1]}%)`,
                        `Closed (${statusPercentages[2]}%)`
                    ],
                    datasets: [{
                        data: statusValues,
                        backgroundColor: ["#2ecc71", "#f1c40f", "#e74c3c"] // Green, yellow, red
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Chart baru untuk Kesesuaian Rekomendasi
            var ctxKesesuaianPie = document.getElementById("kesesuaianPieChart").getContext("2d");
            var kesesuaianPieChart = new Chart(ctxKesesuaianPie, {
                type: "pie",
                data: {
                    labels: [
                        `Sesuai (${kesesuaianPercentages[0]}%)`,
                        `Tidak Sesuai (${kesesuaianPercentages[1]}%)`
                    ],
                    datasets: [{
                        data: kesesuaianValues,
                        backgroundColor: ["#3498db", "#e67e22"], // Blue, orange
                        borderColor: "#ffffff",
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Event listener untuk filter tahun
            document.getElementById("filterTahun").addEventListener("change", function() {
                updateData(this.value);
            });

            // Initialize dengan semua data
            updateData("");
        });
    </script>

    @include('Template.footer')
    @include('Template.js')
</body>

</html>
