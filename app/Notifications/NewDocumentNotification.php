<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDocumentNotification extends Notification
{
    use Queueable;


    public function __construct($main_doc_id)
    {
        $this->main_doc_id = $main_doc_id;
    }


    public function via($notifiable)
    {
        return ['database'];
    }


    public function toArray($notifiable)
    {
        return [
            'main_doc_id' => $this->main_doc_id,
        ];
    }
}
