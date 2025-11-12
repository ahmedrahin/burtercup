<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Log;

class MobileNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $type;
    public $user;
    /**
     * Create a new notification instance.
     */
    public function __construct($title,$message,$type,$user)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $data = [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type
        ];

        //notify user in firebase
        if ($this->user->firebaseTokens) {
            foreach ($this->user->firebaseTokens->where('is_active', 1) as $firebaseToken) {
                Helper::sendNotifyMobile($firebaseToken->token, $data);
            }
        }

        return $data;
    }
}
