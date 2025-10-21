<?php
// Inicia la sesión para leer o escribir datos del usuario entre peticiones
session_start();

// Redirige a la página de inicio de sesión si no hay usuario autenticado
if (empty($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Obtiene los datos del usuario desde la sesión
$usuario = $_SESSION['usuario'];

// Las siguientes variables deben ser definidas antes de incluir este archivo
$conversaciones = $conversaciones ?? [];
$mensajes = $mensajes ?? [];
$canalActivo = $canalActivo ?? null;


$alertas = $_SESSION['errores'] ?? [];
$info = $_SESSION['exito'] ?? null;
unset($_SESSION['errores'], $_SESSION['exito']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat Online</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header class="barra-superior">
    <div class="usuario-actual">
        <span class="avatar"><?= strtoupper(substr($usuario['username'], 0, 1)); ?></span>
        <div>
            <strong><?= htmlspecialchars($usuario['nombre'] ?? $usuario['username'], ENT_QUOTES, 'UTF-8'); ?></strong>
            <small>Conectado</small>
        </div>
    </div>
    <form action="../procesos/logout.php" method="post">
        <button type="submit" class="btn-salir">Salir</button>
    </form>
</header>

<main class="layout">
    <aside class="lista-conversaciones">
        <form class="buscador" action="buscar.php" method="get">
            <input name="q" type="search" placeholder="Buscar chats o usuarios...">
        </form>

        <nav class="conversaciones">
            <?php foreach ($conversaciones as $conversacion): ?>
                <?php
                    $id = $conversacion['id'];
                    $activo = (string)$id === (string)$canalActivo ? 'activo' : '';
                ?>
                <a class="item-conversacion <?= $activo; ?>" href="?chat=<?= urlencode($id); ?>">
                    <span class="avatar"><?= strtoupper(substr($conversacion['alias'] ?? $conversacion['titulo'], 0, 1)); ?></span>
                    <div class="detalle">
                        <strong><?= htmlspecialchars($conversacion['titulo'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        <small><?= htmlspecialchars($conversacion['ultimo'] ?? 'Sin mensajes todavía', ENT_QUOTES, 'UTF-8'); ?></small>
                    </div>
                    <?php if (!empty($conversacion['sin_leer'])): ?>
                        <span class="contador"><?= (int)$conversacion['sin_leer']; ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>

            <?php if (!$conversaciones): ?>
                <p class="vacio">Aún no tienes conversaciones. Inicia una desde el panel principal.</p>
            <?php endif; ?>
        </nav>
    </aside>

    <section class="panel-chat">
        <header class="cabecera-chat">
            <?php if ($canalActivo): ?>
                <div>
                    <h2><?= htmlspecialchars($canalActivo['titulo'] ?? 'Chat seleccionado', ENT_QUOTES, 'UTF-8'); ?></h2>
                    <small><?= htmlspecialchars($canalActivo['estado'] ?? 'En línea', ENT_QUOTES, 'UTF-8'); ?></small>
                </div>
            <?php else: ?>
                <div>
                    <h2>Elige una conversación</h2>
                    <small>Selecciona un chat de la izquierda para comenzar.</small>
                </div>
            <?php endif; ?>
        </header>

        <div class="historial" id="historial">
            <?php foreach ($mensajes as $mensaje): ?>
                <?php $esPropio = (int)$mensaje['usuario_id'] === (int)$usuario['id']; ?>
                <article class="burbuja <?= $esPropio ? 'propio' : 'otro'; ?>">
                    <div class="meta">
                        <span class="autor"><?= htmlspecialchars($mensaje['autor'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <time datetime="<?= htmlspecialchars($mensaje['fecha_iso'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($mensaje['fecha'], ENT_QUOTES, 'UTF-8'); ?>
                        </time>
                    </div>
                    <p><?= nl2br(htmlspecialchars($mensaje['contenido'], ENT_QUOTES, 'UTF-8')); ?></p>
                </article>
            <?php endforeach; ?>

            <?php if (!$mensajes): ?>
                <p class="vacio">No hay mensajes todavía. ¡Envía el primero!</p>
            <?php endif; ?>
        </div>

        <footer class="enviar">
            <form action="../procesos/enviar_mensaje.php" method="post" autocomplete="off">
                <input type="hidden" name="chat_id" value="<?= htmlspecialchars($canalActivo['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <textarea name="mensaje" placeholder="Escribe un mensaje..." rows="1" required></textarea>
                <button type="submit">Enviar</button>
            </form>
<?php if ($alertas): ?>
    <div class="alerta alerta-error">
        <?= htmlspecialchars($alertas[0], ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>
<?php if ($info): ?>
    <div class="alerta alerta-exito">
        <?= htmlspecialchars($info, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>
        </footer>
    </section>
</main>
</body>
</html>