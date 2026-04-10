<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Events\TaskCreated;

class SendTaskCreatedEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskCreated $event)
    {
      $task=$event->task;
      Mail::raw("Task Created: {$task->title}",
      function ($message) use ($task){
        $message->to($task->user->email)
        ->subject('New Task Created');
      });
    }
}
