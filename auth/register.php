<?php
require '../config.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (!$nombre || !$email || !$password) {
        $mensaje = "Todos los campos son obligatorios.";
    } else {

        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $mensaje = "El email ya está registrado.";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $otp = rand(100000, 999999);
            $expira = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            $stmt = $pdo->prepare("
                INSERT INTO usuarios (nombre, email, password, otp_code, otp_expiration)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([$nombre, $email, $hash, $otp, $expira]);

            // Enviar OTP
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = getenv("MAIL_USER");
                $mail->Password = getenv("MAIL_PASS");
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom(getenv("MAIL_USER"), 'X91');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Verificación X91';
                $mail->Body = "
                    <h2>Bienvenido a X91</h2>
                    <p>Tu código de verificación es:</p>
                    <h1>$otp</h1>
                    <p>Expira en 10 minutos.</p>
                ";

                $mail->send();

                header("Location: verify.php?email=" . urlencode($email));
                exit();

            } catch (Exception $e) {
                $mensaje = "Error enviando OTP.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>X91 - Registro</title>
<link rel="stylesheet" href="../assets/auth.css">
</head>
<body>

<div class="auth-container">
    <h1 class="logo">X91</h1>
    <p class="slogan">La nueva generación digital.</p>

    <?php if($mensaje): ?>
        <div class="error"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre completo" required>
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña segura" required>
        <button type="submit">Crear Cuenta</button>
    </form>

    <a href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
</div>

</body>
</html>
