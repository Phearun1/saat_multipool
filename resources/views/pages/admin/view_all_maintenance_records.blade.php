@extends('layouts.master')

@section('title')
Maintenance Records
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Maintenance Management @endslot
@slot('title') {{ __('messages.maintenance_management') }} @endslot
@endcomponent

<div class="container-fluid mt-4">
    

    <div class="table-responsive mt-4">
        <table class="table table-bordered text-center" style="width: 100%;">
            <thead>
                <tr>
                    <th>{{ __('messages.id') }}</th>
                    <th>{{ __('messages.machine_id') }}</th>
                    <th>{{ __('messages.user_name') }}</th>
                    <th>{{ __('messages.user_email') }}</th>
                    <th>{{ __('messages.maintenance_date') }}</th>
                    <th>{{ __('messages.issue_reported') }}</th>
                    <th>{{ __('messages.action_taken') }}</th>
                    <th>{{ __('messages.next_scheduled_maintenance') }}</th>
                    <th>{{ __('messages.status') }}</th>
                </tr>
                   
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr>
                    <td>{{ $record->maintenance_id }}</td>
                    <td>{{ $record->machine_id }}</td>
                    <td>{{ $record->user_name }}</td>
                    <td>{{ $record->user_email }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->maintenance_date)->format('d M, Y H:i') }}</td>
                    <td>{{ $record->issue_reported }}</td>
                    <td>{{ $record->action_taken }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->next_scheduled_maintenance)->format('d M, Y H:i') }}</td>
                    <td>{{ $record->status }}</td>
                    
                </tr>
                @endforeach
                @if($records->isEmpty())
                <tr>
                    <td colspan="9" class="text-center">No maintenance records found.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection