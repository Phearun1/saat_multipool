<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationRequestController extends Controller
{
    public function viewNewLocationRequest()
    {
        // Fetch all location requests for the current user
        $requests = DB::table('location_requests')
            ->where('user_id', auth()->id())
            ->orderBy('request_date', 'desc')
            ->get();

        return view('pages\machine\new_location_install_request', [
            'requests' => $requests,
        ]);
    }

    public function createNewLocationRequest(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'contact_space_owner' => 'required|string|max:255',
            'location_address' => 'required|string|max:255',
            'google_map_link' => 'nullable',
            'location_photos.*' => 'nullable|image|', // Validate each uploaded photo
        ]);

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('location_photos')) {
            foreach ($request->file('location_photos') as $photo) {
                $photoPaths[] = $photo->store('location_photos', 'public'); // Store in public/storage/location_photos
            }
        }

        // Insert the new request into the database
        DB::table('location_requests')->insert([
            'user_id' => auth()->id(),
            'contact_space_owner' => $request->input('contact_space_owner'),
            'location_address' => $request->input('location_address'),
            'google_map_link' => $request->input('google_map_link'),
            'location_photos' => json_encode($photoPaths), // Save photo paths as JSON
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('view_new_location_request')->with('success', 'Location request submitted successfully.');
    }
    public function updateLocationRequest(Request $request, $id)
    {
        $request->validate([
            'contact_space_owner' => 'required|string|max:255',
            'location_address' => 'required|string|max:255',
            'google_map_link' => 'nullable',
            'location_photos.*' => 'nullable|image|max:2048', // Validate each uploaded photo
        ]);

        // Fetch the existing request
        $locationRequest = DB::table('location_requests')->where('id', $id)->first();

        // Handle deleted photos
        $existingPhotos = json_decode($locationRequest->location_photos, true) ?? [];
        $deletePhotos = $request->input('delete_photos', []);
        $updatedPhotos = array_diff($existingPhotos, $deletePhotos);

        foreach ($deletePhotos as $photo) {
            $photoPath = public_path('storage/' . $photo);
            if (in_array($photo, $existingPhotos) && file_exists($photoPath)) {
                unlink($photoPath); // Remove the photo using unlink
            }
        }

        // Handle new photo uploads
        $newPhotos = [];
        if ($request->hasFile('location_photos')) {
            foreach ($request->file('location_photos') as $photo) {
                $newPhotos[] = $photo->store('location_photos', 'public');
            }
        }

        // Combine updated and new photos
        $finalPhotos = array_merge($updatedPhotos, $newPhotos);

        // Update the database and set status to 'Pending'
        DB::table('location_requests')->where('id', $id)->update([
            'contact_space_owner' => $request->input('contact_space_owner'),
            'location_address' => $request->input('location_address'),
            'google_map_link' => $request->input('google_map_link'),
            'location_photos' => json_encode($finalPhotos),
            'status' => 'Pending', // Change status to 'Pending'
            'updated_at' => now(),
        ]);

        return redirect()->route('view_new_location_request')->with('success', 'Location request updated successfully. The status has been reset to Pending for admin review.');
    }

    public function deleteLocationRequest($id)
    {
        $locationRequest = DB::table('location_requests')->where('id', $id)->first();

        if (!$locationRequest || $locationRequest->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized or request not found.'], 403);
        }

        // Delete associated photos
        if ($locationRequest->location_photos) {
            $photos = json_decode($locationRequest->location_photos, true);
            foreach ($photos as $photo) {
                $photoPath = public_path('storage/' . $photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath); // Remove the photo using unlink
                }
            }
        }

        // Delete the request from the database
        DB::table('location_requests')->where('id', $id)->delete();

        return redirect()->route('view_new_location_request')->with('success', 'Request deleted successfully.');
    }
}
