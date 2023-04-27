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
            .card-header
            {
                background-color: rgb(255 255 255 / 3%);
            }
        </style>
    @endpush
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                @foreach($counts as $key=>$count)
                    <div class="col-lg-6 col-md-6 mb-2 analytics_box">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-7">
                                        <i class="ri-emotion-line fs-6 text-info"></i>
                                        <p class="fs-4 mb-1">{{ $labels[$key] }}</p>
                                    </div>
                                    <div class="col-5 count">
                                        <h3 class="font-weight-bold text-end mb-0">{{ $count }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="w-full" style="height: 50%;">
                        <div class="px-10" id="chart"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="w-full" style="height: 50%;">
                        <label class="label label-success">Last 30 Days Users</label>
                        <div class="px-10" id="chart_users"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('ex_scripts')
    <script>
        colors = ['#2E93fA', '#66DA26', '#546E7A', '#E91E63', '#FF9800'];

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
                name: "Last 30 Days Users",
                data: [@foreach($last_30_days_user_counts as $last_30_days_user_count){{ $last_30_days_user_count->count }},@endforeach]
            }],
            chart: {
                height: 400,
                type: 'area'
            },
            dataLabels: {
                enabled: true
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'string',
                categories: [@foreach($last_30_days_user_counts as $last_30_days_user_count)"{{ $last_30_days_user_count->date }}",@endforeach]
            },
            colors: [colors[3]],
            tooltip: {
                x: {
                    format: 'M-d-y'
                },
            },
        };
        var chart2 = new ApexCharts(document.querySelector("#chart_users"), options2);
        chart2.render();

    </script>
@endpush
