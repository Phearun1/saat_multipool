@extends('layouts.master')

@section('title')
Operational Partner Account Detail
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Operational Partner Management @endslot
@slot('title') {{ __('messages.operational_partner_account_detail') }} @endslot
@endcomponent

<div class="container-fluid mt-4">
    @if($operationalpartneraccount)
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title">{{ __('messages.account_information') }}</h4>
                    <p><strong>{{ __('messages.id') }}:</strong> {{ $operationalpartneraccount->partner_account_id }}</p>
                    <p><strong>{{ __('messages.user_name') }}:</strong> {{ $operationalpartneraccount->user_name }}</p>
                    <p><strong>{{ __('messages.user_email') }}:</strong> {{ $operationalpartneraccount->user_email }}</p>
                    <p><strong>{{ __('messages.phone') }}:</strong> {{ $operationalpartneraccount->user_phone_number }}</p>
                    
                    
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-5 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="float-end mt-2">
                        <i class="mdi mdi-cash-check me-1 text-success" style="font-size: 32px"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 mt-1">$<span>{{ number_format($operationalpartneraccount->wallet_balance, 2) }}</span></h4>
                        <p class=" mb-2">{{ __('messages.wallet_balance') }}</p>
                        <p class="text-muted mb-0" style="color: #6c757d;">Last Updated: {{ \Carbon\Carbon::parse($operationalpartneraccount->last_updated)->format('d M, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <h4>{{ __('messages.transaction') }}</h4>
    <div class="table-responsive">
        <table class="table table-bordered text-center" style="width: 100%;">
            <thead>
                <tr>
                    <th>{{ __('messages.id') }}</th>

                    <th>{{ __('messages.machine_id') }}</th>
                    <th>{{ __('messages.transaction_type') }}</th>
                    <th>{{ __('messages.amount') }}</th>
                    <th>{{ __('messages.transaction_date') }}</th>
                    <th>{{ __('messages.remarks') }}</th>

                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_id }}</td>
                    <td>{{ $transaction->machine_id }}</td>
                    <td>{{ $transaction->transaction_type }}</td>
                    <td>${{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date_time)->format('d M, Y H:i') }}</td>
                    <td>{{ $transaction->remarks }}</td>
                </tr>
                @endforeach
                @if($transactions->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">No transactions found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @else
    <div class="alert alert-danger" role="alert">
        Operational Partner Account not found.
    </div>
    @endif
</div>
@endsection