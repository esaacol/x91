<?php
require_once "../config/db.php";

try {

    $stmt = $pdo->prepare("
        SELECT * FROM matches 
        WHERE status = 'finished'
    ");
    $stmt->execute();
    $matches = $stmt->fetchAll();

    foreach ($matches as $match) {

        $stmt = $pdo->prepare("
            SELECT * FROM bets 
            WHERE match_id = ? AND status = 'pending'
            FOR UPDATE
        ");
        $stmt->execute([$match["id"]]);
        $bets = $stmt->fetchAll();

        foreach ($bets as $bet) {

            $pdo->beginTransaction();

            $won = ($bet["selected_outcome"] !== $match["result"]);

            if ($won) {

                // Gana usuario (contraapuesta)
                $stmt = $pdo->prepare("
                    UPDATE wallets
                    SET available_balance = available_balance + ?,
                        locked_balance = locked_balance - ?
                    WHERE user_id = ?
                ");
                $stmt->execute([
                    $bet["potential_payout"],
                    $bet["stake"],
                    $bet["user_id"]
                ]);

                $status = "won";

            } else {

                // Pierde usuario
                $stmt = $pdo->prepare("
                    UPDATE wallets
                    SET locked_balance = locked_balance - ?
                    WHERE user_id = ?
                ");
                $stmt->execute([
                    $bet["stake"],
                    $bet["user_id"]
                ]);

                $status = "lost";
            }

            // Actualizar apuesta
            $stmt = $pdo->prepare("
                UPDATE bets 
                SET status = ?, settled_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$status, $bet["id"]]);

            $pdo->commit();
        }
    }

} catch (Exception $e) {
    // Silencioso para demo
}
