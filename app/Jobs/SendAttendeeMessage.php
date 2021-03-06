<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\AttendeeMessageEmail;
use Illuminate\Support\Facades\Mail;

class SendAttendeeMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $attendeeMessage;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($attendeeMessage)
    {
        $this->attendeeMessage = $attendeeMessage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $this->attendeeMessage->recipients()->each(function ($recipient) {
        //     Mail::to($recipient)->send(new AttendeeMessageEmail($this->attendeeMessage));
        // });

        $this->attendeeMessage->withChunkedRecipients(20, function ($emails) {
            $emails->each(function ($email) {
                Mail::to($email)->queue(new AttendeeMessageEmail($this->attendeeMessage));
            });
        });
    }
}
