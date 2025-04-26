@extends('layouts.master')

@section('title')
Profit Distribution
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Profit Management @endslot
@slot('title') {{ __('messages.profit_distribution') }} @endslot
@endcomponent

<div class="container-fluid mt-4">
    <div class="table-responsive mt-4">
        <table class="table table-bordered text-center" style="width: 100%;">
            <thead>
                <tr>
                    <th>{{ __('messages.id') }}</th>
                    <th>{{ __('messages.sale_id') }}</th>
                    <th>{{ __('messages.machine_id') }}</th>
                    <th>{{ __('messages.machine_location') }}</th>
                    <th>{{ __('messages.sale_date') }}</th>
                    <th>{{ __('messages.investor_profit') }}</th>
                    <th>{{ __('messages.operator_profit') }}</th>
                    <th>{{ __('messages.distribution_date') }}</th>
                </tr>
                </tr>
            </thead>
            <tbody>
                @foreach($profitDistribution as $distribution)
                <tr>
                    <td>{{ $distribution->distribution_id }}</td>
                    <td>{{ $distribution->sale_id }}</td>
                    <td>{{$distribution->machine_id}}</td>
                    <td>{{ $distribution->machine_location }}</td>
                    <td>{{ \Carbon\Carbon::parse($distribution->sale_date_time)->format('d M, Y H:i') }}</td>
                    <td>${{ number_format($distribution->investor_profit, 2) }}</td>
                    <td>${{ number_format($distribution->operator_profit, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($distribution->distribution_date_time)->format('d M, Y H:i') }}</td>
                </tr>
                @endforeach
                @if($profitDistribution->isEmpty())
                <tr>
                    <td colspan="7" class="text-center">No profit distributions found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection