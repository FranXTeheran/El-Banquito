<?php
    $host = '127.0.0.1';
    $dbname = 'familybank';
    $username = 'root';
    $password = 'TU_CONTRASEÑA';

    try {
        $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
?>
