@extends('layouts.master')

@section('title')
{{ __('messages.pool_detail') }}
@endsection

@section('css')
<link href="{{ URL::asset('/assets/libs/ion-rangeslider/ion-rangeslider.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .pool-stats {
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .pool-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-value {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 0;
    }

    .stat-label {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 0;
    }

    .info-item {
        margin-bottom: 15px;
    }

    .info-label {
        font-weight: bold;
    }

    .badge-lg {
        font-size: 14px;
        padding: 8px 12px;
    }

    .fund-progress {
        height: 10px;
        border-radius: 5px;
    }

    .detail-card {
        height: 100%;
    }

    .terms-card {
        max-height: 250px;
        overflow-y: auto;
    }

    .fund-card {
        border-left: 4px solid #3b82f6;
    }

    .tooltip-icon {
        cursor: help;
        color: #6c757d;
    }
</style>
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') {{ __('messages.investments') }} @endslot
@slot('title') {{ __('messages.pool_detail') }} @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- Pool Header -->
                    <div class="col-12 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="mb-0">{{ $pool->pool_name }}</h2>
                                <p class="text-muted mb-0">{{ __('messages.pool_id') }}: #{{ $pool->pool_id }}</p>
                            </div>

                            <div class="d-flex align-items-center">
                                <span class="badge badge-lg bg-{{ $pool->status === 'Active' ? 'success' : ($pool->status === 'Inactive' ? 'warning' : 'danger') }} me-3">
                                    {{ $pool->status }}
                                </span>

                                @if(Auth::user() && Auth::user()->user_type == 1 && $pool->status === 'Active')
                                <!-- Invest button for investors when pool is active -->
                                <button type="button" class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#investModal">
                                    <i class="bx bx-money me-1"></i> {{ __('messages.invest') }}
                                </button>
                                @endif

                                @if(Auth::user() && Auth::user()->user_type == 5) <!-- Admin -->
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editPoolModal">
                                    <i class="bx bx-edit me-1"></i> {{ __('messages.edit_pool') }}
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Fund Statistics -->
                    <div class="col-12">
                        <div class="card fund-card">
                            <div class="card-body">
                                <h5 class="card-title">{{ __('messages.fund_progress') }}</h5>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ number_format($poolfund->total_pool_fund, 2) }} {{ $pool->currency }}</span>
                                        <span>{{ number_format($pool->target_fund, 2) }} {{ $pool->currency }}</span>
                                    </div>
                                    <div class="progress fund-progress">
                                        @php
                                        $percentage = $pool->target_fund > 0 ? min(100, ($poolfund->total_pool_fund / $pool->target_fund) * 100) : 0;
                                        @endphp
                                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;"
                                            aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-4">
                                        <div class="pool-stats bg-light">
                                            <h5 class="card-title">{{ __('messages.total_fund') }}</h5>
                                            <p class="stat-value text-primary">{{ number_format($poolfund->total_pool_fund, 2) }} {{ $pool->currency }}</p>
                                            <p class="stat-label">{{ __('messages.last_updated') }}: {{ \Carbon\Carbon::parse($poolfund->last_updated)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="pool-stats bg-light">
                                            <h5 class="card-title">{{ __('messages.available_fund') }}</h5>
                                            <p class="stat-value text-success">{{ number_format($poolfund->available_fund, 2) }} {{ $pool->currency }}</p>
                                            <p class="stat-label">{{ __('messages.for_investments') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="pool-stats bg-light">
                                            <h5 class="card-title">{{ __('messages.operating_fund') }}</h5>
                                            <p class="stat-value text-warning">{{ number_format($poolfund->operating_fund, 2) }} {{ $pool->currency }}</p>
                                            <p class="stat-label">{{ __('messages.for_operations') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pool Details & Information -->
                    <div class="col-12 mt-4">
                        <div class="row">
                            <!-- Pool Details -->
                            <div class="col-md-4">
                                <div class="card detail-card">
                                    <div class="card-body p-4">
                                        <div class="mb-4">
                                            <h5 class="card-title mb-1 d-flex align-items-center">
                                                <i class="bx bx-info-circle text-primary me-2"></i>
                                                {{ __('messages.pool_details') }}
                                            </h5>
                                        </div>

                                        <div class="row bg-light g-3 p-2 rounded-3" style="max-height: 350px; overflow-y: auto;">
                                            <div class="col-12">
                                                <div class="info-item">
                                                    <div class="info-label d-flex align-items-center">
                                                        <i class="bx bx-user text-primary me-2"></i>
                                                        {{ __('messages.manager') }}:<span class="text-muted ms-1">{{ $manager->full_name ?? 'Not assigned' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="info-item">
                                                    <div class="info-label d-flex align-items-center">
                                                        <i class="bx bx-calendar text-success me-2"></i>
                                                        {{ __('messages.creation_date') }}:<span class="text-muted ms-1">{{ \Carbon\Carbon::parse($pool->creation_date)->format('M d, Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="info-item">
                                                    <div class="info-label d-flex align-items-center">
                                                        <i class="bx bx-pie-chart-alt text-warning me-2"></i>
                                                        {{ __('messages.profit_sharing_model') }}:<span class="text-muted ms-1">{{ $pool->profit_sharing_model ?: 'Not specified' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="info-item">
                                                    <div class="info-label d-flex align-items-center mb-2">
                                                        <i class="bx bx-detail text-info me-2"></i>
                                                        {{ __('messages.description') }}
                                                    </div>
                                                    <div class="text-muted small lh-base">
                                                        {{ $pool->description ?: 'No description available.' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="col-md-8">
                                <div class="card detail-card">
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <h5 class="card-title mb-1 d-flex align-items-center">
                                                <i class="bx bx-file-blank text-warning me-2"></i>
                                                {{ __('messages.terms_and_conditions') }}
                                            </h5>
                                        </div>

                                        <div class="terms-card bg-light rounded-3 p-3" style="max-height: 350px; overflow-y: auto;">
                                            @if($pool->terms_and_conditions)
                                            <div class="terms-content">
                                                {!! nl2br(e($pool->terms_and_conditions)) !!}
                                            </div>
                                            @else
                                            <div class="text-center py-4">
                                                <i class="bx bx-file-blank text-muted mb-2" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2 mb-0">No terms and conditions specified.</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Combined Card for Total Machines Asset, Water Sale, and Bottle Sale -->
        <div class="row">
            <div class="col-md-6 col-xl-4 mb-3">
                <div class="card shadow-sm border-0" style="height:430px">
                    <div class="card-body">
                        <h4 class="card-title mb-4" style="font-size: 1.2rem;">{{ __('messages.machine_overview') }}</h4>

                        <!-- Total Machines Asset -->
                         
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <h4 class="mb-2 mt-4">{{ number_format($machineStats->total_machine_asset)  }}</span></h4>
                                <p class="text-muted mb-0">{{ __('messages.total_machine_asset') }}</p>
                            </div>
                            <div class="text-warning">
                                <i class="uil-cog" style="font-size: 40px;"></i>
                            </div>
                        </div>

                        <!-- Total Water Sale -->
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <h4 class="mb-2 mt-4">$<span data-plugin="counterup">{{ number_format($machineStats->total_water_sale) }}</span></h4>
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
        <div class="row">
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
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->

        <!-- Graph Average Monthly Sale -->
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

        <!-- Latest Transaction Water Sale -->
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
                                        <td><a href="javascript: void(0);" class="text-body fw-bold">#MB2540</a></td>
                                        <td>Neal Matthews</td>
                                        <td>07 Oct, 2019</td>
                                        <td>$400</td>
                                        <td><span class="badge rounded-pill bg-success-subtle text-success font-size-13">Paid</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investors Section -->
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="card-title mb-4">{{ __('messages.pool_investors') }}</h4>

                @if(count($poolInvestments ?? []) > 0)
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('messages.investor') }}</th>
                                <th>{{ __('messages.investment_amount') }}</th>
                                <th>{{ __('messages.investment_date') }}</th>
                                <th>{{ __('messages.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($poolInvestments as $investment)
                            <tr>
                                <td>{{ $investment->investor_name }}</td>
                                <td>{{ number_format($investment->investment_amount, 2) }} {{ $pool->currency }}</td>
                                <td>{{ \Carbon\Carbon::parse($investment->investment_date)->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $investment->status === 'Active' ? 'success' : ($investment->status === 'Withdrawn' ? 'danger' : 'warning') }}">
                                        {{ $investment->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <p>{{ __('messages.no_investors_yet') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Investment Modal -->
@if(Auth::user() && Auth::user()->user_type == 1)
<div class="modal fade" id="investModal" tabindex="-1" aria-labelledby="investModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="investModalLabel">{{ __('messages.invest_in_pool') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('invest_pool', $pool->pool_id) }}" method="POST" id="investmentForm">
                @csrf
                <input type="hidden" name="pool_id" value="{{ $pool->pool_id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="investment_amount" class="form-label fw-bold">{{ __('messages.investment_amount') }} ({{ $pool->currency }})</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ $pool->currency }}</span>
                            <input type="number" class="form-control" id="investment_amount" name="investment_amount"
                                min="1" step="0.01" required placeholder="Enter amount" value="{{ old('investment_amount') }}">
                        </div>
                        <div class="form-text">
                            {{ __('messages.min_investment') }}: {{ $pool->currency }} 1.00
                        </div>
                        @error('investment_amount')
                        <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-money me-1"></i>
                        {{ __('messages.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Edit Pool Modal -->
@if(Auth::user() && Auth::user()->user_type == 5)
<div class="modal fade" id="editPoolModal" tabindex="-1" aria-labelledby="editPoolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPoolModalLabel">{{ __('messages.edit_pool') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.update_pool', $pool->pool_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pool_name" class="form-label">{{ __('messages.pool_name') }}</label>
                                <input type="text" class="form-control" name="pool_name" required maxlength="255" value="{{ $pool->pool_name }}">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">{{ __('messages.description') }}</label>
                                <textarea class="form-control" name="description" rows="3">{{ $pool->description }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="manager_user_id" class="form-label">{{ __('messages.manager') }}</label>
                                <select class="form-control" name="manager_user_id" required>
                                    @foreach($managers ?? [] as $manager)
                                    <option value="{{ $manager->user_id }}" {{ $pool->manager_user_id == $manager->user_id ? 'selected' : '' }}>
                                        {{ $manager->full_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="target_fund" class="form-label">{{ __('messages.target_fund') }}</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="target_fund" required value="{{ $pool->target_fund }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">{{ __('messages.currency') }}</label>
                                <select class="form-control" name="currency" required>
                                    <option value="USD" {{ $pool->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ $pool->currency == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="GBP" {{ $pool->currency == 'GBP' ? 'selected' : '' }}>GBP</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="profit_sharing_model" class="form-label">{{ __('messages.profit_sharing_model') }}</label>
                                <input type="text" class="form-control" name="profit_sharing_model" maxlength="255" value="{{ $pool->profit_sharing_model }}">
                            </div>

                            <div class="mb-3">
                                <label for="terms_and_conditions" class="form-label">{{ __('messages.terms_and_conditions') }}</label>
                                <textarea class="form-control" name="terms_and_conditions" rows="3">{{ $pool->terms_and_conditions }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('messages.status') }}</label>
                                <select class="form-control" name="status" required>
                                    <option value="Active" {{ $pool->status == 'Active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                    <option value="Inactive" {{ $pool->status == 'Inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                                    <option value="Closed" {{ $pool->status == 'Closed' ? 'selected' : '' }}>{{ __('messages.closed') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deletePoolModal">
                            <i class="bx bx-trash me-1"></i> {{ __('messages.delete_pool') }}
                        </button>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">{{ __('messages.update_pool') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Delete Pool Modal -->
@if(Auth::user() && Auth::user()->user_type == 5)
<div class="modal fade" id="deletePoolModal" tabindex="-1" aria-labelledby="deletePoolModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deletePoolModalLabel">{{ __('messages.confirm_delete') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">{{ __('messages.delete_pool_confirm') }}</p>
                <p class="fw-bold mt-2">Pool: {{ $pool->pool_name }}</p>

                <div class="alert alert-warning mt-3">
                    <div class="d-flex">
                        <i class="bx bx-error-circle fs-4 me-2"></i>
                        <div>
                            <p class="mb-1 fw-bold">{{ __('messages.warning') }}</p>
                            <p class="mb-0">{{ __('messages.delete_pool_warning') }}</p>
                        </div>
                    </div>
                </div>

                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="confirmDelete" required>
                    <label class="form-check-label" for="confirmDelete">
                        {{ __('messages.i_understand_delete_consequences') }}
                    </label>
                    <div class="invalid-feedback">
                        {{ __('messages.must_confirm_delete') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form action="{{ route('admin.delete_pool', $pool->pool_id) }}" method="POST" class="d-inline delete-pool-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="deletePoolBtn" disabled>
                        {{ __('messages.delete_pool') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('script')
<!-- Combined script section -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>

<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Handle modal transitions (when opening delete modal from edit modal)
        $('#deletePoolModal').on('hidden.bs.modal', function() {
            if (!$('.modal:visible').length) {
                $('#editPoolModal').modal('show');
            }
            // Reset checkbox when modal is closed
            $('#confirmDelete').prop('checked', false);
            $('#deletePoolBtn').prop('disabled', true);
        });

        // Enable delete button only when checkbox is checked
        $('#confirmDelete').on('change', function() {
            $('#deletePoolBtn').prop('disabled', !$(this).is(':checked'));
        });

        // Add final confirmation dialog
        $('.delete-pool-form').on('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: "{{ __('messages.are_you_sure') }}",
                text: "{{ __('messages.delete_pool_final_confirmation') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: "{{ __('messages.yes_delete_it') }}",
                cancelButtonText: "{{ __('messages.cancel') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form
                    $(this).unbind('submit').submit();
                }
            });
        });

        // Investment form validation
        $('#investModal form').on('submit', function(e) {
            e.preventDefault();

            const amount = parseFloat($('#investment_amount').val());

            if (!amount || amount <= 0) {
                Swal.fire({
                    title: "{{ __('messages.invalid_amount') }}",
                    text: "{{ __('messages.enter_valid_amount') }}",
                    icon: 'error'
                });
                return;
            }

            Swal.fire({
                title: "{{ __('messages.confirm_investment') }}",
                text: "{{ __('messages.confirm_invest_amount') }} " + amount + " {{ $pool->currency }}?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                cancelButtonText: "{{ __('messages.cancel') }}",
                confirmButtonText: "{{ __('messages.yes_invest') }}",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form
                    $(this).unbind('submit').submit();
                }
            });
        });
    });

    // ApexCharts for sales data
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