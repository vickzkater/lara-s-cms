<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

// Model
use App\Models\system\SysUser;

class MailTester extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var User
     */
    public $user;

    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SysUser $sys_user, $subject)
    {
        $this->user = $sys_user;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->view('emails.tester')
            ->text('emails.tester_plain')
            ->with([
                'subject' => $this->subject,
                'name' => $this->user->name,
                'action_url' => route('web.home')
            ]);
    }
}
