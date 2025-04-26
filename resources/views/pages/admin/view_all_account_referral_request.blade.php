@extends('layouts.master')

@section('title', 'View All Account Referral Requests')

@section('css')
<style>
    .table th, .table td {
        font-size: 1.1rem; /* Increase font size */
    }
    .card-title {
        font-size: 1.5rem; /* Increase card title font size */
    }
    .btn {
        font-size: 1rem; /* Increase button font size */
    }
    .table th, .table td {
        font-size:14px;
    }
    
</style>
@endsection

@section('content')
<div class="container mt-2">
    <h3 class="text-center mb-5">{{ __('messages.view_all_account_referral_requests') }}</h3>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Search Form -->
    <form method="GET" action="{{ route('admin.view_all_account_referral_requests') }}" class="mb-4">
        <div class="row ">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Search by Username, Email, or Full Name" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
        
    </form>

    <!-- Referrals Table -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ __('messages.referral_requests') }}</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('messages.id') }}</th>
                        <th>{{ __('messages.username') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.full_name') }}</th>
                        <th>{{ __('messages.user_type') }}</th>
                        <th>{{ __('messages.date_joined') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @forelse ($referrals as $referral)
                    <tr>
                        <td>{{ $referral->user_id }}</td>
                        <td>{{ $referral->username }}</td>
                        <td>{{ $referral->email }}</td>
                        <td>{{ $referral->full_name }}</td>
                        <td>{{ $userTypes[$referral->user_type] ?? 'Unknown' }}</td>
                        <td>{{ \Carbon\Carbon::parse($referral->date_joined)->format('d M, Y') }}</td>
                        <td>
                            @if ($referral->status === 0)
                            <span class="badge bg-warning">Pending</span>
                            @elseif($referral->status === 2)
                            <span class="badge bg-danger">Rejected</span>
                            @else
                            <span class="badge  bg-success">Approved</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.approve_referral', $referral->user_id) }}" class="btn btn-success btn-sm" onclick="return confirmApprove()">Approve</a>
                            <a href="{{ route('admin.reject_referral', $referral->user_id) }}" class="btn btn-danger btn-sm" onclick="return confirmReject()">Reject</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No referral requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    function confirmApprove() {
        return confirm('Are you sure you want to approve this referral?');
    }

    function confirmReject() {
        return confirm('Are you sure you want to reject this referral?');
    }
</script>
@endsection