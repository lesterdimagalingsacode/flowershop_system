<?php

// core/Mailer.php
// ─────────────────────────────────────────────────────────────────────────────
// Reusable mailer. Supports php mail() and PHPMailer (SMTP).
// Switch driver in config/config.php:
//
//   define('MAIL_DRIVER', 'mail');       // default, zero setup
//   define('MAIL_DRIVER', 'phpmailer'); // for InfinityFree SMTP
//
// PHPMailer SMTP config (only needed when MAIL_DRIVER = 'phpmailer'):
//   define('MAIL_HOST',       'smtp.gmail.com');
//   define('MAIL_PORT',       587);
//   define('MAIL_USERNAME',   'your@gmail.com');
//   define('MAIL_PASSWORD',   'your-app-password');
//   define('MAIL_ENCRYPTION', 'tls');   // 'tls' or 'ssl'
//   define('MAIL_FROM_EMAIL', 'your@gmail.com');
//   define('MAIL_FROM_NAME',  'Petal & Soul');
// ─────────────────────────────────────────────────────────────────────────────

class Mailer
{
    private string $driver;
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->driver    = defined('MAIL_DRIVER')    ? MAIL_DRIVER    : 'mail';
        $this->fromEmail = defined('MAIL_FROM_ADDRESS') ? MAIL_FROM_ADDRESS : 'no-reply@petalsoul.com';
        $this->fromName  = defined('MAIL_FROM_NAME')  ? MAIL_FROM_NAME  : 'Petal & Soul';
    }

    // ─── Public send() ────────────────────────────────────────────────────────
    // $view  : path relative to app/views/  e.g. 'emails/order-confirmation'
    // $data  : variables made available inside the view template
    // Returns true on success, false on failure.

    public function send(
        string $to,
        string $toName,
        string $subject,
        string $view,
        array  $data = []
    ): bool {
        $html = $this->renderView($view, $data);

        return $this->driver === 'phpmailer'
            ? $this->sendViaPHPMailer($to, $toName, $subject, $html)
            : $this->sendViaMail($to, $toName, $subject, $html);
    }

    // ─── Driver: php mail() ───────────────────────────────────────────────────

    private function sendViaMail(
        string $to,
        string $toName,
        string $subject,
        string $html
    ): bool {
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        $headers .= "Reply-To: {$this->fromEmail}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        $recipient = "{$toName} <{$to}>";

        return @mail($recipient, $subject, $html, $headers);
    }

    // ─── Driver: PHPMailer (SMTP) ─────────────────────────────────────────────

    private function sendViaPHPMailer(
        string $to,
        string $toName,
        string $subject,
        string $html
    ): bool {
        // PHPMailer must be installed:
        //   composer require phpmailer/phpmailer
        // OR manually place PHPMailer files in /vendor/phpmailer/

        if (! class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log('[Mailer] PHPMailer not found. Run: composer require phpmailer/phpmailer');
            return false;
        }

        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            // SMTP config
            $mail->isSMTP();
            $mail->Host       = defined('MAIL_HOST')       ? MAIL_HOST       : 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = defined('MAIL_USERNAME')   ? MAIL_USERNAME   : '';
            $mail->Password   = defined('MAIL_PASSWORD')   ? MAIL_PASSWORD   : '';
            $mail->SMTPSecure = defined('MAIL_ENCRYPTION') ? MAIL_ENCRYPTION : 'tls';
            $mail->Port       = defined('MAIL_PORT')       ? MAIL_PORT       : 587;

            // Sender + recipient
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to, $toName);
            $mail->addReplyTo($this->fromEmail, $this->fromName);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = strip_tags($html); // plain-text fallback

            $mail->send();
            return true;

        } catch (\Exception $e) {
            error_log('[Mailer] PHPMailer error: ' . $e->getMessage());
            return false;
        }
    }

    // ─── View renderer ────────────────────────────────────────────────────────
    // Renders app/views/{$view}.php with $data extracted into local variables.

    private function renderView(string $view, array $data = []): string
    {
        $viewPath = BASE_PATH . "/app/views/{$view}.php";

        if (! file_exists($viewPath)) {
            error_log("[Mailer] Email view not found: {$viewPath}");
            return '';
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
}