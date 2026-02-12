<?php

$host = "sql107.infinityfree.com";
$dbname = "if0_41135493_x91";
$username = "if0_41135493";
$password = "yTYXV6Hi4v";

echo "<h2>Probando conexión real...</h2>";

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );

    echo "<h3 style='color:green;'>✅ Conectado correctamente</h3>";

    $stmt = $pdo->query("SELECT 1");
    echo "<p>Consulta test OK</p>";

} catch (PDOException $e) {

    echo "<h3 style='color:red;'>❌ Error real:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";

}

