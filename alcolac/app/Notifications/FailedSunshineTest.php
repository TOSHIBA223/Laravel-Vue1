<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class FailedSunshineTest extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $answers, $employeeCode)
    {
        $this->user = $user;
        $this->answers = $answers;
        $this->employeeCode = $employeeCode;
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
        $mail_message = (new MailMessage)
                ->subject($this->user . ' #' . $this->employeeCode . ' has failed the COVID19 test')
                ->line($this->user . ' #'. $this->employeeCode .  ' has failed our COVID19 test');
        foreach ($this->answers as $answer) {
            $mail_message->line($answer['definition'] . ': ' . $answer['answer']);
        }
        return $mail_message;
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
