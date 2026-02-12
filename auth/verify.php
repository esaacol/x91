<?php
require '../config.php';

$email = $_GET["email"] ?? "";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];
    $codigo = $_POST["codigo"];

    $stmt = $pdo->prepare("
        SELECT * FROM usuarios
        WHERE email = ?
        AND otp_code = ?
        AND otp_expiration > NOW()
    ");
    $stmt->execute([$email, $codigo]);
    $user = $stmt->fetch();

    if ($user) {

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET email_verificado = true,
                otp_code = NULL,
                otp_expiration = NULL
            WHERE id = ?
        ");
        $stmt->execute([$user["id"]]);

        header("Location: login.php?success=1");
        exit();

    } else {
        $mensaje = "Código inválido o expirado.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Verificar - X91</title>
</head>
<body>

<h2>Verifica tu cuenta</h2>

<?php if($mensaje) echo "<p>$mensaje</p>"; ?>

<form method="POST">
<input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
<input type="text" name="codigo" placeholder="Código OTP" required><br>
<button type="submit">Verificar</button>
</form>

</body>
</html>
