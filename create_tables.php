<?php
require 'config.php';

try {

    $sql = "

    CREATE TABLE IF NOT EXISTS usuarios (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(120) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        otp_code VARCHAR(6),
        otp_expiration TIMESTAMP,
        email_verificado BOOLEAN DEFAULT FALSE,
        intentos_otp INT DEFAULT 0,
        estado VARCHAR(20) DEFAULT 'activo',
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    ";

    $pdo->exec($sql);

    echo "<h2>✅ Tabla creada correctamente.</h2>";

} catch (PDOException $e) {
    echo "<h2>❌ Error creando tabla:</h2>";
    echo $e->getMessage();
}
