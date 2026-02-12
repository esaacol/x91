<?php
require '../config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($nombre && $email && $password) {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password)
                               VALUES (:nombre, :email, :password)");
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $passwordHash
        ]);

        header("Location: login.php");
        exit();

    } else {
        $error = "Todos los campos son obligatorios";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-4">

            <div class="card shadow">
                <div class="card-body">
                    <h4 class="text-center mb-4">Crear Cuenta</h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
                        </div>

                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>

                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>

                        <button class="btn btn-success w-100">Registrarse</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="login.php">Ya tengo cuenta</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
