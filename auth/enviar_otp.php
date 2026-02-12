<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../config.php';

function enviarOTP($email, $codigo) {

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = getenv("MAIL_USER");
        $mail->Password   = getenv("MAIL_PASS");
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom(getenv("MAIL_USER"), 'X91');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verificación X91';

        $mail->Body = "
        <h2 style='font-family:Arial'>X91</h2>
        <p>Tu código de verificación es:</p>
        <h1>$codigo</h1>
        <p>Expira en 10 minutos.</p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}
