<?php

namespace App\Jobs;

use App\Mail\LikeThresholdNotification;
use App\Models\Person;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendLikeThresholdNotification implements ShouldQueue
{
    use Queueable;

    public Person $person;

    /**
     * Create a new job instance.
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminEmail = config('mail.from.address');
        
        Mail::to($adminEmail)->send(
            new LikeThresholdNotification($this->person)
        );
    }
}
