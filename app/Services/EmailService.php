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

        if (!mail($email->customer->email, $email->subject, quoted_printable_encode($content), $headers)) {
            $message = sprintf('failed to send %s to customer-id %s', $email->template, $email->customer->id);

            trigger_error($message, E_USER_WARNING);
        }
    }

    public function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
    }

    /**
     * @psalm-suppress PossiblyUnusedParam
     * @psalm-suppress UnresolvableInclude
     */
    protected function renderTemplate(Email $email): string
    {
        if (empty($email->template)) {
            throw new InvalidArgumentException('missing template');
        }

        ob_start();

        require $email->template;

        return trim(ob_get_clean());
    }
}
