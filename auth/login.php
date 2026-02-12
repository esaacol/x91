<?php
require '../config.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $mensaje = "Credenciales incorrectas.";
    } elseif (!$user["email_verificado"]) {
        $mensaje = "Debes verificar tu correo.";
    } elseif (!password_verify($password, $user["password"])) {
        $mensaje = "Credenciales incorrectas.";
    } else {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["nombre"] = $user["nombre"];
        header("Location: ../dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>X91 - Login</title>
<link rel="stylesheet" href="../assets/auth.css">
</head>
<body>

<div class="auth-container">
    <h1 class="logo">X91</h1>
    <p class="slogan">Accede a tu mundo digital.</p>

    <?php if($mensaje): ?>
        <div class="error"><?= $mensaje ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Iniciar Sesión</button>
    </form>

    <a href="register.php">Crear nueva cuenta</a>
</div>

</body>
</html>
