<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ListingApplication;
use App\Models\User;

class ListingApplicationUpdate extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ListingApplication $listingApplication, public User $user)
    {
        //
        $this->listingApplication = $listingApplication;
        $this->user = $listingApplication->applicant;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $status = $this->listingApplication->status;
        $listing = $this->listingApplication->listing;

        return (new MailMessage)
            ->subject("Application Status Update: {$listing->title}")
            ->greeting("Hi {$this->user->name},")
            ->line("Your application for {$listing->title} at {$listing->company} has been {$status}.")
            ->action('View Application', route('listing-applications.show', $this->listingApplication->id))
            ->line('Thank you for using TalentForge!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'listing_id' => $this->listingApplication->listing_id,
            'status' => $this->listingApplication->status,
            'title' => $this->listingApplication->listing->title,
            'company' => $this->listingApplication->listing->company,
        ];
    }
}
