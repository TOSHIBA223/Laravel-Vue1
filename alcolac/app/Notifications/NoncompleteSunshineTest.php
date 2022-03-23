<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class NoncompleteSunshineTest extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($users, $fileName)
    {
        $this->users = $users;
        $this->fileName = $fileName;
        $this->filePath = Storage::disk('sunshine_local')->path($fileName);
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
        $user_string = '';
        foreach($this->users as $user)
            $user_string .= '#' . $user['employee_code'] . ': ' . $user['name'] . " \n ";

        return (new MailMessage)
            ->subject('The following users have not completed the COVID19 test')
            ->line('The following users have not completed the COVID19 test:')
            ->line($user_string)
            ->attach($this->filePath, [
                'as' => $this->fileName,
                'mime' => 'text/csv',
            ]);
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
