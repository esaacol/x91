<?php
require '../config.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (!$nombre || !$email || !$password) {
        $mensaje = "Todos los campos son obligatorios.";
    } else {

        // Verificar si ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $mensaje = "El correo ya está registrado.";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $otp = rand(100000, 999999);
            $expira = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            $stmt = $pdo->prepare("
                INSERT INTO usuarios (nombre, email, password, otp_code, otp_expiration)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([$nombre, $email, $hash, $otp, $expira]);

            // ==========================
            // ENVÍO EMAIL CON RESEND API
            // ==========================

            $apiKey = getenv("RESEND_API_KEY");

            $htmlEmail = "
            <div style='margin:0;padding:0;background:#0f2027;font-family:Segoe UI,Arial,sans-serif'>
                <div style='max-width:600px;margin:40px auto;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.2)'>
                    
                    <div style='background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);padding:30px;text-align:center;color:white'>
                        <h1 style='margin:0;font-size:32px;letter-spacing:4px'>X91</h1>
                        <p style='margin-top:10px;opacity:0.8'>La nueva generación digital</p>
                    </div>

                    <div style='padding:40px;text-align:center'>
                        <h2 style='margin-bottom:20px;color:#203a43'>Verifica tu cuenta</h2>
                        <p style='font-size:16px;color:#555'>
                            Hola <strong>$nombre</strong>, gracias por unirte a X91.
                        </p>
                        <p style='color:#555'>
                            Usa el siguiente código para activar tu cuenta:
                        </p>

                        <div style='margin:30px 0;font-size:36px;font-weight:bold;letter-spacing:8px;color:#0072ff'>
                            $otp
                        </div>

                        <p style='color:#888;font-size:14px'>
                            Este código expirará en 10 minutos.
                        </p>

                        <hr style='margin:30px 0;border:none;border-top:1px solid #eee'>

                        <p style='font-size:13px;color:#aaa'>
                            Si no creaste esta cuenta, puedes ignorar este mensaje.
                        </p>
                    </div>

                    <div style='background:#f4f6f8;padding:20px;text-align:center;font-size:12px;color:#888'>
                        © " . date("Y") . " X91 Technologies. Todos los derechos reservados.
                    </div>

                </div>
            </div>
            ";

            $data = [
                "from" => "X91 <onboarding@resend.dev>",
                "to" => [$email],
                "subject" => "Activa tu cuenta en X91",
                "html" => $htmlEmail
            ];

            $ch = curl_init("https://api.resend.com/emails");

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer $apiKey",
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                error_log("Curl error: " . curl_error($ch));
            }

            curl_close($ch);

            header("Location: verify.php?email=" . urlencode($email));
            exit();
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
    <p class="slogan">Crea tu cuenta y evoluciona.</p>

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
