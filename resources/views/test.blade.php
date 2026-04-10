namespace App\Listeners;

use App\Events\TaskCreated;
use Illuminate\Support\Facades\Mail;

class SendTaskCreatedEmail
{
    public function handle(TaskCreated $event)
    {
        $task = $event->task;

        Mail::raw(
            "New Task Created: " . $task->title,
            function ($message) use ($task) {
                $message->to($task->user->email)
                        ->subject('Task Created Successfully');
            }
        );
    }
}