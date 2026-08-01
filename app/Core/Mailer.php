<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    /**
     * Bangun badan email HTML dengan kerangka standar IC-ASSIST.
     *
     * Sebelumnya kerangka ini (header, kartu putih, garis pemisah, footer
     * copyright) disalin utuh di TIGA tempat — AuthController 2x dan
     * ForgotPasswordController 1x — dan hanya berbeda pada kalimat pembuka,
     * isi tengah, serta catatan kaki. Mengubah warna atau tahun copyright
     * dulu berarti menyunting tiga berkas.
     *
     * @param string $intro   kalimat pembuka (boleh mengandung HTML)
     * @param string $center  bagian tengah — kotak OTP atau tombol tautan
     * @param string $note    catatan masa berlaku
     * @param string $warning catatan kecil "abaikan bila bukan Anda"
     */
    public static function buildHtml(string $intro, string $center, string $note, string $warning = ''): string
    {
        $warningHtml = $warning !== ''
            ? "<p style='font-size: 12px; color: #adb5bd;'>{$warning}</p>"
            : '';

        return "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;'>
                <div style='background-color: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center;'>
                    <h2 style='color: #0097d9; margin: 0 0 5px 0;'>IC-ASSIST</h2>
                    <p style='color: #6c757d; margin: 0 0 30px 0; font-size: 14px;'>Sistem Pendaftaran Calon Asisten</p>

                    <div style='text-align: left; color: #333333;'>
                        <p>Halo,</p>
                        <p>{$intro}</p>
                    </div>

                    <div style='margin: 30px 0;'>
                        {$center}
                    </div>

                    <div style='text-align: left; color: #6c757d; font-size: 13px; border-top: 1px solid #eeeeee; padding-top: 20px;'>
                        <p>{$note}</p>
                        {$warningHtml}
                    </div>

                    <div style='margin-top: 30px; font-size: 11px; color: #adb5bd;'>
                        &copy; " . date('Y') . " IC-ASSIST All rights reserved
                    </div>
                </div>
            </div>
        ";
    }

    /** Kotak kode OTP untuk bagian tengah email. */
    public static function otpBox(string $otp): string
    {
        return "<div style='background-color: #f1f3f5; color: #0f172a; padding: 15px 30px; border-radius: 10px; font-weight: bold; font-size: 32px; display: inline-block; letter-spacing: 5px;'>{$otp}</div>";
    }

    /** Tombol tautan untuk bagian tengah email. */
    public static function linkButton(string $url, string $label): string
    {
        return "<a href='{$url}' style='background-color: #0097d9; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 25px; font-weight: bold; display: inline-block;'>{$label}</a>";
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
