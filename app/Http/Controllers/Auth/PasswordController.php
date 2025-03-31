<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Session;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password' => [
                    'required',
                    'confirmed',
                    Password::defaults()->min(8),
                ],
            ], [
                'password.required' => 'Password baru diperlukan',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
                'password.min' => 'Password harus minimal 8 karakter',
                'current_password.required' => 'Password saat ini diperlukan',
                'current_password.current_password' => 'Password saat ini tidak sesuai',
            ]);

            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            Session::flash('status', 'password-updated');
            return back();

        } catch (\Exception $e) {
            Session::flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            return back()->withInput();
        }
    }
}
