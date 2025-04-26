@extends('layouts.master')

@section('title')
@lang('translation.Product_Detail')
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Ecommerce @endslot
@slot('title') Product Detail @endslot
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="product-detail">
                            <div class="tab-content position-relative">
                                <div class="product-img">
                                    <img src="{{ URL::asset('assets/images/saat_pool.png') }}" alt="" class="img-fluid mx-auto d-block">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="mt-4 mt-xl-3 d-flex justify-content-between align-items-center">
                            <h4 class="font-size-20 mb-3">Machine ID: {{ $machine->machine_id }}</h4>
                            @if(Auth::user() && Auth::user()->user_type == 5)
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignProfitModal">
                                Profit Percentage
                            </button>
                            @endif
                        </div>
                        <h5 class="uil uil-bill me-1 mt-2 pt-2 text-muted">/Litre: 500 Riel</h5>
                        <div class="mt-3">
                            <h5 class="font-size-14">Total Plastic Saved:</h5>
                            <p>{{ $machine->bottles_saved_count }}</p>
                        </div>
                        <div class="mt-3">
                            <h5 class="font-size-14">Average Sale Per Day:</h5>
                            <p>${{ number_format($machine->total_revenue / 30, 2) }}</p>
                        </div>
                        <div class="mt-3">
                            <h5 class="font-size-14">Address:</h5>
                            <p>{{ $machine->location }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-2">Water Sale Transactions</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Date and Time</th>
                                    <th>Water Amount (L)</th>
                                    <th>Total Sale ($)</th>
                                    <th>Payment Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>#{{ $transaction->sale_id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->sale_date_time)->format('d M, Y h:i A') }}</td>
                                    <td>{{ $transaction->volume_sold }}L</td>
                                    <td>${{ number_format($transaction->sale_amount, 2) }}</td>
                                    <td>{{ $transaction->payment_method }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Profit Modal -->
@if(Auth::user() && Auth::user()->user_type == 5)
<div class="modal fade" id="assignProfitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Profit Percentage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignProfitForm" action="{{ route('admin.assignProfit') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="machine_id" value="{{ $machine->machine_id }}">

                    <!-- Search User Input -->
                    <div class="mb-3">
                        <label class="form-label">Search User (Full Name / Phone Number)</label>
                        <input type="text" id="userSearch" class="form-control" placeholder="Enter name or phone number">
                        <div id="userList" class="mt-2 border p-2 bg-white" style="display: none; max-height: 200px; overflow-y: auto;"></div>
                    </div>

                    <!-- Profit Distribution Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Current %</th>
                                    <th>New %</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="userTable">
                                @foreach($users as $user)
                                <tr id="userRow_{{ $user->user_id }}">
                                    <td>{{ $user->user_id }}</td>
                                    <td>{{ $user->full_name }}</td>
                                    <td>{{ $user->role }}</td>
                                    <td>{{ $user->percentage }}%</td>
                                    <td>
                                        <input type="number" class="form-control profit-input" name="percentages[{{ $user->user_id }}]" min="0" max="100" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteUser({{ $user->user_id }})">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                                @if($users->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">No users found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Assign Profit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
    document.getElementById('userSearch').addEventListener('input', function() {
        let query = this.value.trim();
        if (query.length < 2) {
            document.getElementById('userList').style.display = 'none';
            return;
        }

        fetch(`{{ route('admin.searchUsers') }}?query=${query}`)
            .then(response => response.json())
            .then(data => {
                let userList = document.getElementById('userList');
                userList.innerHTML = '';
                if (data.users.length > 0) {
                    userList.style.display = 'block';
                    data.users.forEach(user => {
                        let userItem = document.createElement('div');
                        userItem.classList.add('p-2', 'border-bottom');
                        userItem.innerHTML = `<strong>${user.full_name}</strong> (${user.phone_number})`;
                        userItem.style.cursor = 'pointer';
                        userItem.onclick = function() {
                            addUserToTable(user.user_id, user.full_name, user.user_type);
                            document.getElementById('userList').style.display = 'none';
                        };
                        userList.appendChild(userItem);
                    });
                } else {
                    userList.style.display = 'none';
                }
            });
    });

    function addUserToTable(userId, fullName, role) {
        let userTable = document.getElementById('userTable');
        let existingRow = document.getElementById(`userRow_${userId}`);
        if (existingRow) {
            alert('User is already added!');
            return;
        }

        let row = `<tr id="userRow_${userId}">
            <td>${userId}</td>
            <td>${fullName}</td>
            <td>${role}</td>
            <td>0%</td>
            <td><input type="number" class="form-control profit-input" name="percentages[${userId}]" min="0" max="100" value="0" required></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-user" data-user-id="${userId}">Delete</button></td>
        </tr>`;

        userTable.innerHTML += row;
        attachRemoveEventListeners();
    }

    function attachRemoveEventListeners() {
        document.querySelectorAll('.remove-user').forEach(button => {
            button.removeEventListener('click', removeUser);
            button.addEventListener('click', removeUser);
        });
    }

    function removeUser(event) {
        let userId = event.target.getAttribute('data-user-id');
        let machineId = document.querySelector('[name="machine_id"]').value;

        fetch(`/admin/delete_user_profit/${userId}/${machineId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`userRow_${userId}`).remove();
            } else {
                alert(data.message);
            }
        });
    }

    attachRemoveEventListeners();

    document.getElementById('assignProfitForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch("{{ route('admin.assignProfit') }}", {
            method: 'POST',
            body: formData
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        }).catch(error => {
            console.error("Error:", error);
            alert("An error occurred while assigning profit.");
        });
    });
</script>

@endsection