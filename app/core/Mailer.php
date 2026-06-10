<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Require PHPMailer files manually since they are not in the autoloader path
require_once __DIR__ . '/../Libraries/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../Libraries/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../Libraries/PHPMailer/src/SMTP.php';

class Mailer
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host       = Env::get('MAIL_HOST', 'smtp.gmail.com');
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = Env::get('MAIL_USERNAME');
            $this->mail->Password   = Env::get('MAIL_PASSWORD');
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
            $this->mail->Port       = Env::get('MAIL_PORT', 465);

            // Set Hostname and Return-Path (Sender) to match sending domain to prevent spam filters
            $mailUsername = Env::get('MAIL_USERNAME');
            $domain = substr(strrchr($mailUsername, "@"), 1);
            $this->mail->Hostname   = $domain ?: 'gmail.com';
            $this->mail->Sender     = $mailUsername;

            // Default sender
            $this->mail->setFrom(Env::get('MAIL_FROM_ADDRESS'), Env::get('MAIL_FROM_NAME'));
            
            // Add automatic submission headers to signify transactional notifications
            $this->mail->addCustomHeader('Auto-Submitted', 'auto-generated');
        } catch (Exception $e) {
            // Handle setup errors if needed
            error_log("Mailer Error: {$this->mail->ErrorInfo}");
        }
    }

    public function send($to, $subject, $body)
    {
        try {
            $this->mail->clearAddresses(); // Clear recipient addresses from previous sends
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            
            // Set text fallback to prevent spam filters from penalizing the email
            $this->mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '</p>', '</div>'], ["\n", "\n", "\n\n", "\n\n"], $body));

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
        }
    }
}
