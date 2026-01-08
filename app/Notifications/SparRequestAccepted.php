<?php

namespace App\Notifications;

use App\Models\SparRequest;
use App\Models\Fighter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SparRequestAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    public $sparRequest;
    public $acceptor;

    /**
     * Create a new notification instance.
     */
    public function __construct(SparRequest $sparRequest, Fighter $acceptor)
    {
        $this->sparRequest = $sparRequest;
        $this->acceptor = $acceptor;
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
        $acceptorName = $this->acceptor->name;
        $senderName = $notifiable->name;

        return (new MailMessage)
            ->subject("Sparring Request Accepted by {$acceptorName}")
            ->greeting("Hi {$senderName}!")
            ->line("Great news! {$acceptorName} has accepted your sparring request on Ngumi Network.")
            ->line("**Request Details:**")
            ->line("**Accepted by:** {$acceptorName}")
            ->line("**Original Message:** " . ($this->sparRequest->message ?: 'No message provided'))
            ->line("**Accepted on:** " . $this->sparRequest->responded_at->format('M d, Y H:i'))
            ->action('View Spar Details', url('/spar-requests'))
            ->line('You can now coordinate your sparring session through the platform.')
            ->line('Remember to follow safety protocols and respect your training partner.')
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
            'acceptor_id' => $this->acceptor->id,
            'acceptor_name' => $this->acceptor->name,
            'message' => $this->sparRequest->message,
            'type' => 'spar_request_accepted',
        ];
    }
}
