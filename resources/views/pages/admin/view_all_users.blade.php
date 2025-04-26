@extends('layouts.master')

@section('title')
@lang('translation.Customers')
@endsection

@section('css')
<link href="{{ URL::asset('assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') User Management @endslot
@slot('title') {{ __('messages.users') }} @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
        {{ __('messages.add_user') }}
        </button>

        <div class="card">
            <div class="card-body">
                <!-- Search Form -->
                <div class="mb-4">
                    <div class="input-group">
                        <input type="text" id="phoneSearch" class="form-control" placeholder="  {{ __('messages.search') }} {{ __('messages.phone') }} ">
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-centered datatable dt-responsive nowrap table-card-list" style="border-collapse: collapse; width: 100%;" id="userTable">
                        <thead>
                            <tr>
                                <th> {{ __('messages.customer_id') }}</th>
                                <th> {{ __('messages.customer') }}</th>
                                <th> {{ __('messages.email') }}</th>
                                <th> {{ __('messages.phone') }}</th>
                                <th> {{ __('messages.role') }}</th>
                                <th> {{ __('messages.actions') }}</th>
                            </tr>
                               
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td><a href="javascript: void(0);" class="text-reset fw-bold">#{{ $user->user_id }}</a></td>
                                <td>
                                    <div class="d-inline-block me-2">
                                        <span class="avatar-title rounded-circle bg-light text-body">{{ strtoupper(substr($user->full_name, 0, 1)) }}</span>
                                    </div>
                                    <span>{{ $user->full_name }}</span>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone_number }}</td>
                                @php
                                $roles = [
                                1 => 'Investor',
                                2 => 'Space Owner',
                                3 => 'Money Collector',
                                4 => 'Maintenance',
                                5 => 'Admin'
                                ];
                                @endphp

                                <td>{{ $roles[$user->user_type] ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('admin.user.details', $user->user_id) }}" class="px-3 text-primary">
                                        <i class="uil uil-eye font-size-18"></i>  {{ __('messages.view_detail') }}
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    <div class="d-flex justify-content-end">
                        {{ $users->links('pagination::bootstrap-4') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">{{ __('messages.customer_id') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.user.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">{{ __('messages.username') }}</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('messages.password') }}</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('messages.email') }}</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">{{ __('messages.full_name') }}</label>
                        <input type="text" class="form-control" name="full_name">
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">{{ __('messages.phone_number') }}</label>
                        <input type="text" class="form-control" name="phone_number">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">{{ __('messages.address') }}</label>
                        <input type="text" class="form-control" name="address">
                    </div>
                    <div class="mb-3">
                        <label for="user_type" class="form-label">{{ __('messages.user_type') }}</label>
                        <select class="form-control" name="user_type" required>

                            <option value="1">{{ __('messages.investor') }}</option>
                            <option value="2">{{ __('messages.space_owner') }}</option>
                            <option value="3">{{ __('messages.money_collector') }}</option>
                            <option value="4">{{ __('messages.maintenance') }}</option>
                            <option value="5">{{ __('messages.admin') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">{{ __('messages.status') }}</label>
                        <select class="form-control" name="status" required>
                            <option value="1">{{ __('messages.active') }}</option>
                            <option value="0">{{ __('messages.inactive') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    document.getElementById('phoneSearch').addEventListener('keyup', function() {
        let query = this.value;

        // Make an AJAX request
        fetch(`{{ route('admin.ajax_search_users') }}?phone_number=${query}`)
            .then(response => response.json())
            .then(data => {
                let tableBody = document.querySelector('#userTable tbody');
                tableBody.innerHTML = ''; // Clear the table body

                // Map user roles for dynamic display
                const roles = {
                    1: 'Investor',
                    2: 'Space Owner',
                    3: 'Money Collector',
                    4: 'Maintenance',
                    5: 'Admin'
                };

                // Populate the table with new data
                data.users.forEach(user => {
                    tableBody.innerHTML += `
                    <tr>
                        <td><a href="javascript: void(0);" class="text-reset fw-bold">#${user.user_id}</a></td>
                        <td>
                            <div class="d-inline-block me-2">
                                <span class="avatar-title rounded-circle bg-light text-body">
                                    ${user.full_name.charAt(0).toUpperCase()}
                                </span>
                            </div>
                            <span>${user.full_name}</span>
                        </td>
                        <td>${user.email}</td>
                        <td>${user.phone_number}</td>
                        <td>${roles[user.user_type] || '-'}</td>
                        <td>
                            <a href="/admin/view_users_detail/${user.user_id}" class="px-3 text-primary">
                                <i class="uil uil-eye font-size-18"></i> View Detail
                            </a>
                        </td>
                    </tr>
                `;
                });
            })
            .catch(error => console.error('Error fetching user data:', error));
    });
</script>
@endsection