@extends('layouts.master')

@section('title')
Operational Partner Accounts
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Operational Partner Management @endslot
@slot('title') {{ __('messages.operational_partner_account') }} @endslot
@endcomponent

<div class="container-fluid mt-4">
    <div class="table-responsive mt-4">
        <table class="table table-bordered text-center" style="width: 100%;">
            <thead>
                <tr>
                    <th>{{ __('messages.id') }}</th>
                    <th>{{ __('messages.user_name') }}</th>
                    <th>{{ __('messages.user_email') }}</th>
                    <th>{{ __('messages.phone') }}</th>
                    <th>{{ __('messages.wallet_balance') }}</th>
                    <th>{{ __('messages.last_updated') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach($operationalpartneraccount as $account)
                <tr>
                    <td>{{ $account->partner_account_id }}</td>
                    <td>{{ $account->user_name }}</td>
                    <td>{{ $account->user_email }}</td>
                    <td>{{ $account->user_phone_number }}</td>
                    <td>${{ number_format($account->wallet_balance, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($account->last_updated)->format('d M, Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.view_operational_partner_account_detail', ['id' => $account->partner_account_id]) }}"

                            class="btn btn-primary btn-sm">{{ __('messages.view_detail') }}</a>
                </tr>
                @endforeach
                @if($operationalpartneraccount->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">No operational partner accounts found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection