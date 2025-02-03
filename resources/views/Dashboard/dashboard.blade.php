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
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Count Stats -->
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-files-o font-large-2 blue-grey"></i>
                                <h3 class="mt-2">{{ App\Models\Audit::count() }}</h3>
                                <p class="text-muted">Laporan Hasil Audit</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-folder-open font-large-2 success"></i>
                                <h3 class="mt-2">{{ App\Models\Recomended::where('status', 1)->count() }}</h3>
                                <p class="text-muted">Terbuka</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-spinner font-large-2 warning"></i>
                                <h3 class="mt-2">{{ App\Models\Recomended::where('status', 2)->count() }}</h3>
                                <p class="text-muted">Progres</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fa fa-folder font-large-2 danger"></i>
                                <h3 class="mt-2">{{ App\Models\Recomended::where('status', 3)->count() }}</h3>
                                <p class="text-muted">Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik -->
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Statistik Audit</h4>
                                    <canvas id="auditChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Distribusi Keseluruhan</h4>
                                    <canvas id="auditPieChart"></canvas>
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
            var auditData = {
                auditCount: {{ App\Models\Audit::count() }},
                terbuka: {{ App\Models\Recomended::where('status', 1)->count() }},
                progres: {{ App\Models\Recomended::where('status', 2)->count() }},
                selesai: {{ App\Models\Recomended::where('status', 3)->count() }}
            };

            var ctxBar = document.getElementById("auditChart").getContext("2d");
            var ctxPie = document.getElementById("auditPieChart").getContext("2d");

            new Chart(ctxBar, {
                type: "bar",
                data: {
                    labels: ["Total Data"],
                    datasets: [{
                            label: "Laporan Audit",
                            data: [auditData.auditCount],
                            backgroundColor: "#3498db",
                            borderColor: "#2980b9",
                            borderWidth: 1
                        },
                        {
                            label: "Terbuka",
                            data: [auditData.terbuka],
                            backgroundColor: "#2ecc71",
                            borderColor: "#27ae60",
                            borderWidth: 1
                        },
                        {
                            label: "Progres",
                            data: [auditData.progres],
                            backgroundColor: "#f1c40f",
                            borderColor: "#f39c12",
                            borderWidth: 1
                        },
                        {
                            label: "Selesai",
                            data: [auditData.selesai],
                            backgroundColor: "#e74c3c",
                            borderColor: "#c0392b",
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: "top"
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            new Chart(ctxPie, {
                type: "pie",
                data: {
                    labels: ["Terbuka", "Progres", "Selesai"],
                    datasets: [{
                        data: [auditData.terbuka, auditData.progres, auditData.selesai],
                        backgroundColor: ["#2ecc71", "#f1c40f", "#e74c3c"]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: "top"
                        }
                    }
                }
            });
        });
    </script>

    @include('Template.footer')

    @include('Template.js')

</body>

</html>
