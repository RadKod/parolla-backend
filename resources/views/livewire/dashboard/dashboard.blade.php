<div>
    @push('styles')
        <style>
            .analytics_box:last-child {
                width: 100%;
                max-width: 100%;
                flex: 0 0 100%;
            }
            .count{
                text-align: right;
            }
            .card-header {
                background-color: rgb(255 255 255 / 3%);
                border-bottom: 1px solid rgba(0,0,0,.125);
                padding: 1rem;
            }
            .dashboard-title {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 0;
            }
            .stat-card {
                transition: all 0.3s ease;
                border-radius: 0.5rem;
                overflow: hidden;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            }
            .stat-card .card-body {
                padding: 1.25rem;
            }
            .stat-card .icon {
                font-size: 2rem;
                margin-bottom: 0.5rem;
            }
            .stat-card .stat-label {
                font-size: 1rem;
                font-weight: 500;
                margin-bottom: 0.25rem;
            }
            .stat-card .stat-value {
                font-size: 1.75rem;
                font-weight: 700;
                margin-bottom: 0;
            }
            .user-table {
                width: 100%;
            }
            .user-table th {
                background-color: rgba(0,0,0,.03);
                padding: 0.75rem;
                border-bottom: 1px solid rgba(0,0,0,.125);
                font-weight: 600;
                text-align: left;
            }
            .user-table td {
                padding: 0.75rem;
                border-bottom: 1px solid rgba(0,0,0,.05);
            }
            .user-table tr:last-child td {
                border-bottom: none;
            }
            .user-table tr:hover {
                background-color: rgba(0,0,0,.02);
            }
            .user-avatar {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background-color: #6c757d;
                color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 0.85rem;
            }
            .percent-badge {
                background-color: #28a745;
                color: white;
                border-radius: 1rem;
                padding: 0.25rem 0.75rem;
                font-size: 0.85rem;
                font-weight: 600;
            }
            .section-title {
                font-size: 1.25rem;
                font-weight: 600;
                margin-bottom: 1rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid rgba(0,0,0,.125);
            }
            .chart-container {
                padding: 1rem;
                border-radius: 0.5rem;
                background-color: white;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            }
            .card {
                border-radius: 0.5rem;
                overflow: hidden;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                border: none;
            }
        </style>
    @endpush
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="dashboard-title">{{ __('Dashboard') }}</h5>
                    <div class="d-flex align-items-center">
                        <span class="text-muted me-2">Permanent Users:</span>
                        <span class="percent-badge">{{ $permanent_users_percent }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="col-md-12 mb-4">
            <div class="row">
                @foreach($counts as $key=>$count)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        @php
                                            $icons = [
                                                'ri-folder-open-line text-primary',
                                                'ri-lock-line text-info',
                                                'ri-file-list-3-line text-success',
                                                'ri-user-line text-warning',
                                                'ri-eye-line text-danger',
                                                'ri-user-star-line text-success'
                                            ];
                                        @endphp
                                        <i class="{{ $icons[$key] }} icon"></i>
                                        <h6 class="stat-label">{{ $labels[$key] }}</h6>
                                        <h3 class="stat-value">{{ number_format($count) }}</h3>
                                    </div>
                                    <div class="col-4 text-end">
                                        @if($key === 5) <!-- Permanent Users -->
                                            <div class="d-flex justify-content-end align-items-center">
                                                <div class="text-success fs-5">{{ $permanent_users_percent }}%</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Charts -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Statistics Overview') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div id="chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ __('Recent Permanent Users') }}</h6>
                </div>
                <div class="card-body">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th width="40"></th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permanent_users as $user)
                                <tr>
                                    <td>
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->username ?? $user->firstname ?? 'U', 0, 1)) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $user->username ?? 'N/A' }}</div>
                                        <div class="small text-muted">{{ $user->firstname }} {{ $user->lastname }}</div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">No permanent users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ __('User Growth - Last 30 Days') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div id="chart_users"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ __('Permanent User Growth - Last 30 Days') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <div id="chart_permanent_users"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('ex_scripts')
    <script>
        colors = ['#2E93fA', '#66DA26', '#546E7A', '#E91E63', '#FF9800', '#28a745'];

        var options = {
            series: @json($counts),
            chart: {
                height: 390,
                type: 'radialBar',
            },
            plotOptions: {
                radialBar: {
                    offsetY: 0,
                    offsetX: 0,
                    startAngle: 0,
                    endAngle: 250,
                    hollow: {
                        margin: 5,
                        size: '40%',
                        background: 'transparent',
                        image: undefined,
                    },
                    dataLabels: {
                        name: {
                            show: false,
                        },
                        value: {
                            show: false,
                        }
                    }
                }
            },
            colors: colors,
            labels: @json($labels),
            legend: {
                show: true,
                floating: false,
                fontSize: '12px',
                position: 'left',
                offsetX: 0,
                offsetY: 0,
                labels: {
                    useSeriesColors: false,
                },
                markers: {
                    size: 0
                },
                formatter: function(seriesName, opts) {
                    return seriesName + ":  " + opts.w.globals.series[opts.seriesIndex]
                },
                itemMargin: {
                    vertical: 3
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        show: false
                    }
                }
            }]
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

        var options2 = {
            series: [{
                name: "Total Users",
                data: [@foreach($last_30_days_user_counts as $last_30_days_user_count){{ $last_30_days_user_count->count }},@endforeach]
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                }
            },
            xaxis: {
                type: 'string',
                categories: [@foreach($last_30_days_user_counts as $last_30_days_user_count)"{{ $last_30_days_user_count->date }}",@endforeach]
            },
            colors: ['#FF9800'],
            tooltip: {
                x: {
                    format: 'M-d-y'
                },
            },
        };
        var chart2 = new ApexCharts(document.querySelector("#chart_users"), options2);
        chart2.render();
        
        // Permanent Users Chart
        var options3 = {
            series: [{
                name: "Permanent Users",
                data: [@foreach($last_30_days_permanent_users as $data){{ $data->count }},@endforeach]
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                }
            },
            xaxis: {
                type: 'string',
                categories: [@foreach($last_30_days_permanent_users as $data)"{{ $data->date }}",@endforeach]
            },
            colors: ['#28a745'],
            tooltip: {
                x: {
                    format: 'M-d-y'
                },
            },
        };
        var chart3 = new ApexCharts(document.querySelector("#chart_permanent_users"), options3);
        chart3.render();
    </script>
@endpush
