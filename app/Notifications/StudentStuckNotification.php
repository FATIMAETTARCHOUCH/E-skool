<?php

namespace App\Notifications;

use App\Models\Chapter;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentStuckNotification extends Notification
{
    use Queueable;

    public function __construct(protected User $student, protected Chapter $chapter) { }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Student stuck on chapter')
            ->line("Student {$this->student->first_name} {$this->student->last_name} is stuck on chapter: {$this->chapter->title}.")
            ->action('View chapter', url('/admin/progress'));
    }

    public function toArray($notifiable)
    {
        return [
            'student_id' => $this->student->id,
            'chapter_id' => $this->chapter->id,
            'message' => "Student is stuck on chapter: {$this->chapter->title}",
        ];
    }
}
