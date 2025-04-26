@extends('layouts.master')
@section('title') @lang('translation.Profile') @endsection
@section('content')

<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow-lg border-0 rounded-4" style="width: 600px; background-color: #f8f9fa;">
        <div class="card-body text-center">
            <!-- Profile Image -->
            <img src="{{ URL::asset('assets/images/users/avatar-1.jpg') }}" alt="Profile Picture" class="rounded-circle mb-4" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #6c757d;">

            <!-- Profile Title -->
            <h5 class="mb-2 fw-bold text-dark">{{ __('messages.my_profile') }}</h5>

            <!-- Profile Details -->
            <div class="text-start px-3">
                <p class="mb-2"><i class="fas fa-user me-2"></i><strong>{{ __('messages.full_name') }}:</strong> <span class="">{{ $user->username ?? 'Cheam Chanphearun' }}</span></p>
                <p class="mb-2"><i class="fas fa-phone me-2"></i><strong>{{ __('messages.phone') }}:</strong> <span class="">{{ $user->phone_number ?? '+855 16805280' }}</span></p>
                <p class="mb-3"><i class="fas fa-envelope me-2"></i><strong>{{ __('messages.email') }}:</strong> <span class="">{{ $user->email ?? 'phearun6600@gmail.com' }}</span></p>
                <p class="mb-3"><i class="fas fa-map-marker-alt me-2"></i><strong>{{ __('messages.address') }}:</strong> <span class="">{{ $user->address ?? 'Toul Kork' }}</span></p>
            </div>

            <h5 class="mb-2 fw-bold text-dark">{{ __('messages.account_detail') }}</h5>
            <div class="text-start px-3">
                <p class="mb-2"><i class="fas fa-university me-2"></i><strong>{{ __('messages.bank') }}:</strong> <span class="">ABA</span></p>
                <p class="mb-2"><i class="fas fa-credit-card me-2"></i><strong>{{ __('messages.account_name') }}:</strong> <span class="">016805280</span></p>
                <p class="mb-2"><i class="fas fa-user-tie me-2"></i><strong>{{ __('messages.account_name') }}:</strong> <span class="">CHAN PHEARUN CHEAM</span></p>
            </div>
        </div>
    </div>
</div>


<!-- Additional Styling -->
<style>
    .card {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .rounded-circle {
        border: 3px solid #6c757d;
    }

    h5 {
        font-size: 20px;
        color: #343a40;
    }

    p {
        margin-bottom: 0.5rem;
        font-size: 14px;
        color: #6c757d;
    }

    .btn-gradient-primary {
        padding: 12px;
        font-size: 16px;
        font-weight: bold;
        background: linear-gradient(to right, #007bff, #6610f2);
        color: white;
        border: none;
    }

    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }
</style>

@endsection