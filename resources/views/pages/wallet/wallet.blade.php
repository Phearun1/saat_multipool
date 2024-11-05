@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')


<div class="row">
    <p>
        Wallet
    </p>
</div>

@endsection
@section('script')
<!-- apexcharts -->
<script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>
@endsection