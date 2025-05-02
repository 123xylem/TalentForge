<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Listing;
use App\Models\User;

class ListingUpdate extends Notification
{
    use Queueable;

    public $application;
    /**
     * Create a new notification instance.
     */
    public function __construct(public Listing $listing, public User $user)
    {
        $this->listing = $listing;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {

        $application = $this->listing->applications->where('user_id', $this->user->id)->first();
        if ($application->status == 'rejected') {
            \Log::info('Application rejected, not sending notification');
            return [];
        }

        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        try {
            $message = "The listing {$this->listing->title} at {$this->listing->company} has been closed so you are in the final cohort!";
            return (new MailMessage)
                ->subject("Application Status Update: {$this->listing->title}")
                ->greeting("Hi {$this->user->name},")
                ->line($message)
                ->action('View Listing', route('listings.show', $this->listing->id))
                ->line('Thank you for using TalentForge!');
        } catch (\Exception $e) {
            \Log::error('Error sending mail notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        try {
            $message = "The listing {$this->listing->title} at {$this->listing->company} has been closed so you are in the final cohort!";

            return [
                'url' => route('listings.show', $this->listing->id),
                'message' => $message,
            ];
        } catch (\Exception $e) {
            \Log::error('Error sending database notification: ' . $e->getMessage());
            return [];
        }
    }
}
