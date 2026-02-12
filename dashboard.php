<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">
            Bienvenido <?= htmlspecialchars($_SESSION['nombre']) ?>
        </span>
        <a href="auth/logout.php" class="btn btn-danger">Cerrar sesión</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <h3>Panel Principal</h3>
            <p>Tu sistema ya está funcionando correctamente en Render 🚀</p>
        </div>
    </div>
</div>

</body>
</html>
