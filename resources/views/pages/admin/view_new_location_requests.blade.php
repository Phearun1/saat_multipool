@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')

<div class="row">
    <div class="col-lg-12 d-flex justify-content-between align-items-center mb-3">
        <h5>{{ __('messages.new_location_install_request_list') }}</h5>
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
                                    <th class="text-center">{{ __('messages.users') }}</th>
                                    <th class="text-center">{{ __('messages.contact_space_owner') }}</th>
                                    <th class="text-center">{{ __('messages.location_address') }}</th>
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
                                    <td class="text-center">{{ $request->full_name }}</td>
                                    <td class="text-center">{{ $request->contact_space_owner }}</td>
                                    <td class="text-center">{{ $request->location_address }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($request->created_at)->format('d M, Y') }}</td>
                                    <td class="text-center">
                                        @if ($request->location_photos)
                                        @php $photos = json_decode($request->location_photos, true); @endphp
                                        @foreach ($photos as $photo)
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#photoModal-{{ $loop->index }}">
                                            <img src="{{ asset('storage/' . $photo) }}" alt="Location Photo" width="50" height="50" class="rounded">
                                        </a>

                                        <!-- Photo Modal -->
                                        <div class="modal fade" id="photoModal-{{ $loop->index }}" tabindex="-1" aria-labelledby="photoModalLabel-{{ $loop->index }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="photoModalLabel-{{ $loop->index }}">Location Photo</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ asset('storage/' . $photo) }}" alt="Location Photo" class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                        <form action="{{ route('admin.update_location_request_status', $request->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel-{{ $request->id }}">{{ __('messages.edit_request') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    
                                                    <div class="mb-3">
                                                        <label for="status" class="form-label">Status</label>
                                                        <select class="form-control" name="status" required>
                                                            <option value="Pending" {{ $request->status == 'Pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                                                            <option value="Completed" {{ $request->status == 'Completed' ? 'selected' : '' }}>{{ __('messages.completed') }}</option>
                                                            <option value="Declined" {{ $request->status == 'Declined' ? 'selected' : '' }}>{{ __('messages.declined') }}</option>
                                                        </select>
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
                                                <form action="{{ route('admin.delete_location_request', $request->id) }}" method="POST">
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
                                    <td colspan="8" class="text-center">No requests found.</td>
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

@endsection

@section('script')
<script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/pages/dashboard.init.js') }}"></script>
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
@endsection