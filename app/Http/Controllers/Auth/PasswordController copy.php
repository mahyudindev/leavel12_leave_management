<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            // Get the user model
            $user = $request->user();
            
            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->with('error', 'Invalid current password');
            }

            // Update password directly in the database
            $updated = DB::table('users')
                ->where('user_id', $user->user_id)
                ->update([
                    'password' => Hash::make($validated['password']),
                    'updated_at' => now()
                ]);
            
            if (!$updated) {
                Log::error('Failed to save password for user_id: ' . $user->user_id);
                return back()->with('error', 'Failed to update password. Please try again.');
            }
            
            return back()->with('status', 'password-updated');
        } catch (\Exception $e) {
            Log::error('Password update error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
}
