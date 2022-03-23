<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncorrectAddress extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $employeeCode, $oldAddress, $newAddress)
    {
        $this->user = $user;
        $this->employeeCode = $employeeCode;
        if ($oldAddress) {
            if ($oldAddress->suburb) {
                $this->oldAddress = $oldAddress->address . ', ' . $oldAddress->suburb . ' ' . $oldAddress->state . ' ' . $oldAddress->post_code. ', Australia';
            } else {
                $this->oldAddress = $oldAddress->address . ', Australia';
            }
        }
        $this->newAddress = $newAddress->address . ($newAddress->suburb ? ', ' . $newAddress->suburb . ' ' . $newAddress->state . ' ' . $newAddress->post_code . ', Australia' : ', Australia');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->user . ' #' . $this->employeeCode . ' has an incorrect address')
            ->line($this->user . ' #' . $this->employeeCode . ' has updated their address')
            ->line('Old Address: ' . $this->oldAddress)
            ->line('New Address: ' . $this->newAddress);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
