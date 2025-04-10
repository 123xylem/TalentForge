<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Skill;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        $isEmployer = $request->user()->type === 'employer';
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            'availableSkills' => !$isEmployer ? Skill::all() : [],
            'userSkills' => !$isEmployer ? $request->user()->skills : [],
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        $imgFilePath = null;
        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            $imgFilePath = $request->file('profile_image')->store('uploads', 'public');
            $user->profile_image = Storage::url($imgFilePath);
        }

        $cvFilePath = null;
        if ($request->hasFile('cv') && $request->file('cv')->isValid()) {
            $cvFilePath = $request->file('cv')->store('uploads', 'public');
            $user->cv = Storage::url($cvFilePath);
        }
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $user->save();

        // Update skills
        if ($request->has('skills')) {
            $user->skills()->sync($request->skills);
        }

        return to_route('profile.edit')->with('success', 'Profile updated successfully');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
