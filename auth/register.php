<?php
require '../config.php';
require 'enviar_otp.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $telefono = trim($_POST["telefono"]);
    $password = $_POST["password"];

    if ($nombre && $email && $password) {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $otp = rand(100000, 999999);
        $expira = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $stmt = $pdo->prepare("INSERT INTO usuarios 
            (nombre, email, telefono, password, otp_codigo, otp_expira) 
            VALUES (:nombre, :email, :telefono, :password, :otp, :expira)");

        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':telefono' => $telefono,
            ':password' => $passwordHash,
            ':otp' => $otp,
            ':expira' => $expira
        ]);

        enviarOTP($email, $otp);

        $_SESSION['verificar_email'] = $email;

        header("Location: verify.php");
        exit();

    } else {
        $error = "Todos los campos son obligatorios";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Registro - X91</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-md-5">

<div class="card bg-secondary shadow">
<div class="card-body">

<h2 class="text-center mb-2 fw-bold">X91</h2>
<p class="text-center small">Innovación segura para tu futuro digital</p>

<?php if ($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">

<input type="text" name="nombre" class="form-control mb-3" placeholder="Nombre completo" required>
<input type="email" name="email" class="form-control mb-3" placeholder="Correo electrónico" required>
<input type="text" name="telefono" class="form-control mb-3" placeholder="Número de teléfono">
<input type="password" name="password" class="form-control mb-3" placeholder="Contraseña" required>

<button class="btn btn-light w-100">Crear cuenta</button>

</form>

<div class="text-center mt-3">
<a href="login.php" class="text-light">Ya tengo cuenta</a>
</div>

</div>
</div>

</div>
</div>
</div>

</body>
</html>
