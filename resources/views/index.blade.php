@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')

<div class="row">
    <p style="font-size:x-large; font-weight:bold">Dashboard</p>

    <!-- Total Pool Fund Card -->
    <div class="col-md-6 col-xl-4 mb-3">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="uil-money-bill me-1 text-success" style="font-size: 32px"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1">$<span data-plugin="counterup">34,158</span></h4>
                    <p class="text-muted mb-0">Total Pool Fund</p>
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
                    <h4 class="mb-1 mt-1">$<span data-plugin="counterup">34,158</span></h4>
                    <p class="text-muted mb-0">Tool Available Fund</p>
                </div>
            </div>
        </div>
    </div> <!-- end col -->

    <!-- Total Water Sale Card -->
    <div class="col-md-6 col-xl-4 mb-3">
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="uil-wallet me-1 text-success" style="font-size: 32px"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1">$<span data-plugin="counterup">34,158</span></h4>
                    <p class="text-muted mb-0">Total Operating Fund</p>
                </div>
            </div>
        </div>
    </div> <!-- end col -->

    <!-- Combined Card for Total Machines Asset, Water Sale, and Bottle Sale -->
    <div class="row">
        <div class="col-md-6 col-xl-4 mb-3">
            <div class="card shadow-sm border-0" style="height:430px">
                <div class="card-body">
                    <h4 class="card-title mb-4" style="font-size: 1.2rem;">Machine Overview</h4>

                    <!-- Total Machines Asset -->
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h4 class="mb-2 mt-4">$<span data-plugin="counterup">12,345</span></h4>
                            <p class="text-muted mb-0">Total Machines Asset</p>
                        </div>
                        <div class="text-warning">
                            <i class="uil-cog" style="font-size: 40px;"></i>
                        </div>
                    </div>

                    <!-- Total Water Sale -->
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h4 class="mb-2 mt-4">$<span data-plugin="counterup">12,345</span></h4>
                            <p class="text-muted mb-0">Total Water Sale</p>
                        </div>
                        <div class="text-primary">
                            <i class="uil-water-glass" style="font-size: 40px;"></i>
                        </div>
                    </div>

                    <!-- Total Bottle Sale -->
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h4 class="mb-2 mt-4">$<span data-plugin="counterup">12,345</span></h4>
                            <p class="text-muted mb-0">Total Bottle Sale</p>
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
                    <h4 class="card-title mb-4" style="font-size: 1.2rem;">Key Financial Metric</h4>
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
                        <h4 class="card-title" style="font-size:1.2rem;">List Daily Distribution</h4>
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
                        <h4 class="card-title" style="font-size:1.2rem;">List Maintenance Activity</h4>
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
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="float-end">
                            <div class="dropdown">
                                <a class="dropdown-toggle text-reset" href="#" id="dropdownMenuButton5" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="fw-semibold">Sort By:</span> <span class="text-muted">Yearly<i class="mdi mdi-chevron-down ms-1"></i></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton5">
                                    <a class="dropdown-item" href="#">Monthly</a>
                                    <a class="dropdown-item" href="#">Yearly</a>
                                    <a class="dropdown-item" href="#">Weekly</a>
                                </div>
                            </div>
                        </div>
                        <h4 class="card-title mb-4">Graph Daily Average sale per Machine</h4>
                        <div class="mt-1">
                            <ul class="list-inline main-chart mb-0">
                                <li class="list-inline-item chart-border-left me-0 border-0">
                                    <h3 class="text-primary">$<span data-plugin="counterup">2,371</span><span class="text-muted d-inline-block font-size-15 ms-3">Total Pool Fund</span></h3>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <div id="sales-analytics-chart" data-colors='["--bs-primary", "#dfe2e6", "--bs-warning"]' class="apex-charts" dir="ltr"></div>
                        </div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div> <!-- end row-->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Latest Transaction Water Sale</h4>
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 20px;">
                                            <div class="form-check font-size-16">
                                                <input type="checkbox" class="form-check-input" id="customCheck1">
                                                <label class="form-check-label" for="customCheck1">&nbsp;</label>
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Billing Name</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Payment Status</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-check font-size-16">
                                                <input type="checkbox" class="form-check-input" id="customCheck2">
                                                <label class="form-check-label" for="customCheck2">&nbsp;</label>
                                            </div>
                                        </td>
                                        <td><a href="javascript: void(0);" class="text-body fw-bold">#MB2540</a> </td>
                                        <td>Neal Matthews</td>
                                        <td>07 Oct, 2019</td>
                                        <td>$400</td>
                                        <td><span   class="badge rounded-pill bg-success-subtle text-success font-size-13">Paid</span></td>
                                        
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
        @endsection