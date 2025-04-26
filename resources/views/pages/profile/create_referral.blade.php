@extends('layouts.master')
@section('title', 'Create Referral')

@section('content')
<div class="container mt-5">
    <h3 class="text-center mb-4">{{ __('messages.user_referrals') }}</h3>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Button to Open Modal -->
    <div class="text-end mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal">
            <i class="fas fa-plus"></i> {{ __('messages.create_new_account') }}
        </button>
    </div>

    <!-- Referrals Table -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ __('messages.accounts_created_by_you') }}</h5>
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
                            @if ($referral->status === 1)
                            <span class="badge bg-warning">Pending</span>
                            @elseif($referral->status === 2)
                            <span class="badge bg-danger">Rejected</span>
                            @else
                            <span class="badge bg-success">Approved</span>
                            @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No accounts created yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Creating Account -->
<div class="modal fade" id="createAccountModal" tabindex="-1" aria-labelledby="createAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAccountModalLabel">Create New Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('create_referral') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" name="full_name" id="full_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" class="form-control"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="user_type" class="form-label">User Type</label>
                            <select name="user_type" id="user_type" class="form-control" required>
                                <option value="" disabled selected>Select User Type</option>
                                @foreach ($userTypes as $key => $type)
                                <option value="{{ $key }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection