<?php

namespace App\Notifications;

use App\Models\SparRequest;
use App\Models\Fighter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SparRequestCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public $sparRequest;
    public $canceller;

    /**
     * Create a new notification instance.
     */
    public function __construct(SparRequest $sparRequest, Fighter $canceller)
    {
        $this->sparRequest = $sparRequest;
        $this->canceller = $canceller;
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
        $cancellerName = $this->canceller->name;
        $receiverName = $notifiable->name;

        return (new MailMessage)
            ->subject("Sparring Request Cancelled by {$cancellerName}")
            ->greeting("Hi {$receiverName},")
            ->line("{$cancellerName} has cancelled their sparring request to you on Ngumi Network.")
            ->line("**Request Details:**")
            ->line("**Cancelled by:** {$cancellerName}")
            ->line("**Original Message:** " . ($this->sparRequest->message ?: 'No message provided'))
            ->line("**Cancelled on:** " . now()->format('M d, Y H:i'))
            ->action('Find Other Partners', url('/directory'))
            ->line('This could be due to scheduling conflicts or other commitments.')
            ->line('Feel free to reach out to other fighters in the directory.')
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
            'canceller_id' => $this->canceller->id,
            'canceller_name' => $this->canceller->name,
            'message' => $this->sparRequest->message,
            'type' => 'spar_request_cancelled',
        ];
    }
}
