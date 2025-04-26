@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')

<div class="row">
    <p style="font-size:x-large; font-weight:bold">{{ __('messages.dashboard') }}</p>

    <!-- Total Pool Fund Card -->
    <div class="col-md-6 col-xl-4 mb-3">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="uil-money-bill me-1 text-success" style="font-size: 32px"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1">$<span>{{ number_format($poolfunds->total_pool_fund, 2) }}</span></h4>
                    <p class="text-muted mb-0">{{ __('messages.total_pool_fund') }}</p>
                </div>
            </div>
        </div>
    </div> <!-- end col -->

    <!-- Tool Available Fund Card -->
    <div class="col-md-6 col-xl-4 mb-3">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="mdi mdi-cash-check me-1 text-success" style="font-size: 32px"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1">$<span>{{ number_format($poolfunds->available_fund, 2) }}</span></h4>
                    <p class="text-muted mb-0">{{ __('messages.total_available_fund') }}</p>
                </div>
            </div>
        </div>
    </div> <!-- end col -->

    <!-- Total Operating Fund Card -->
    <div class="col-md-6 col-xl-4 mb-3">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="uil-wallet me-1 text-success" style="font-size: 32px"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1">$<span>{{ number_format($poolfunds->operating_fund, 2) }}</span></h4>
                    <p class="text-muted mb-0">{{ __('messages.total_operating_fund') }} </p>
                </div>
            </div>
        </div>
    </div> <!-- end col -->


    <!-- Combined Card for Total Machines Asset, Water Sale, and Bottle Sale -->
    <div class="row">
        <div class="col-md-6 col-xl-4 mb-3">
            <div class="card shadow-sm border-0" style="height:430px">
                <div class="card-body">
                    <h4 class="card-title mb-4" style="font-size: 1.2rem;">{{ __('messages.machine_overview') }}</h4>

                    <!-- Total Machines Asset -->
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h4 class="mb-2 mt-4">$<span data-plugin="counterup">{{ number_format($machineStats->total_machine_asset, 2) }}</span></h4>
                            <p class="text-muted mb-0">{{ __('messages.total_machine_asset') }}</p>
                        </div>
                        <div class="text-warning">
                            <i class="uil-cog" style="font-size: 40px;"></i>
                        </div>
                    </div>

                    <!-- Total Water Sale -->
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h4 class="mb-2 mt-4">$<span data-plugin="counterup">{{ number_format($machineStats->total_water_sale, 2) }}</span></h4>
                            <p class="text-muted mb-0">{{ __('messages.total_water_sales') }}</p>
                        </div>
                        <div class="text-primary">
                            <i class="uil-water-glass" style="font-size: 40px;"></i>
                        </div>
                    </div>

                    <!-- Total Bottle Sale -->
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h4 class="mb-2 mt-4">{{ number_format($machineStats->total_bottle_sale) }}</h4>
                            <p class="text-muted mb-0">{{ __('messages.total_bottle_sales') }}</p>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-wine-bottle" style="font-size: 40px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->

        <!-- Key Financial Metric Card -->
        <div class="col-md-12 col-xl-8 mb-3">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body">
                    <h4 class="card-title mb-4" style="font-size: 1.2rem;">{{ __('messages.key_financial_metrics') }}</h4>
                    <ol class="activity-feed mb-0 ps-2" data-simplebar style="max-height: 340px;">
                        <div class="feed-item">
                            <p class="font-size-16 text-primary"><strong>Break-even Point</strong></p>
                            <p class="mb-0">3.5 Years</p>
                        </div>
                        <div class="feed-item">
                            <p class="font-size-16 text-primary"><strong>Return on Investment</strong></p>
                            <p class="mb-0">Approximately 66% based on total funds invested</p>
                        </div>
                        <div class="feed-item">
                            <p class="font-size-16 text-primary"><strong>Internal Rate of Return</strong></p>
                            <p class="mb-0">Estimated 40% or higher, indicating strong profitability</p>
                        </div>
                        <div class="feed-item">
                            <p class="font-size-16 text-primary"><strong>Net Present Value</strong></p>
                            <p class="mb-0">Positive $259,771.30 at a 10% discount rate</p>
                        </div>
                    </ol>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->

    <!-- List Daily Distribution Section -->
    <div class="row ">
        <div class="col-md-6 col-xl-6 mb-3">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="card-title" style="font-size:1.2rem;">{{ __('messages.list_daily_distribution') }}</h4>
                    </div>

                    <div class="table-responsive" data-simplebar style="max-height: 320px;">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Total Pool</th>
                                    <th>Total Revenue</th>
                                    <th>To Wallet</th>
                                    <th>To Portfolio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">02/December/2025</td>
                                    <td class="text-center">$10,000</td>
                                    <td class="text-center">$100</td>
                                    <td class="text-center">$50</td>
                                    <td class="text-center">$50</td>

                                </tr>
                                <tr>
                                    <td class="text-center">02/December/2025</td>
                                    <td class="text-center">$10,000</td>
                                    <td class="text-center">$100</td>
                                    <td class="text-center">$50</td>
                                    <td class="text-center">$50</td>

                                </tr>
                                <tr>
                                    <td class="text-center">02/December/2025</td>
                                    <td class="text-center">$10,000</td>
                                    <td class="text-center">$100</td>
                                    <td class="text-center">$50</td>
                                    <td class="text-center">$50</td>

                                </tr>
                                <tr>
                                    <td class="text-center">02/December/2025</td>
                                    <td class="text-center">$10,000</td>
                                    <td class="text-center">$100</td>
                                    <td class="text-center">$50</td>
                                    <td class="text-center">$50</td>

                                </tr>
                                <tr>
                                    <td class="text-center">02/December/2025</td>
                                    <td class="text-center">$10,000</td>
                                    <td class="text-center">$100</td>
                                    <td class="text-center">$50</td>
                                    <td class="text-center">$50</td>

                                </tr>
                                <tr>
                                    <td class="text-center">02/December/2025</td>
                                    <td class="text-center">$10,000</td>
                                    <td class="text-center">$100</td>
                                    <td class="text-center">$50</td>
                                    <td class="text-center">$50</td>

                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- List Maintenance Activity Table (Scrollable) -->
        <div class="col-md-6 col-xl-6 mb-4">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="card-title" style="font-size:1.2rem;">{{ __('messages.list_maintenance_activity') }}</h4>
                        <!-- Date Section (Top Right) -->
                        <div class="d-flex align-items-center text-end">
                            <h5 class="text-muted mb-0 me-2">Date:</h5>
                            <p class="font-weight-bold mb-0">Today, 12:20 pm</p>
                        </div>
                    </div>

                    <div class="table-responsive" data-simplebar style="max-height: 320px;">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Machine ID</th>
                                    <th>Maintenance Distribution</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Andrei Coman posted a new article: <span class="text-primary">Forget UX Rowland</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Andrei Coman posted a new article: <span class="text-primary">Forget UX Rowland</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Andrei Coman posted a new article: <span class="text-primary">Forget UX Rowland</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Andrei Coman posted a new article: <span class="text-primary">Forget UX Rowland</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Andrei Coman posted a new article: <span class="text-primary">Forget UX Rowland</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Andrei Coman posted a new article: <span class="text-primary">Forget UX Rowland</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Andrei Coman posted a new article: <span class="text-primary">Forget UX Rowland</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>Andrei Coman posted a new article: <span class="text-primary">Forget UX Rowland</span></td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
        <!-- Existing Content Below -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title mb-4" style="font-size: 1.2rem;">{{ __('messages.graph_average_monthly_sale') }}</h1>

                        <!-- Dropdown to change group type -->
                        <form method="GET" class="mb-4">
                            <label for="group_type" class="form-label">Group By:</label>
                            <select name="group_type" id="group_type" class="form-select" onchange="this.form.submit()">
                                <option value="weekly" {{ $groupType == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ $groupType == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ $groupType == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                        </form>

                        <!-- Line Graph -->
                        <div id="average-sales-chart" class="apex-charts"></div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4" style="font-size: 1.2rem;">{{ __('messages.latest_transaction_water_sale') }}</h4>
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        
                                        <th>ID</th>
                                        <th>Billing Name</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Payment Status</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        
                                        <td><a href="javascript: void(0);" class="text-body fw-bold">#MB2540</a> </td>
                                        <td>Neal Matthews</td>
                                        <td>07 Oct, 2019</td>
                                        <td>$400</td>
                                        <td><span class="badge rounded-pill bg-success-subtle text-success font-size-13">Paid</span></td>

                                    </tr>
                                    <!-- Add more rows as needed -->
                                </tbody>
                            </table>
                        </div>
                        <!-- end table-responsive -->
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->

        @endsection

        @section('script')
        <!-- apexcharts -->
        <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
        <script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>
        <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var options = {
                    series: [{
                        name: 'Average Sale',
                        data: @json($salesData->pluck('average')) // Extract average values
                    }],
                    chart: {
                        type: 'line',
                        height: 350,
                        toolbar: {
                            show: false // Remove toolbar
                        }
                    },
                    stroke: {
                        width: 3,
                        curve: 'smooth'
                    },
                    markers: {
                        size: 4
                    },
                    colors: ['#ffc107'], // Yellow line color
                    xaxis: {
                        categories: @json($salesData->pluck('label')), // Extract group labels
                        title: {
                            text: 'Time Period'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Average Sale Amount'
                        },
                        labels: {
                            formatter: function(val) {
                                return "$" + val.toFixed(2);
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return "$" + val.toFixed(2);
                            }
                        }
                    }
                };

                // Render the chart
                var chart = new ApexCharts(document.querySelector("#average-sales-chart"), options);
                chart.render();
            });
        </script>

        @endsection