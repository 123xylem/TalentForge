<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MessageRecieved extends Notification implements ShouldQueue
{
    use Queueable;
    protected $sender;
    /**
     * Create a new notification instance.
     */
    public function __construct(public $message, public $conversationId, public $senderId, public $createdAt)
    {
        $this->message = $message;
        $this->conversationId = $conversationId;
        $this->senderId = $senderId;
        $this->createdAt = $createdAt;
        $this->sender = User::find($senderId);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('You have a new message from ' . $this->sender->name)
            ->line($this->message)
            ->action('View Message', url('/'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'content' => $this->message,
            'conversationId' => $this->conversationId,
            'user_id' => $this->senderId,
            'created_at' => $this->createdAt,
            'message' => 'New Message recieved from ' . $this->sender->name,
            'open_message' => true,
        ];
    }
}
