<?php
require '../config.php';

if (!isset($_SESSION['verificar_email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['verificar_email'];
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $codigo = $_POST["codigo"];

    $stmt = $pdo->prepare("SELECT * FROM usuarios 
        WHERE email = :email 
        AND otp_codigo = :codigo 
        AND otp_expira > NOW()");

    $stmt->execute([
        ':email' => $email,
        ':codigo' => $codigo
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        $pdo->prepare("UPDATE usuarios 
            SET verificado = true, otp_codigo = NULL 
            WHERE email = :email")
            ->execute([':email' => $email]);

        unset($_SESSION['verificar_email']);

        header("Location: login.php");
        exit();

    } else {
        $error = "Código inválido o expirado";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Verificar Cuenta - X91</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">

<div class="container py-5">
<div class="row justify-content-center">
<div class="col-md-4">

<div class="card bg-secondary">
<div class="card-body text-center">

<h2 class="fw-bold">X91</h2>
<p>Ingresa el código enviado a tu correo</p>

<?php if ($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
<input type="text" name="codigo" class="form-control mb-3 text-center" placeholder="000000" required>
<button class="btn btn-light w-100">Verificar</button>
</form>

</div>
</div>

</div>
</div>
</div>

</body>
</html>
