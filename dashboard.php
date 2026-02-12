<?php
require 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard - X91</title>
</head>
<body>

<h1>Bienvenido <?= $_SESSION["nombre"] ?></h1>

<a href="logout.php">Cerrar sesión</a>

</body>
</html>
