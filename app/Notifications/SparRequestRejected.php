<?php

namespace App\Notifications;

use App\Models\SparRequest;
use App\Models\Fighter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SparRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $sparRequest;
    public $rejector;

    /**
     * Create a new notification instance.
     */
    public function __construct(SparRequest $sparRequest, Fighter $rejector)
    {
        $this->sparRequest = $sparRequest;
        $this->rejector = $rejector;
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
        $rejectorName = $this->rejector->name;
        $senderName = $notifiable->name;

        return (new MailMessage)
            ->subject("Sparring Request Declined by {$rejectorName}")
            ->greeting("Hi {$senderName},")
            ->line("{$rejectorName} has declined your sparring request on Ngumi Network.")
            ->line("**Request Details:**")
            ->line("**Declined by:** {$rejectorName}")
            ->line("**Original Message:** " . ($this->sparRequest->message ?: 'No message provided'))
            ->line("**Declined on:** " . $this->sparRequest->responded_at->format('M d, Y H:i'))
            ->action('Find Other Partners', url('/directory'))
            ->line('Don\'t be discouraged! There are many other fighters looking to spar.')
            ->line('Try adjusting your search criteria or reaching out to different fighters.')
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
            'rejector_id' => $this->rejector->id,
            'rejector_name' => $this->rejector->name,
            'message' => $this->sparRequest->message,
            'type' => 'spar_request_rejected',
        ];
    }
}
