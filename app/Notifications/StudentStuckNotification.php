<?php

namespace App\Notifications;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentStuckNotification extends Notification
{
    use Queueable;

    public function __construct(protected User $student, protected Lesson $lesson) { }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Student stuck on lesson')
            ->line("Student {$this->student->first_name} {$this->student->last_name} is stuck on lesson: {$this->lesson->title}.")
            ->action('View lesson', url('/admin/progress'));
    }

    public function toArray($notifiable)
    {
        return [
            'student_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'message' => "Student is stuck on lesson: {$this->lesson->title}",
        ];
    }
}
