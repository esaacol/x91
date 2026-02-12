<?php
require_once "config/config.php";

/* =========================
   PROTEGER PÁGINA
========================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* =========================
   OBTENER BALANCE
========================= */
$stmt = $pdo->prepare("SELECT balance FROM wallets WHERE user_id = ?");
$stmt->execute([$user_id]);
$wallet = $stmt->fetch();

$balance = $wallet ? $wallet['balance'] : 0;

/* =========================
   OBTENER PARTIDOS
========================= */
$stmt = $pdo->prepare("
    SELECT * FROM matches 
    WHERE status = 'scheduled' 
    ORDER BY match_date ASC 
    LIMIT 20
");
$stmt->execute();
$matches = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>X91</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="app">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h3>X91</h3>
        <p>🏠 Inicio</p>
        <p>💰 Mi Wallet</p>
        <p>📊 Mis Apuestas</p>
        <p>⚙ Configuración</p>
        <br>
        <a href="auth/logout.php">Cerrar sesión</a>
    </div>

    <!-- CONTENIDO -->
    <div class="content">

        <!-- TOPBAR -->
        <div class="topbar">
            <div>X91 Fútbol</div>
            <div class="balance">
                Balance: €<?php echo number_format($balance, 2); ?>
            </div>
        </div>

        <div class="banner">
            <h2>Eventos principales</h2>
        </div>

        <div class="matches">

            <?php if (!empty($matches)): ?>
                <?php foreach ($matches as $match): ?>

                    <div class="match-card">

                        <div class="match-header">
                            <?= htmlspecialchars($match["home_team"]) ?>
                            <span>VS</span>
                            <?= htmlspecialchars($match["away_team"]) ?>
                        </div>

                        <div class="progress-bar">
                            <div class="progress"></div>
                        </div>

                        <!-- FORMULARIO DE APUESTA -->
                        <form method="POST" action="actions/place_bet.php">

                            <input type="hidden" name="match_id" value="<?= $match['id'] ?>">
                            <input type="hidden" name="odds" value="2.10">

                            <div class="odds-row">

                                <button type="submit" name="selection" value="home">
                                    Local
                                    <strong>2.10</strong>
                                </button>

                                <button type="submit" name="selection" value="draw">
                                    Empate
                                    <strong>3.20</strong>
                                </button>

                                <button type="submit" name="selection" value="away">
                                    Visitante
                                    <strong>2.80</strong>
                                </button>

                            </div>

                            <br>

                            <input 
                                type="number" 
                                name="stake" 
                                placeholder="Monto €" 
                                step="0.01" 
                                min="1" 
                                required
                            >

                        </form>

                        <div class="match-date">
                            <?= date("d/m H:i", strtotime($match["match_date"])) ?>
                        </div>

                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay partidos disponibles.</p>
            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>
