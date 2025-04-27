<?php

namespace App\Http\Controllers;

use App\Models\ListingApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Notifications\ListingApplicationUpdate;
use Illuminate\Support\Facades\Log;

class ListingApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $cvPath = $request->cv ? $request->cv : null;
        // Build validation rules
        $rules = [
            'cover_letter' => ['nullable', 'string', 'max:1000', 'min:5'],
        ];

        // Add CV FILE validation only if no existing CV URL
        if (!$cvPath) {
            $rules['cv'] = ['required', 'file', 'mimes:pdf,doc,docx,pdf,jpg,png,jpeg,webp'];
            if ($request->hasFile('cv') && $request->file('cv')->isValid()) {
                $cvPath = $request->file('cv')->store('uploads', 'public');
            }
        }

        $request->validate($rules);
        $listingApplication = new ListingApplication();
        $listingApplication->listing_id = $request->listing_id;
        $listingApplication->user_id = Auth::user()->id;
        $listingApplication->cv = $cvPath;
        $listingApplication->cover_letter = $request->cover_letter;
        $listingApplication->status = 'applied';

        $listingApplication->save();
        $this->notifyApplicant($listingApplication, $listingApplication->listing->owner, null);
        return to_route('listings.show', $request->listing_id)->with('flash', ['success' => 'Aplication Submitted successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ListingApplication $listingApplication)
    {
        $applicant = $listingApplication->applicant;
        $skills = $applicant->skills;
        $applicant['skills'] = $skills;
        return Inertia::render('ListingApplications/Show', [
            'listingApplication' => $listingApplication,
            'applicant' => $applicant,
            'listing' => $listingApplication->listing,
            'skills' => $skills,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ListingApplication $listingApplication)
    {

        return Inertia::render('ListingApplications/Edit', [
            'listingApplication' => $listingApplication,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ListingApplication $listingApplication)
    {
        //  
        $action = $request->action === 'progress' ? 'shortlisted' : 'rejected';
        $listingApplication->status = $action;
        $applicant = $listingApplication->applicant;
        //NOTIFY Applicant or employer

        $listingApplication->save();
        return to_route('listing-applications.show', $listingApplication->id)->with('flash', ['success' => 'Application updated successfully!']);
    }

    private function notifyApplicant(ListingApplication $listingApplication, User $recipient, string $employerAction = null): void
    {
        try {
            $recipient->notify(new ListingApplicationUpdate($listingApplication, $recipient, $employerAction));
            //Notify now handles email and app notifications so we dont need a mailable class
            // Mail::to($recipient->email)->queue(new ListingApplicationUpdated($recipient, $listingApplication, $employerAction));
        } catch (\Exception $e) {
            \Log::error('Email failed: ' . $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ListingApplication $listingApplication)
    {
        //
    }
}
