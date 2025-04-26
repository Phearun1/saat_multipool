<script>
    function removeImage(key, photoPath, requestId) {
        // Confirm before removing the image
        if (confirm('Are you sure you want to delete this photo?')) {
            // Remove the image element
            const imageElement = event.target.closest('.position-relative');
            imageElement.remove();

            // Add a hidden input to track the image for deletion
            const form = document.querySelector(`#editModal-${requestId} form`);
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'delete_photos[]';
            input.value = photoPath;
            form.appendChild(input);
        }
    }
</script>


@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')

<div class="row">
    <div class="col-lg-12 d-flex justify-content-between align-items-center mb-3">
        <h5>{{ __('messages.new_location_install_request_list') }}</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newLocationModal">
        {{ __('messages.new_location_install') }}
        </button>
    </div>
</div>

<div class="row">
    <div class="d-flex justify-content-center">
        <div class="col-lg-11 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">{{ __('messages.request_history_list') }}</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">{{ __('messages.id') }}</th>
                                    <th class="text-center">{{ __('messages.name') }}</th>
                                    <th class="text-center">{{ __('messages.location') }}</th>
                                    <th class="text-center">{{ __('messages.request_date') }}</th>
                                    <th class="text-center">{{ __('messages.photos') }}</th>
                                    <th class="text-center">{{ __('messages.status') }}</th>
                                    <th class="text-center">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $request)
                                <tr>
                                    <td class="text-center">#{{ $request->id }}</td>
                                    <td class="text-center">{{ $request->contact_space_owner }}</td>
                                    <td class="text-center">{{ $request->location_address }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($request->request_date)->format('d M, Y') }}</td>
                                    <td class="text-center">
                                        @if ($request->location_photos)
                                        @php $photos = json_decode($request->location_photos, true); @endphp
                                        @foreach ($photos as $photo)
                                        <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $photo) }}" alt="Location Photo" width="50" height="50" class="rounded">
                                        </a>
                                        @endforeach
                                        @else
                                        No Photos
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill
                                            {{ $request->status === 'Completed' ? 'bg-success-subtle text-success' : '' }}
                                            {{ $request->status === 'Pending' ? 'bg-warning text-white' : '' }}
                                            {{ $request->status === 'Declined' ? 'bg-danger text-white' : '' }}
                                            p-2 font-size-14">
                                            {{ $request->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal" data-bs-target="#editModal-{{ $request->id }}">
                                            {{ __('messages.edit') }}
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $request->id }}">
                                            {{ __('messages.delete') }}
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal-{{ $request->id }}" tabindex="-1" aria-labelledby="editModalLabel-{{ $request->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="{{ route('edit_location_request', $request->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel-{{ $request->id }}">Edit Request</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="contactSpaceOwner" class="form-label">Contact Space Owner</label>
                                                        <input type="text" class="form-control" name="contact_space_owner" value="{{ $request->contact_space_owner }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="locationAddress" class="form-label">Location Address</label>
                                                        <input type="text" class="form-control" name="location_address" value="{{ $request->location_address }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="googleMapLink" class="form-label">Google Map Link</label>
                                                        <input type="text" class="form-control" name="google_map_link" value="{{ $request->google_map_link }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="locationPhotos" class="form-label">Upload Location Photos</label>
                                                        <input type="file" class="form-control" name="location_photos[]" multiple>
                                                        
                                                    </div>
                                                    <div class="mt-2">
                                                        <label class="form-label">Current Photos</label>
                                                        <div class="d-flex flex-wrap">
                                                            @php $photos = json_decode($request->location_photos, true); @endphp
                                                            @if ($photos)
                                                            @foreach ($photos as $key => $photo)
                                                            <div class="position-relative me-3 mb-2">
                                                                <img src="{{ asset('storage/' . $photo) }}" alt="Location Photo" width="100" height="100" class="rounded">
                                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle" onclick="removeImage('{{ $key }}', '{{ $photo }}', {{ $request->id }})">×</button>
                                                                <input type="hidden" name="existing_photos[]" value="{{ $photo }}">
                                                            </div>
                                                            @endforeach
                                                            @else
                                                            <p class="text-muted">No Photos</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>


                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal-{{ $request->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $request->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel-{{ $request->id }}">Delete Request</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this request?
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('delete_location_request', $request->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No requests found.</td>
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

<!-- Modal for New Location Install -->
<div class="modal fade" id="newLocationModal" tabindex="-1" aria-labelledby="newLocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newLocationModalLabel">{{ __('messages.new_location_install') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('create_new_location_request') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="contactSpaceOwner" class="form-label">{{ __('messages.contact_space_owner') }}</label>
                        <input type="text" class="form-control" name="contact_space_owner" placeholder="Enter contact details" required>
                    </div>
                    <div class="mb-3">
                        <label for="locationAddress" class="form-label">{{ __('messages.location_address') }}</label>
                        <input type="text" class="form-control" name="location_address" placeholder="Enter location address" required>
                    </div>
                    <div class="mb-3">
                        <label for="googleMapLink" class="form-label">{{ __('messages.google_map_link') }}</label>
                        <input type="text" class="form-control" name="google_map_link" placeholder="Enter Google Map link">
                    </div>
                    <div class="mb-3">
                        <label for="locationPhotos" class="form-label">{{ __('messages.upload_location_photos') }}</label>
                        <input type="file" class="form-control" name="location_photos[]" multiple>
                        <small class="text-muted">You can upload multiple photos (Max: 2MB each).</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>
@endsection