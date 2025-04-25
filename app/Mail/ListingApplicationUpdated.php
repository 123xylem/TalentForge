<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Listing;
use App\Models\User;
use App\Models\ListingApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Address;

class ListingApplicationUpdated extends Mailable
{
    use Queueable, SerializesModels;
    public Listing $listing;
    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public ListingApplication $listingApplication,
    ) {
        //
        $this->listing = $listingApplication->listing;
        Log::info([$this->listing, $this->listingApplication, $this->user]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            /* should use global from address */
            subject: 'Listing Application Updated',
            cc: [new Address('chrisjcullen@gmx.com', 'Chris Cullen')]

        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.listing-application-updated',
            with: [
                'listing_title' => $this->listing->title,
                'listing_company' => $this->listing->owner,
                'user_name' => $this->user->name,
                'application_status' => $this->listingApplication->status,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
