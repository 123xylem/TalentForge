<?php

namespace App\Http\Controllers;

use App\Models\ListingApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

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
        return to_route('listings.show', $request->listing_id)->with('flash', ['success' => 'Aplication Submitted successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ListingApplication $listingApplication)
    {
        $applicants = $listingApplication->applicants()->get();
        $applicantsData = [];
        foreach ($applicants as $applicant) {
            $applicantsData[] = [
                'cv' => Storage::url($listingApplication->cv),
                'cover_letter' => $listingApplication->cover_letter,
                'status' => $listingApplication->status,
                'created_at' => $listingApplication->created_at,
                'name' => $applicant->name,
                'email' => $applicant->email,
                'phone' => $applicant->phone,
                'address' => $applicant->address,

            ];
        }
        return Inertia::render('ListingApplications/Show', [
            'listingApplication' => $listingApplication,
            'applicants' => $applicantsData,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(listing_application $listing_application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, listing_application $listing_application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(listing_application $listing_application)
    {
        //
    }
}
