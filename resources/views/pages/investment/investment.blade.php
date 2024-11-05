@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')



<div class="row">
    <p>
        Investment
    </p>
    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="uil-money-bill me-1 text-primary" style="font-size: 32px"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1">$<span data-plugin="counterup">34,158</span></h4>
                    <p class="text-muted mb-0">Total Pool Fund</p>
                </div>
                </p>
            </div>
        </div>
    </div> <!-- end col-->
    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="mdi mdi-cash-check me-1 text-success" style="font-size: 32px"></i>
                </div>
                <div>
                    <h4 class="mb-1 mt-1">$<span data-plugin="counterup">34,158</span></h4>
                    <p class="text-muted mb-0">Tool Available Fund</p>
                </div>
                </p>
            </div>
        </div>
    </div> <!-- end col-->

    <div class="col-md-6 col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="float-end mt-2">
                    <i class="uil-water-glass me-1 text-warning" style="font-size: 32px"></i>
                </div>
                <div>
                    <div class="d-flex">
                        <h4 class="mb-1 mt-1">$<span data-plugin="counterup">34,158</span></h4>
                        <div style="width: 15px">

                        </div>
                        <div class="d-flex align-items-center">
                            <p class="mb-0">
                                <span data-plugin="counterup">100</span>
                                Machines
                            </p>
                        </div>
                    </div>
                    <p class="text-muted mb-0">Total Machines Asset</p>
                </div>
                </p>
            </div>
        </div>
    </div> <!-- end col-->
</div> <!-- end row-->

@endsection
@section('script')
<!-- apexcharts -->
<script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>
@endsection