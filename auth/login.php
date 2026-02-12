<?php
require '../config.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {

        if (!$user["email_verificado"]) {
            $mensaje = "Debes verificar tu cuenta primero.";
        } elseif (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["nombre"] = $user["nombre"];

            header("Location: ../dashboard.php");
            exit();

        } else {
            $mensaje = "Credenciales incorrectas.";
        }

    } else {
        $mensaje = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login - X91</title>
</head>
<body>

<h2>Iniciar Sesión</h2>

<?php
if(isset($_GET["success"])) {
    echo "<p>Cuenta verificada correctamente.</p>";
}
if($mensaje) {
    echo "<p>$mensaje</p>";
}
?>

<form method="POST">
<input type="email" name="email" placeholder="Correo" required><br>
<input type="password" name="password" placeholder="Contraseña" required><br>
<button type="submit">Entrar</button>
</form>

<a href="register.php">Crear cuenta</a>

</body>
</html>
