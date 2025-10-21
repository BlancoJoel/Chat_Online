<?php
session_start();

// Intentar cargar amigos desde la base de datos; si falla, usar lista por defecto
$friends = [];
try {
    require_once __DIR__ . '/../conexion/conexion.php'; // debe definir $conn (PDO)

    // Obtener id del usuario actual desde la sesión (soporta varios nombres habituales)
    $me = null;
    if (!empty($_SESSION['user_id'])) $me = $_SESSION['user_id'];
    elseif (!empty($_SESSION['Id'])) $me = $_SESSION['Id'];
    elseif (!empty($_SESSION['IdUsuario'])) $me = $_SESSION['IdUsuario'];
    elseif (!empty($_SESSION['username_id'])) $me = $_SESSION['username_id'];

    if ($me === null) {
        // Si no hay usuario en sesión no mostramos amigos (podrías redirigir al login)
        throw new Exception('Usuario no autenticado');
    }

    // Consulta: busca filas de amistad donde el usuario participa y el estado indica amistad aceptada.
    // Ajusta la comprobación de Estado según los valores de tu BD ('aceptada', '1', etc.)
    $sql = "
        SELECT u.Id, COALESCE(u.Nombre, u.Username) AS Nombre, u.Username
        FROM tbl_amistades a
        JOIN tbl_users u
          ON u.Id = CASE WHEN a.Id_amistad_Origen = :me THEN a.Id_amistad_Destino ELSE a.Id_amistad_Origen END
        WHERE (a.Id_amistad_Origen = :me OR a.Id_amistad_Destino = :me)
          AND (a.Estado = 'aceptada' OR a.Estado = '1' OR a.Estado = 1)
        ORDER BY u.Nombre
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':me', $me, PDO::PARAM_INT);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $name = $row['Nombre'] ?? $row['Username'] ?? 'Usuario';
        $friends[] = [
            'id' => $row['Id'],
            'name' => $name,
            'status' => 'offline', // si tienes un mecanismo para saber online/offline, reemplázalo aquí
            'avatar' => 'https://i.pravatar.cc/60?u=' . $row['Id'] // placeholder por usuario
        ];
    }

} catch (Exception $e) {
    // Fallback: lista de ejemplo si no hay conexión, el usuario no está autenticado o ocurre un error
    error_log('No se pudieron cargar amigos: ' . $e->getMessage());
    $friends = [
        ['id'=>1,'name'=>'Ana Gómez','status'=>'online','avatar'=>'https://i.pravatar.cc/60?img=1'],
        ['id'=>2,'name'=>'Luis Pérez','status'=>'offline','avatar'=>'https://i.pravatar.cc/60?img=2'],
        ['id'=>3,'name'=>'María Ruiz','status'=>'online','avatar'=>'https://i.pravatar.cc/60?img=3'],
        ['id'=>4,'name'=>'Javier Díaz','status'=>'offline','avatar'=>'https://i.pravatar.cc/60?img=4'],
    ];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Amigos - Chat</title>
<link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="page">
    <div class="panel" role="region" aria-label="Lista de amigos">
        <aside class="sidebar" aria-labelledby="amigosTitulo">
            <div class="header">
                <div id="amigosTitulo" class="title">Mis amigos</div>
                <div class="count" aria-hidden="true"><?php echo count($friends); ?></div>
            </div>

            <div class="search">
                <input type="search" id="search" placeholder="Buscar amigo..." aria-label="Buscar amigo">
            </div>

            <div class="list" id="friendsList">
                <?php foreach($friends as $f): 
                    $status = isset($f['status']) && $f['status']==='online' ? 'online' : 'offline';
                    $nameEsc = htmlspecialchars($f['name'], ENT_QUOTES, 'UTF-8');
                    $avatar = htmlspecialchars($f['avatar'], ENT_QUOTES, 'UTF-8');
                    $id = htmlspecialchars($f['id'], ENT_QUOTES, 'UTF-8');
                ?>
                <div class="friend" data-name="<?php echo strtolower($nameEsc); ?>">
                    <div class="avatar" aria-hidden="true">
                        <img src="<?php echo $avatar; ?>" alt="">
                    </div>
                    <div class="info">
                        <div class="name"><?php echo $nameEsc; ?></div>
                        <div class="status"><span class="dot <?php echo $status; ?>"></span><?php echo $status === 'online' ? 'En línea' : 'Desconectado'; ?></div>
                    </div>
                    <div class="actions">
                        <button class="btn chat" onclick="startChat('<?php echo $id; ?>')" aria-label="Iniciar chat con <?php echo $nameEsc; ?>">Iniciar chat</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </aside>
    </div>
</div>

<script>
// Función para iniciar chat (ajusta la ruta si tu chat está en otra ubicación)
function startChat(friendId){
    window.location.href = 'chat.php?with=' + encodeURIComponent(friendId);
}

// Filtrado básico de la lista
document.getElementById('search').oninput = function(e){
    var q = e.target.value.trim().toLowerCase();
    var items = document.querySelectorAll('#friendsList .friend');
    items.forEach(function(it){
        var name = it.getAttribute('data-name') || '';
        it.style.display = q === '' || name.indexOf(q) !== -1 ? '' : 'none';
    });
};
</script>
</body>
</html>