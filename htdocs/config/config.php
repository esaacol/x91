<?php
// Datos EXACTOS del panel de InfinityFree
$host = "sql107.infinityfree.com";
$dbname = "if0_41135493_x91";
$username = "if0_41135493";
$password = "yTYXV6Hi4v";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {
    die("Error real: " . $e->getMessage());
}
