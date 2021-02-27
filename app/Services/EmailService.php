<?php

namespace TaskService\Services;

use InvalidArgumentException;
use TaskService\Models\Email;

class EmailService
{
    public function sendEmail(Email $email): void
    {
        $content = $this->renderTemplate($email);

        $headers = [
            'From' => $email->from,
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Transfer-Encoding' => 'quoted-printable',
        ];

        $subject = '=?UTF-8?Q?' . quoted_printable_encode($email->subject) . '?=';

        if (!mail($email->to, $subject, quoted_printable_encode($content), $headers)) {
            $message = sprintf('failed to send %s to %s', $email->template, $email->to);

            trigger_error($message, E_USER_WARNING);
        }
    }

    public function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
    }

    protected function renderTemplate(Email $email): string
    {
        if (empty($email->template)) {
            throw new InvalidArgumentException('missing template');
        }
        if (!file_exists($email->template)) {
            throw new InvalidArgumentException('missing template file');
        }

        ob_start();

        require $email->template;

        return trim(ob_get_clean());
    }
}
