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
                    $auditCount = App\Models\Audit::count();
                    $data = App\Models\Recomended::selectRaw('YEAR(created_at) as tahun, status, COUNT(*) as jumlah')
                        ->groupBy('tahun', 'status')
                        ->get()
                        ->groupBy('tahun');
                @endphp

                <script>
                    var auditData = {
                        auditCount: {{ $auditCount }},
                        tahunData: {!! json_encode($data) !!}
                    };
                </script>

                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-files-o font-large-2 blue-grey"></i>
                                <h3 class="mt-2" id="auditCount">{{ $auditCount }}</h3>
                                <p class="text-muted">Laporan Hasil Audit Keseluruhan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-folder-open font-large-2 success"></i>
                                <h3 class="mt-2" id="terbukaCount">0</h3>
                                <p class="text-muted">Terbuka (Rekomendasi)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-spinner font-large-2 warning"></i>
                                <h3 class="mt-2" id="progresCount">0</h3>
                                <p class="text-muted">Progres (Rekomendasi)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-folder font-large-2 danger"></i>
                                <h3 class="mt-2" id="selesaiCount">0</h3>
                                <p class="text-muted">Selesai (Rekomendasi)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Distribusi Status (Polar Area)</h4>
                                    <canvas id="auditPolarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Distribusi Keseluruhan (Pie)</h4>
                                    <canvas id="auditPieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Status Audit (Doughnut)</h4>
                                    <canvas id="auditDoughnutChart"></canvas>
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
            function updateData(tahun) {
                let terbuka = 0,
                    progres = 0,
                    selesai = 0;

                if (tahun === "") {
                    for (let key in auditData.tahunData) {
                        auditData.tahunData[key].forEach(item => {
                            if (item.status === 1) terbuka += item.jumlah;
                            if (item.status === 2) progres += item.jumlah;
                            if (item.status === 3) selesai += item.jumlah;
                        });
                    }
                } else if (auditData.tahunData[tahun]) {
                    auditData.tahunData[tahun].forEach(item => {
                        if (item.status === 1) terbuka = item.jumlah;
                        if (item.status === 2) progres = item.jumlah;
                        if (item.status === 3) selesai = item.jumlah;
                    });
                }

                document.getElementById("terbukaCount").innerText = terbuka;
                document.getElementById("progresCount").innerText = progres;
                document.getElementById("selesaiCount").innerText = selesai;

                const chartData = [terbuka, progres, selesai];
                auditDoughnutChart.data.datasets[0].data = chartData;
                auditPieChart.data.datasets[0].data = chartData;
                auditPolarChart.data.datasets[0].data = chartData;


                auditDoughnutChart.update();
                auditPieChart.update();
                auditPolarChart.update();
            }

            var ctxDoughnut = document.getElementById("auditDoughnutChart").getContext("2d");
            var ctxPie = document.getElementById("auditPieChart").getContext("2d");
            var ctxPolar = document.getElementById("auditPolarChart").getContext("2d");


            const labels = ["Terbuka", "Progres", "Selesai"];
            const colors = ["#2ecc71", "#f1c40f", "#e74c3c"];

            var auditDoughnutChart = new Chart(ctxDoughnut, {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [{
                        data: [0, 0, 0],
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            var auditPieChart = new Chart(ctxPie, {
                type: "pie",
                data: {
                    labels: labels,
                    datasets: [{
                        data: [0, 0, 0],
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            var auditPolarChart = new Chart(ctxPolar, {
                type: "polarArea",
                data: {
                    labels: labels,
                    datasets: [{
                        data: [0, 0, 0],
                        backgroundColor: colors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });


            document.getElementById("filterTahun").addEventListener("change", function() {
                updateData(this.value);
            });

            updateData("");
        });
    </script>

    @include('Template.footer')
    @include('Template.js')

</body>

</html>
