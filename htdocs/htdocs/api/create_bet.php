<?php
require_once "../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$userId = $data["user_id"] ?? null;
$matchId = $data["match_id"] ?? null;
$selected = $data["selected_outcome"] ?? null;
$stake = floatval($data["stake"] ?? 0);

if (!$userId || !$matchId || !in_array($selected, ["home","draw","away"]) || $stake <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Datos inválidos"]);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1️⃣ Obtener wallet
    $stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = ? FOR UPDATE");
    $stmt->execute([$userId]);
    $wallet = $stmt->fetch();

    if (!$wallet || $wallet["available_balance"] < $stake) {
        throw new Exception("Saldo insuficiente");
    }

    // 2️⃣ Obtener partido
    $stmt = $pdo->prepare("SELECT * FROM matches WHERE id = ? FOR UPDATE");
    $stmt->execute([$matchId]);
    $match = $stmt->fetch();

    if (!$match || $match["status"] !== "scheduled") {
        throw new Exception("Partido no disponible");
    }

    // 3️⃣ Calcular payout (modelo ejemplo 1.8x fijo contraapuesta)
    $multiplier = 1.8; 
    $potentialPayout = $stake * $multiplier;

    // 4️⃣ Bloquear saldo
    $stmt = $pdo->prepare("
        UPDATE wallets 
        SET available_balance = available_balance - ?, 
            locked_balance = locked_balance + ?
        WHERE user_id = ?
    ");
    $stmt->execute([$stake, $stake, $userId]);

    // 5️⃣ Crear apuesta
    $betId = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("
        INSERT INTO bets (id, user_id, match_id, selected_outcome, stake, potential_payout)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$betId, $userId, $matchId, $selected, $stake, $potentialPayout]);

    $pdo->commit();

    echo json_encode([
        "message" => "Apuesta creada",
        "potential_payout" => $potentialPayout
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
