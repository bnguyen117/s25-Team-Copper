<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\ResetPassword;

/**
 * Email for resetting user passwords.
 */
class CustomResetPassword extends ResetPassword
{
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable, $this->token);
        return (new MailMessage)
            ->subject('Reset Your Password')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We received a request to reset your password for CreditTrax.')
            ->action('Reset Password', $url)
            ->line('This link will expire in ' . config('auth.passwords.users.expire', 60) . ' minutes.')
            ->line('If you did not request this password reset, please contact support.')
            ->salutation('Thanks, The CreditTrax Team');
    }
}
