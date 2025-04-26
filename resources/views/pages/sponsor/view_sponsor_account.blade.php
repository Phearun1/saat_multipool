@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')

<div class="row">
    <div class="row mb-4">
        <div class="col-md-6 ">
            <h5>{{ __('messages.sponsor_account') }}</h5>
        </div>
        <div class="col-md-6 text-end ">
            <!-- Sponsor Button -->
            <button type="button" class="btn btn-primary text-white me-3 mb-2 mb-md-3" style="width:150px;font-weight:bold" data-bs-toggle="modal" data-bs-target="#sponsorModal">
            {{ __('messages.sponsor') }}
            </button>
            <!-- Withdraw Money Button -->
            <button type="button" class="btn btn-warning text-black me-3 mb-2 mb-md-3" style="width:150px;font-weight:bold" data-bs-toggle="modal" data-bs-target="#withdrawModal">
            {{ __('messages.withdraw') }}
            </button>
        </div>
    </div>

    <!-- Total Balance -->
    <div class="col-md-6 col-xl-5 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="uil-money-bill me-1 text-primary" style="font-size: 32px"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1">$<span data-plugin="counterup">{{ number_format($sponsor->portfolio_fund ?? 0, 2) }}</span></h4>
                    <p class="text-muted mb-0">{{ __('messages.total_balance') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sponsor Modal -->
<div class="modal fade" id="sponsorModal" tabindex="-1" aria-labelledby="sponsorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sponsorModalLabel">{{ __('messages.sponsor') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sponsorForm" method="POST" action="{{ route('investment.sponsor_account.sponsor') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="sponsorAmount" class="form-label">{{ __('messages.amount') }}</label>
                        <input type="number" class="form-control" id="sponsorAmount" name="amount" placeholder="{{ __('messages.enter_amount') }}" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="sponsorForm" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="withdrawModalLabel">{{ __('messages.withdraw_money') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="withdrawForm" method="POST" action="{{ route('investment.sponsor_account.withdraw') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="withdrawAmount" class="form-label">{{ __('messages.amount') }}</label>
                        <input type="number" class="form-control" id="withdrawAmount" name="amount" placeholder="{{ __('messages.enter_amount') }}" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <button type="submit" form="withdrawForm" class="btn btn-primary">{{ __('messages.submit') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Latest Transactions -->
<div class="row">
    <div class="mt-4 d-flex justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">{{ __('messages.latest_transactions') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">{{ __('messages.id') }}</th>
                                    <th class="text-center">{{ __('messages.date') }}</th>
                                    <th class="text-center">{{ __('messages.total') }}</th>
                                    <th class="text-center">{{ __('messages.type') }}</th>
                                    <th class="text-center">{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="text-center"><a href="javascript:void(0);" class="text-body fw-bold">#{{ $transaction->id }}</a></td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M, Y H:i') }}</td>
                                    <td class="text-center">${{ number_format($transaction->total, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill 
                                            {{ $transaction->type === 'Sponsor' ? 'bg-success-subtle text-success' : '' }}
                                            {{ $transaction->type === 'Withdraw' ? 'bg-danger-subtle text-danger' : '' }}">
                                            {{ $transaction->type }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill
                                            {{ $transaction->status === 'Completed' ? 'bg-success-subtle text-success' : '' }}
                                            {{ $transaction->status === 'Pending' ? 'bg-warning-subtle text-warning' : '' }}
                                            {{ $transaction->status === 'Failed' ? 'bg-danger-subtle text-danger' : '' }}">
                                            {{ $transaction->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $transactions->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Confirmation for sponsor form submission
        document.getElementById('sponsorForm').addEventListener('submit', function (e) {
            const amount = document.getElementById('sponsorAmount').value;
            if (!confirm(`Are you sure you want to sponsor $${amount}?`)) {
                e.preventDefault();
            }
        });

        // Confirmation for withdraw form submission
        document.getElementById('withdrawForm').addEventListener('submit', function (e) {
            const amount = document.getElementById('withdrawAmount').value;
            if (!confirm(`Are you sure you want to withdraw $${amount}?`)) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
