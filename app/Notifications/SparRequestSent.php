<?php

namespace App\Notifications;

use App\Models\SparRequest;
use App\Models\Fighter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SparRequestSent extends Notification implements ShouldQueue
{
    use Queueable;

    public $sparRequest;
    public $sender;

    /**
     * Create a new notification instance.
     */
    public function __construct(SparRequest $sparRequest, Fighter $sender)
    {
        $this->sparRequest = $sparRequest;
        $this->sender = $sender;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $senderName = $this->sender->name;
        $receiverName = $notifiable->name;

        return (new MailMessage)
            ->subject("New Sparring Request from {$senderName}")
            ->greeting("Hi {$receiverName}!")
            ->line("{$senderName} has sent you a sparring request on Ngumi Network.")
            ->line("**Request Details:**")
            ->line("**From:** {$senderName}")
            ->line("**Message:** " . ($this->sparRequest->message ?: 'No message provided'))
            ->line("**Date:** " . $this->sparRequest->created_at->format('M d, Y H:i'))
            ->action('View Spar Request', url('/spar-requests'))
            ->line('You can accept, reject, or respond to this request through your dashboard.')
            ->salutation('Best regards, Ngumi Network Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'spar_request_id' => $this->sparRequest->id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'message' => $this->sparRequest->message,
            'type' => 'spar_request_sent',
        ];
    }
}
