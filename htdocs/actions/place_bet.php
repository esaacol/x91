<?php
include "../config/config.php";

if (!isset($_SESSION['user_id'])) {
    die("No autorizado");
}

$user_id = $_SESSION['user_id'];
$match_id = $_POST['match_id'];
$selection = $_POST['selection'];
$odds = $_POST['odds'];
$stake = $_POST['stake'];

$conn->begin_transaction();

try {

    // Obtener saldo actual
    $result = $conn->query("SELECT balance FROM wallets WHERE user_id = $user_id FOR UPDATE");
    $wallet = $result->fetch_assoc();
    $balance = $wallet['balance'];

    if ($balance < $stake) {
        throw new Exception("Saldo insuficiente");
    }

    // Descontar saldo
    $new_balance = $balance - $stake;
    $conn->query("UPDATE wallets SET balance = $new_balance WHERE user_id = $user_id");

    // Insertar apuesta
    $stmt = $conn->prepare("INSERT INTO bets (user_id, match_id, selection, odds, stake) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issdd", $user_id, $match_id, $selection, $odds, $stake);
    $stmt->execute();

    $conn->commit();

    echo "Apuesta realizada correctamente";

} catch (Exception $e) {

    $conn->rollback();
    echo $e->getMessage();
}
?>
