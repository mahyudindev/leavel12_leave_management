<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->user_id . ',user_id'],
                'current_password' => ['nullable', 'current_password'],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            ]);

            // Get the user
            $user = $request->user();
            
            // Prepare update data
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'updated_at' => now()
            ];
            
            // Add password to update data if provided
            if (!empty($validated['password'])) {
                if (!Hash::check($validated['current_password'], $user->password)) {
                    return back()->with('error', 'Invalid current password');
                }
                $updateData['password'] = Hash::make($validated['password']);
            }
            
            // Update directly in the database
            $updated = DB::table('users')
                ->where('user_id', $user->user_id)
                ->update($updateData);
            
            if (!$updated) {
                Log::error('Failed to save profile update for user: ' . $user->user_id);
                return back()->with('error', 'Failed to update profile. Please try again.');
            }
            
            return back()->with('status', 'profile-updated');
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while updating your profile: ' . $e->getMessage());
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
