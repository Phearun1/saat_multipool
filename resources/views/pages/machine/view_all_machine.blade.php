@extends('layouts.master')

@section('title') {{ __('messages.machines_map') }} @endsection

@section('css')
<link href="{{ URL::asset('/assets/libs/ion-rangeslider/ion-rangeslider.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Ecommerce @endslot
@slot('title') {{ __('messages.machines_map') }} @endslot
@endcomponent

<!-- Map Section -->
<div id="map" style="margin:auto; width:1300px; height: 450px; margin-bottom: 20px;"></div>

<div class="row">
    <div class="col-xl-11 col-lg-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ __('messages.all_machine') }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-inline float-md-end">
                                <form action="{{ route('machines.all') }}" method="GET" class="d-flex">
                                    <input type="text" name="search" class="form-control bg-light border-light rounded" placeholder="{{ __('messages.search') }}..." value="{{ request()->input('search') }}">
                                    <button type="submit" class="btn btn-primary ms-2">{{ __('messages.search') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mt-3 mb-3">
                            <button class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addMachineModal"> {{ __('messages.add_machine') }}</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-centered table-nowrap mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center">{{ __('messages.machine_id') }}</th>
                                                <th class="text-center">{{ __('messages.machine_location') }}</th>
                                                <th class="text-center">{{ __('messages.coordinates') }}</th>
                                                <th class="text-center">{{ __('messages.image') }}</th>
                                                <th class="text-center">{{ __('messages.machine_status') }}</th>
                                                <th class="text-center">{{ __('messages.actions') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($machines as $machine)
                                            <tr>
                                                <td class="text-center"><a href="javascript:void(0);" class="text-body fw-bold">#{{ $machine->machine_id }}</a></td>
                                                <td class="text-center">{{ $machine->location }}</td>
                                                <td class="text-center">{{ $machine->latitude }}, {{ $machine->longitude }}</td>
                                                <td class="text-center">
                                                    <img src="{{ asset('storage/' . ($machine->image ?? 'default/saat_pool.png')) }}" alt="Machine Image" class="img-thumbnail" style="width: 100px;">
                                                </td>
                                                <td class="text-center">
                                                    @if ($machine->status === 'Active')
                                                    <span class="badge bg-success">{{ __('messages.active') }}</span>
                                                    @elseif ($machine->status === 'Maintenance')
                                                    <span class="badge bg-warning">{{ __('messages.maintenance') }}</span>
                                                    @else
                                                    <span class="badge bg-danger">{{ __('messages.inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="/view_machine_detail/{{ $machine->machine_id }}" class="btn btn-primary btn-sm">{{ __('messages.view_detail') }}</a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No Machines Found</td>
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
                            <p class="mb-sm-0">Showing {{ $machines->count() }} of {{ $machines->total() }}</p>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $machines->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- filepath: /d:/saatcrowdfundingpoolinvestor/resources/views/pages/machine/view_all_machine.blade.php -->
<div class="modal fade" id="addMachineModal" tabindex="-1" aria-labelledby="addMachineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMachineModalLabel">{{ __('messages.add_machine') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('machines.add') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">{{ __('messages.machine_location') }}</label>
                                <input type="text" class="form-control" name="location" required>
                            </div>
                            <div class="mb-3">
                                <label for="installation_date" class="form-label">{{ __('messages.installation_date') }}</label>
                                <input type="date" class="form-control" name="installation_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="profit_share_investors" class="form-label">{{ __('messages.profit_share_for_investors') }} (%)</label>
                                <input type="number" class="form-control" name="profit_share_investors" required>
                            </div>
                            <div class="mb-3">
                                <label for="profit_share_operators" class="form-label">{{ __('messages.profit_share_for_operators') }} (%)</label>
                                <input type="number" class="form-control" name="profit_share_operators" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address" class="form-label">{{ __('messages.coordinates') }} (latitude, longitude)</label>
                                <input type="text" class="form-control" name="address" placeholder="11.583522860946546, 104.88079010741988" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">{{ __('messages.status') }}</label>
                                <select class="form-control" name="status" required>
                                    <option value="Active">{{ __('messages.active') }}</option>
                                    <option value="Inactive">{{ __('messages.inactive') }}</option>
                                    <option value="Maintenance">{{ __('messages.maintenance') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">{{ __('messages.machine_image') }}</label>
                                <input type="file" class="form-control" name="image">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{ __('messages.add_machine') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<script>
    var map = L.map('map').setView([12.5564, 104.9282], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Add markers for each machine
    @foreach($machines as $machine)
        @if($machine->latitude && $machine->longitude)
            L.marker([{{ $machine->latitude }}, {{ $machine->longitude }}]).addTo(map)
            .bindPopup('<b>Machine ID:</b> {{ $machine->machine_id }}<br><b>Location:</b> {{ $machine->location }}<br><b>Coordinates:</b> {{ $machine->latitude }}, {{ $machine->longitude }}<br><b>Status:</b> {{ $machine->status }}');
        @endif
    @endforeach
</script>
@endsection