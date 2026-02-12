<?php
$host = "dpg-d66krl06fj8s7397t3lg-a.oregon-postgres.render.com";
$dbname = "x91_db";
$user = "x91_db_user";
$password = "at26s0tdPHR6rb9ckPotpNLIMJoypc61";
$port = "5432";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
