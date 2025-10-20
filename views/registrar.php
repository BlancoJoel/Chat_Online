<?php
session_start();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear cuenta</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/inicio.css">
        <script src="../js/login.js"></script>
</head>
<body>
    <main class="contenedor">
        <section class="tarjeta">
            <h1>Únete al Chat</h1>


            <form action="../procesos/registro.php" method="post" autocomplete="off" class="formulario">
                <label>
                    Nombre de usuario
                    <input type="text" id="username" name="username" onblur="validaUsuario()" >
                    <div id="errorUsuario" class="error"></div>
                </label>
                <label>
                    Contraseña
                    <input type="password" id="password" name="password" onblur="validaPassword()" >
                    <div id="errorPassword" class="error"></div>
                </label>
                <label>
                    Confirmar contraseña
                    <input type="password" id="confirm_password" name="confirm_password" onblur="validaConfirmPassword()" >
                    <div id="errorConfirm" class="error"></div>
                </label>
                <button type="submit">Registrarme</button>
            </form>
        </section>
    </main>

</body>
</html>