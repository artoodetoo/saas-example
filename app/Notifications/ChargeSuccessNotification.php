<?php

namespace App\Notifications;

use App\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class ChargeSuccessNotification extends Notification
{
    use Queueable;

    private $payment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('You have been charged $' . cents($this->payment->total))
            ->line('Thank you for using our application!');

        $filename = storage_path('app/invoices/' . $this->payment->id . '.pdf');
        if (file_exists($filename)) {
            $message->attach($filename);
        }
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->from('Ghost', ':ghost:')
            ->content('Someone have been charged')
            ->attachment(function ($attachment) {
                $username = $this->payment->user->name ?? 'unknown';
                $attachment->title('Payment Successful')
                    ->fields([
                        'StripeID' => $this->payment->stripe_id,
                        'User' => $username,
                        'Amount' => '$' . cents($this->payment->total),
                    ]);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
