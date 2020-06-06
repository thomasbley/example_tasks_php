<?php

namespace TaskService\Test\Unit\Services;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TaskService\Models\Customer;
use TaskService\Models\Email;
use TaskService\Models\Task;
use TaskService\Services\EmailService;
use TaskService\Services\ServicesMocks;
use TaskService\Views\TaskCompletedEmail;

class EmailServiceTest extends TestCase
{
    protected Customer $customer;
    protected Task $task;

    public function setUp(): void
    {
        require_once __DIR__ . '/ServicesMocks.php';

        $this->customer = new Customer();
        $this->customer->id = 42;
        $this->customer->email = 'foo.receiver@example.com';

        $this->task = new Task();
        $this->task->id = 41;
        $this->task->title = 'test';

    }

    public function testSendEmail(): void
    {
        ServicesMocks::$mailReturn = true;

        $email = new TaskCompletedEmail();
        $email->customer = $this->customer;
        $email->subject = 'Test Email';
        $email->from = 'foo.sender@example.com';
        $email->task = $this->task;

        $service = new EmailService();
        $service->sendEmail($email);

        $params = ServicesMocks::$mailParams;

        $this->assertStringContainsString($this->customer->email, $params[0]);
        $this->assertSame($email->subject, $params[1]);
        $this->assertStringContainsString('Task <b>test</b> completed!', $params[2]);
        $this->assertSame($email->from, $params[3]['From']);
    }

    public function testSendEmailMissingTemplate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('missing template');

        $email = new Email();
        $email->template = '';

        $service = new EmailService();
        $service->sendEmail($email);
    }

    public function testSendEmailFailure(): void
    {
        $this->expectWarning();
        $this->expectWarningMessage('failed to send');

        ServicesMocks::$mailReturn = false;

        $email = new TaskCompletedEmail();
        $email->customer = $this->customer;
        $email->task = $this->task;

        $service = new EmailService();
        $service->sendEmail($email);

        $this->assertTrue(true);
    }
}
