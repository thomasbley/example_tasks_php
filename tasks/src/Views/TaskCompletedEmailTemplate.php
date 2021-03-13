<?php

/**
 * @var TaskService\Services\EmailService $this
 * @var TaskService\Views\TaskCompletedEmail $email
 */
$service = $this;

$email->to = $email->customer->email;

$email->from = 'Task Service <task.service@invalid.local>';

$email->subject = sprintf('Task #%s completed', $email->task->id);

?>
<!DOCTYPE html>
<html>
    <body>
    	Task <b><?= $service->e($email->task->title); ?></b> completed!
    </body>
</html>