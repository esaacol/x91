<?php
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
$password = $data["password"] ?? null;

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Datos inválidos"]);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);
$userId = bin2hex(random_bytes(16));
$walletId = bin2hex(random_bytes(16));

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO users (id, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $email, $passwordHash]);

    $stmt = $pdo->prepare("INSERT INTO wallets (id, user_id, currency, available_balance, locked_balance) VALUES (?, ?, 'EUR', 0, 0)");
    $stmt->execute([$walletId, $userId]);

    $pdo->commit();

    echo json_encode(["message" => "Usuario creado correctamente"]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["error" => "Error interno"]);
}
