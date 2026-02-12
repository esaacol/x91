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

        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $mensaje = "El correo ya está registrado.";
        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $otp = rand(100000, 999999);
            $expira = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            $stmt = $pdo->prepare("
                INSERT INTO usuarios (nombre, email, password, otp_code, otp_expiration, email_verificado)
                VALUES (?, ?, ?, ?, ?, false)
            ");
            $stmt->execute([$nombre, $email, $hash, $otp, $expira]);

            $apiKey = getenv("RESEND_API_KEY");

            $htmlEmail = "
            <h2>Bienvenido a X91</h2>
            <p>Tu código de verificación es:</p>
            <h1>$otp</h1>
            <p>Expira en 10 minutos.</p>
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
            curl_exec($ch);
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
<title>Registro - X91</title>
</head>
<body>
<h2>Crear cuenta</h2>

<?php if($mensaje) echo "<p>$mensaje</p>"; ?>

<form method="POST">
<input type="text" name="nombre" placeholder="Nombre" required><br>
<input type="email" name="email" placeholder="Correo" required><br>
<input type="password" name="password" placeholder="Contraseña" required><br>
<button type="submit">Crear Cuenta</button>
</form>

<a href="login.php">Ir a Login</a>

</body>
</html>
