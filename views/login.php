<?php
session_start();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/inicio.css">
        <script src="../js/login.js"></script>
</head>
<body>
    <main class="contenedor">
        <section class="tarjeta">
            <h1>Chat Online</h1>

            <form action="../procesos/login.php" method="post" autocomplete="off" class="formulario">
                <label>
                    Usuario
                    <input id="username" type="text" name="username" required onblur="validaUsuario()">
                    <div id="errorUsuario" class="error"></div>
                </label>
                <label>
                    Contraseña
                    <input id="password" type="password" name="password" required onblur="validaPassword()">
                    <div id="errorPassword" class="error"></div>
                </label>
                <button type="submit">Entrar</button>
                <p class="texto-registrarse">¿No tienes una cuenta? <a href="registrar.php">Regístrate</a></p>
            </form>
        </section>
    </main>
</body>
</html>