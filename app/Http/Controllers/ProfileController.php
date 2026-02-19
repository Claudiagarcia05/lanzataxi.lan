<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $solicitud): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $solicitud->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $solicitud): RedirectResponse
    {
        $solicitud->user()->fill($solicitud->validated());

        if ($solicitud->user()->isDirty('email')) {
            $solicitud->user()->email_verified_at = null;
        }

        $solicitud->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $solicitud): RedirectResponse
    {
        $solicitud->validate([
            'password' => ['required', 'current_password'],
        ]);

        $usuario = $solicitud->user();

        Auth::guard('web')->logout();

        $usuario->delete();

        $solicitud->session()->invalidate();
        $solicitud->session()->regenerateToken();

        return Redirect::to('/');
    }
}
