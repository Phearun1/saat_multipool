@extends('layouts.master')

@section('title') {{ __('messages.pools_map') }} @endsection

@section('css')
<link href="{{ URL::asset('/assets/libs/ion-rangeslider/ion-rangeslider.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Investments @endslot
@slot('title') {{ __('messages.all_pools') }} @endslot
@endcomponent

<div class="row">
    <div class="col-xl-11 col-lg-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('messages.all_pools') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-inline float-md-end">
                                <form action="{{ route('admin.view_all_pool') }}" method="GET" class="d-flex">
                                    <input type="text" name="search" class="form-control bg-light border-light rounded" placeholder="{{ __('messages.search') }}..." value="{{ request()->input('search') }}">
                                    <button type="submit" class="btn btn-primary ms-2">{{ __('messages.search') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mt-3 mb-3">
                            @if(Auth::user() && Auth::user()->user_type == 5)
                            <button class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addPoolModal"> {{ __('messages.add_pool') }}</button>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-centered table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">{{ __('messages.pool_id') }}</th>
                                                <th class="text-center">{{ __('messages.pool_name') }}</th>
                                                <th class="text-center">{{ __('messages.manager') }}</th>
                                                <th class="text-center">{{ __('messages.creation_date') }}</th>
                                                <th class="text-center">{{ __('messages.status') }}</th>
                                                <th class="text-center">{{ __('messages.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pools as $pool)
                                            <tr>
                                                <td class="text-center"><a href="javascript:void(0);" class="text-body fw-bold">#{{ $pool->pool_id }}</a></td>
                                                <td class="text-center">{{ $pool->pool_name }}</td>
                                                <td class="text-center">{{ $pool->manager_name }}</td>
                                                <td class="text-center">{{ date('M d, Y', strtotime($pool->creation_date)) }}</td>
                                                <td class="text-center">
                                                    @if ($pool->status === 'Active')
                                                    <span class="badge bg-success">{{ __('messages.active') }}</span>
                                                    @elseif ($pool->status === 'Closed')
                                                    <span class="badge bg-danger">{{ __('messages.closed') }}</span>
                                                    @else
                                                    <span class="badge bg-warning">{{ __('messages.inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="/view_pool_detail/{{ $pool->pool_id }}" class="btn btn-primary btn-sm">{{ __('messages.view_detail') }}</a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center">No Investment Pools Found</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <p class="mb-sm-0">Showing {{ $pools->count() }} of {{ $pools->total() }}</p>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $pools->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addPoolModal" tabindex="-1" aria-labelledby="addPoolModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addPoolModalLabel">{{ __('messages.add_pool') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.create_pool') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pool_name" class="form-label">{{ __('messages.pool_name') }}</label>
                                <input type="text" class="form-control" name="pool_name" required maxlength="255" value="{{ old('pool_name') }}">
                                @error('pool_name')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">{{ __('messages.description') }}</label>
                                <textarea class="form-control" name="description" rows="2" maxlength="500" style="resize: none;">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="manager_user_id" class="form-label">{{ __('messages.manager') }}</label>
                                <select class="form-control select2" name="manager_user_id" required>
                                    <option value="">{{ __('messages.select_manager') }}</option>
                                    @foreach($managers as $manager)
                                    <option value="{{ $manager->user_id }}" {{ old('manager_user_id') == $manager->user_id ? 'selected' : '' }}>
                                        {{ $manager->name }}
                                        @if(isset($manager->user_type))
                                        @php
                                        $userTypeLabels = [
                                        1 => 'Investor',
                                        2 => 'Space Owner',
                                        3 => 'Money Collector',
                                        4 => 'Maintenance',
                                        5 => 'Admin'
                                        ];
                                        $userType = $userTypeLabels[$manager->user_type] ?? 'Unknown';
                                        @endphp
                                        ({{ $userType }})
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                @error('manager_user_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="target_fund" class="form-label">{{ __('messages.target_fund') }}</label>
                                <input type="number" step="0.01" min="0" class="form-control" name="target_fund" required value="{{ old('target_fund') }}">
                                @error('target_fund')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">{{ __('messages.currency') }}</label>
                                <select class="form-control" name="currency" required>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                </select>
                                @error('currency')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="profit_sharing_model" class="form-label">{{ __('messages.profit_sharing_model') }}</label>
                                <input type="text" class="form-control" name="profit_sharing_model" maxlength="255" value="{{ old('profit_sharing_model') }}">
                                @error('profit_sharing_model')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="terms_and_conditions" class="form-label">{{ __('messages.terms_and_conditions') }}</label>
                                <textarea class="form-control" name="terms_and_conditions" rows="3">{{ old('terms_and_conditions') }}</textarea>
                                @error('terms_and_conditions')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('messages.status') }}</label>
                                <select class="form-control" name="status" required>
                                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                                    <option value="Closed" {{ old('status') == 'Closed' ? 'selected' : '' }}>{{ __('messages.closed') }}</option>
                                </select>
                                @error('status')
                                <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.add_pool') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

@endsection