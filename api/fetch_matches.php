<?php
require_once "../config/db.php";

$apiToken = "33f0c0d4ce02425686bb4c8ec20581f2";

$dateFrom = date("Y-m-d");
$dateTo = date("Y-m-d", strtotime("+1 day"));

$url = "https://api.football-data.org/v4/matches?dateFrom=$dateFrom&dateTo=$dateTo";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Auth-Token: $apiToken"
]);

$response = curl_exec($ch);

if(curl_errno($ch)){
    echo "Error cURL: " . curl_error($ch);
    exit;
}

curl_close($ch);

$data = json_decode($response, true);

if (!isset($data["matches"])) {
    echo "No hay datos";
    exit;
}

foreach ($data["matches"] as $match) {

    $stmt = $pdo->prepare("
        INSERT IGNORE INTO matches 
        (id, external_id, league, home_team, away_team, match_date, status, home_odds, draw_odds, away_odds)
        VALUES (?, ?, ?, ?, ?, ?, 'scheduled', 0, 0, 0)
    ");

    $stmt->execute([
        bin2hex(random_bytes(16)),
        $match["id"],
        $match["competition"]["name"],
        $match["homeTeam"]["name"],
        $match["awayTeam"]["name"],
        $match["utcDate"]
    ]);
}

echo "Partidos actualizados correctamente";
