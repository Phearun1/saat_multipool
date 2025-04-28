<!-- filepath: /d:/saatcrowdfundingpoolinvestor/resources/views/pages/machine/view_machine_detail.blade.php -->
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
                        <div class="row">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
                            <div class="mt-3">
                                    <h5 class="font-size-14">Profit Share Investors:</h5>
                                    <p>{{ $machine->profit_share_investors }}%</p>
                                </div>
                                <div class="mt-3">
                                    <h5 class="font-size-14">Profit Share Space Owner:</h5>
                                    <p>{{ $machine->profit_share_operators }}%</p>
                                </div>
                                
                                <div class="mt-3">
                                    <h5 class="font-size-14">Machine Status:</h5>
                                    <p class="badge bg-{{ $machine->status ? 'success' : 'danger' }}">
                                        {{ $machine->status ? 'Active' : 'Inactive' }}
                                    </p>
                                </div>
                            </div>
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
                        <div id="userList" class="mt-2 border p-2 bg-white" style="display: none; max-height: 200px; overflow-y: auto; color:black"></div>
                    </div>

                    <!-- Total percentage indicator -->
                    <div class="mt-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Allocation</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 me-3">
                                        <div class="progress" style="height: 20px;">
                                            <div id="percentageBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                        </div>
                                    </div>
                                    <div>
                                        <span id="totalPercentageValue" class="badge rounded-pill bg-primary fs-6">0%</span>
                                    </div>
                                </div>
                                <div class="mt-2 text-muted small" id="percentageMessage">
                                    Total percentage allocation must not exceed 100%.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profit Distribution Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Note</th>
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
                                    <td>
                                        <input type="text" class="form-control comment-input" name="comments[{{ $user->user_id }}]" value="{{ $user->comment ?? '' }}" placeholder="Add a note">
                                    </td>
                                    <td>{{ $user->percentage }}%</td>
                                    <td>
                                        <input type="number" class="form-control profit-input" name="percentages[{{ $user->user_id }}]" value="{{ $user->percentage }}" min="0" max="100" step="0.01" required>
                                    </td>
                                    
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-user" data-user-id="{{ $user->user_id }}">
                                            <i class="mdi mdi-delete"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                @if($users->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center">No users found.</td>
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
                        
                        // Map user_type to role name
                        let roleName = 
                            user.user_type === 1 ? 'Investor' : 
                            user.user_type === 2 ? 'Space Owner' :
                            user.user_type === 3 ? 'Money Collector' :
                            user.user_type === 4 ? 'Maintenance' :
                            user.user_type === 5 ? 'Admin' : 'Unknown';
                        
                        userItem.innerHTML = `<strong>${user.full_name}</strong> (${user.phone_number}) - <span class="badge bg-info">${roleName}</span>`;
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
        
        // Remove "No users found" row if it exists
        let noUserRow = userTable.querySelector('tr td[colspan="7"]');
        if (noUserRow) {
            noUserRow.closest('tr').remove();
        }
        
        // Check if user already exists
        let existingRow = document.getElementById(`userRow_${userId}`);
        if (existingRow) {
            alert('User is already added!');
            return;
        }

        // Map role ID to role name
        const roleName = 
            role === 1 ? 'Investor' : 
            role === 2 ? 'Space Owner' : 
            role === 3 ? 'Money Collector' : 
            role === 4 ? 'Maintenance' : 
            role === 5 ? 'Admin' : 'Unknown';
        
        // Create new row with comment field
        let newRow = document.createElement('tr');
        newRow.id = `userRow_${userId}`;
        newRow.innerHTML = `
            <td>${userId}</td>
            <td>${fullName}</td>
            <td>${roleName}</td>
            <td>
                <input type="text" class="form-control comment-input" 
                       name="comments[${userId}]" 
                       placeholder="Add a note">
            </td>
            
            <td>
                <input type="number" class="form-control profit-input" 
                       name="percentages[${userId}]" 
                       min="0" max="100" value="0" step="0.01" required>
            </td>
            <td>0%</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-user" data-user-id="${userId}">
                    <i class="mdi mdi-delete"></i> Delete
                </button>
            </td>
        `;

        // Add the new row to the table
        userTable.appendChild(newRow);
        
        // Add event listeners
        newRow.querySelector('.profit-input').addEventListener('input', updateTotalPercentage);
        
        // Update remove button event listeners
        attachRemoveEventListeners();
        updateTotalPercentage();
    }

    function attachRemoveEventListeners() {
        document.querySelectorAll('.remove-user').forEach(button => {
            button.removeEventListener('click', deleteUser);
            button.addEventListener('click', deleteUser);
        });
    }

    function deleteUser(event) {
        if (!confirm('Are you sure you want to delete this user?')) {
            return;
        }

        let userId = event.target.getAttribute('data-user-id');
        let machineId = document.querySelector('[name="machine_id"]').value;

        fetch(`/admin/delete_user_profit/${userId}/${machineId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`userRow_${userId}`).remove();
                updateTotalPercentage();
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

    document.querySelectorAll('.profit-input').forEach(input => {
        input.addEventListener('input', updateTotalPercentage);
    });

    function updateTotalPercentage() {
        let totalPercentage = 0;
        document.querySelectorAll('.profit-input').forEach(input => {
            let value = parseFloat(input.value) || 0;
            totalPercentage += value;
        });

        let percentageBar = document.getElementById('percentageBar');
        let totalPercentageValue = document.getElementById('totalPercentageValue');
        let percentageMessage = document.getElementById('percentageMessage');

        percentageBar.style.width = `${totalPercentage}%`;
        percentageBar.setAttribute('aria-valuenow', totalPercentage);
        percentageBar.textContent = `${totalPercentage}%`;
        totalPercentageValue.textContent = `${totalPercentage}%`;

        if (totalPercentage > 100) {
            percentageMessage.textContent = "Total percentage allocation exceeds 100%!";
            percentageMessage.classList.add('text-danger');
        } else {
            percentageMessage.textContent = "Total percentage allocation must not exceed 100%.";
            percentageMessage.classList.remove('text-danger');
        }
    }
</script>

@endsection