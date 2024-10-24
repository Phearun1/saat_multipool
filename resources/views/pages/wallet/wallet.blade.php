@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Minible @endslot
@slot('title') Dashboard @endslot
@endcomponent