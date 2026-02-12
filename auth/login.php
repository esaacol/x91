<?php
require '../config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        if (!$user['verificado']) {
            $error = "Debes verificar tu correo antes de iniciar sesión.";
        }

        elseif (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];

            header("Location: ../dashboard.php");
            exit();
        }

        else {
            $error = "Credenciales incorrectas.";
        }

    } else {
        $error = "Credenciales incorrectas.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login - X91</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    min-height: 100vh;
}
.brand {
    font-size: 2.5rem;
    font-weight: 700;
    letter-spacing: 3px;
}
.slogan {
    font-size: 0.9rem;
    opacity: 0.8;
}
.card {
    border-radius: 15px;
}
</style>
</head>

<body class="d-flex align-items-center justify-content-center text-light">

<div class="col-md-4">

<div class="card bg-dark shadow-lg p-4">

<div class="text-center mb-4">
<div class="brand">X91</div>
<div class="slogan">Innovación segura para tu futuro digital</div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger text-center">
<?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
</div>

<div class="mb-3">
<input type="password" name="password" class="form-control" placeholder="Contraseña" required>
</div>

<button class="btn btn-primary w-100">Iniciar Sesión</button>

</form>

<div class="text-center mt-3">
<a href="register.php" class="text-light">Crear cuenta</a>
</div>

</div>

</div>

</body>
</html>
