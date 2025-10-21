<?php
// filepath: c:\wamp64\www\Chat_Online\Chat_Online\procesos\procesar_registro.php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/registrar.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$nombre = trim($_POST['nombre'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$errores = [];

if ($username === '') {
    $errores[] = 'El nombre de usuario es obligatorio.';
}

if ($nombre === '') {
    $nombre = $username;
}

if ($password === '' || $confirmPassword === '') {
    $errores[] = 'Debes ingresar y confirmar la contraseña.';
} elseif ($password !== $confirmPassword) {
    $errores[] = 'Las contraseñas no coinciden.';
}

if (strlen($password) < 8
    || !preg_match('/[A-Z]/', $password)
    || !preg_match('/[a-z]/', $password)
    || !preg_match('/\d/', $password)
    || !preg_match('/[^A-Za-z0-9]/', $password)
) {
    $errores[] = 'La contraseña debe tener 8 caracteres mínimo y combinar mayúsculas, minúsculas, números y símbolos.';
}

if ($errores) {
    $_SESSION['errores'] = $errores;
    header('Location: ../views/registrar.php');
    exit;
}

require_once __DIR__ . '/../services/connection.php';

try {
    $stmt = $conn->prepare('SELECT 1 FROM tbl_users WHERE Username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);

    if ($stmt->fetchColumn()) {
        $_SESSION['errores'] = ['El usuario ya está registrado.'];
        header('Location: ../views/registrar.php');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $insert = $conn->prepare(
        'INSERT INTO tbl_users (pwd, Nombre, Username) VALUES (:pwd, :nombre, :username)'
    );
    $insert->execute([
        ':pwd' => $hash,
        ':nombre' => $nombre,
        ':username' => $username,
    ]);

    $_SESSION['exito'] = 'Cuenta creada. Ya puedes iniciar sesión.';
    header('Location: ../views/login.php');
    exit;
} catch (PDOException $e) {
    error_log('Registro: ' . $e->getMessage());
    $_SESSION['errores'] = ['No se pudo registrar. Intenta más tarde.'];
    header('Location: ../views/registrar.php');
    exit;
}