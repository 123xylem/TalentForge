<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'type' => 'required|string|in:job_hunter,employer',
            'bio' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'profile_image' => 'nullable|file|mimes:jpg,png,pdf,jpeg,webp|max:2048',
            'cv' => 'nullable|file|mimes:jpg,png,pdf,doc,docx|max:2048',
            'phone' => 'nullable|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company' => 'nullable|string|max:255',
        ]);

        $imgFilePath = null;
        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            $imgFilePath = $request->file('profile_image')->store('uploads', 'public');
        }

        $cvFilePath = null;
        if ($request->hasFile('cv') && $request->file('cv')->isValid()) {
            $cvFilePath = $request->file('cv')->store('uploads', 'public');
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'bio' => $request->bio,
            'website' => $request->website,
            'location' => $request->location,
            'profile_image' => $imgFilePath,
            'cv' => $cvFilePath,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard.index');
    }
}
