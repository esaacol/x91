<?php
include "../config/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {

        $user_id = $stmt->insert_id;

        // Crear wallet con saldo inicial 100
        $conn->query("INSERT INTO wallets (user_id, balance) VALUES ($user_id, 100)");

        echo "Registro exitoso. <a href='login.php'>Iniciar sesión</a>";
    } else {
        echo "Error: Usuario o email ya existe";
    }
}
?>

<form method="POST">
    <h2>Registro</h2>
    <input name="username" placeholder="Usuario" required><br><br>
    <input name="email" type="email" placeholder="Email" required><br><br>
    <input name="password" type="password" placeholder="Contraseña" required><br><br>
    <button type="submit">Registrarse</button>
</form>
