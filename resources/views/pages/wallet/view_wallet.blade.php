<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('depositSubmit').addEventListener('click', function(e) {
            e.preventDefault();

            let depositAmount = document.getElementById('depositAmount').value;

            if (!depositAmount || isNaN(depositAmount) || depositAmount <= 0) {
                alert("Please enter a valid amount.");
                return;
            }

            fetch("{{ route('wallet.deposit') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json",
                        "Accept": "text/html" // ✅ Expect HTML response
                    },
                    body: JSON.stringify({
                        amount: depositAmount
                    })
                })
                .then(response => response.text()) // ✅ Get full HTML response
                .then(html => {
                    let iframe = document.getElementById('paymentIframe');

                    // ✅ Use srcdoc instead of contentWindow
                    iframe.srcdoc = html;

                    // ✅ Show the modal containing the iframe
                    let qrCodeModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
                    qrCodeModal.show();

                    // Redirect to the wallet view page after deposit
                    // Poll the status of the transaction every 5 seconds
                    
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                });
        });

        // Function to refresh the wallet balance
        function refreshWalletBalance() {
            fetch("{{ route('wallet.getBalance') }}") // Make sure to remove any extra spaces inside the route function
                .then(response => response.json())
                .then(data => {
                    document.querySelector('h4 span').textContent = data.balance;
                })
                .catch(error => console.error('Error fetching wallet balance:', error));
        }

        // Poll the transaction status
        function pollTransactionStatus(transactionId) {
            setInterval(() => {
                fetch(`/wallet/transaction-status/${transactionId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'Completed') {
                            // If the status is completed, stop polling and refresh the wallet balance
                            refreshWalletBalance();
                            alert('Deposit completed and balance updated.');
                            clearInterval(this); // Stop polling once completed
                        } else {
                            console.log(data.message); // Log the current status (pending)
                        }
                    })
                    .catch(error => console.error('Error fetching transaction status:', error));
            }, 5000); // Poll every 5 seconds
        }
    });
</script>


<style>
    /* Add blur effect when QR code modal is open */
    .blur-background {
        filter: blur(5px);
        transition: filter 0.3s ease-in-out;
    }
</style>

@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')

<div class="row mb-3">
    <div class="col-md-6">
        <h5 style="margin-bottom:20px">{{ __('messages.wallet') }}</h5>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-primary text-white me-3 mb-2 mb-md-3"
            style="width:150px;font-weight:bold"
            data-bs-toggle="modal"
            data-bs-target="#depositModal">
            {{ __('messages.deposit_money') }}
        </button>
        <button type="button" class="btn btn-warning text-black me-3 mb-2 mb-md-3"
            style="width:150px;font-weight:bold"
            data-bs-toggle="modal"
            data-bs-target="#withdrawModal">
            {{ __('messages.withdraw_money') }}
        </button>
    </div>
</div>

<div class="row ">
    <div class="col-md-6 col-xl-5 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="mdi mdi-cash-check me-1 text-success" style="font-size: 32px"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1">$<span>{{ number_format($wallet->total_available_fund ?? 0, 2) }}</span></h4>
                    <p class="text-muted mb-0">{{ __('messages.total_available_fund') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="withdrawModalLabel">{{ __('messages.withdraw_money') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="withdrawForm" method="POST" action="{{ route('wallet.withdraw') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="withdrawAmount" class="form-label">{{ __('messages.amount') }}</label>
                        <input type="number" class="form-control" id="withdrawAmount" name="amount" placeholder="{{ __('messages.enter_amount') }}" required step="0.01">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="withdrawForm" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="depositModalLabel">{{ __('messages.deposit_money') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="depositForm">
                    @csrf
                    <div class="mb-3">
                        <label for="depositAmount" class="form-label">{{ __('messages.amount') }}</label>
                        <input type="number" class="form-control" id="depositAmount" name="amount" placeholder="{{ __('messages.enter_amount') }}" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button> -->
                <button type="submit" class="btn btn-primary" id="depositSubmit">{{ __('messages.submit') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content qr-modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='/wallet'"></button>
            </div>
            <iframe id="paymentIframe" style="width:100%; height:650px; border:none;"></iframe>
        </div>

    </div>

</div>







<!-- Latest Transactions -->
<div class="row">
    <div class="d-flex justify-content-center">
        <div class="col-lg-11 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">{{ __('messages.latest_transactions') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">{{ __('messages.id') }}</th>
                                    <th class="text-center">{{ __('messages.date') }}</th>
                                    <th class="text-center">{{ __('messages.total') }}</th>
                                    <th class="text-center">{{ __('messages.type') }}</th>
                                    <th class="text-center">{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td class="text-center">#{{ $transaction->id }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M, Y H:i') }}</td>
                                    <td class="text-center">${{ number_format($transaction->amount, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill 
                                            {{ $transaction->type === 'Deposit' ? 'bg-success-subtle text-success' : '' }}
                                            {{ $transaction->type === 'Withdraw' ? 'bg-danger-subtle text-danger' : '' }}">
                                            {{ $transaction->type }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill 
                                            {{ $transaction->status === 'Pending' ? 'bg-warning-subtle text-warning' : '' }}
                                            {{ $transaction->status === 'Completed' ? 'bg-success-subtle text-success' : '' }}">
                                            {{ $transaction->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                                @if($transactions->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center">No transactions found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- Add Pagination Links -->
                    <div class="mt-3 d-flex justify-content-end">
                        {{ $transactions->links('pagination::bootstrap-4') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('script')

<!-- apexcharts -->
<script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>

@endsection