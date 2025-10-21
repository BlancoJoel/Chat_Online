<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/login.php');
    exit;
}

$usuario = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$errores = [];

if ($usuario === '' || $password === '') {
    $errores[] = 'Usuario y contraseña son obligatorios.';
}

if ($errores) {
    $_SESSION['errores'] = $errores;
    header('Location: ../views/login.php');
    exit;
}

include __DIR__ . '/../services/connection.php';

try {
    $stmt = $conn->prepare('SELECT Id, pwd, Nombre, Username FROM tbl_users WHERE Username = :username LIMIT 1');
    $stmt->execute([':username' => $usuario]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fila || !password_verify($password, $fila['pwd'])) {
        $_SESSION['errores'] = ['Credenciales inválidas.'];
        header('Location: ../views/login.php');
        exit;
    }

    $_SESSION['usuario'] = [
        'id' => $fila['Id'],
        'nombre' => $fila['Nombre'],
        'username' => $fila['Username'],
    ];

    $_SESSION['exito'] = 'Sesión iniciada correctamente.';
    header('Location: ../views/index.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['errores'] = ['Error de conexión.'];
    header('Location: ../views/login.php');
    exit;
}