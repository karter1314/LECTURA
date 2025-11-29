<?php
require_once __DIR__ . '/conexion.php';

$usuario_id = isset($_POST['usuario_id']) ? (int) $_POST['usuario_id'] : 0;
$libro_id = isset($_POST['libro_id']) ? (int) $_POST['libro_id'] : 0;
$resumen = isset($_POST['resumen']) ? trim($_POST['resumen']) : '';
$puntos_otorgados = 10;

if ($usuario_id <= 0 || $libro_id <= 0 || $resumen === '') {
    die('Todos los campos son obligatorios.');
}

// Verificar usuario
$consulta_usuario = 'SELECT id, nombre FROM usuarios WHERE id = ?';
$stmt_usuario = mysqli_prepare($conexion, $consulta_usuario);
if (!$stmt_usuario) {
    die('Error al preparar validación de usuario: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt_usuario, 'i', $usuario_id);
mysqli_stmt_execute($stmt_usuario);
$result_usuario = mysqli_stmt_get_result($stmt_usuario);
$usuario = mysqli_fetch_assoc($result_usuario);

if (!$usuario) {
    mysqli_stmt_close($stmt_usuario);
    die('El usuario no existe.');
}
mysqli_stmt_close($stmt_usuario);

// Verificar libro
$consulta_libro = 'SELECT id, titulo FROM libros WHERE id = ?';
$stmt_libro = mysqli_prepare($conexion, $consulta_libro);
if (!$stmt_libro) {
    die('Error al preparar validación de libro: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt_libro, 'i', $libro_id);
mysqli_stmt_execute($stmt_libro);
$result_libro = mysqli_stmt_get_result($stmt_libro);
$libro = mysqli_fetch_assoc($result_libro);

if (!$libro) {
    mysqli_stmt_close($stmt_libro);
    die('El libro no existe.');
}
mysqli_stmt_close($stmt_libro);

// Insertar resumen
$sql_resumen = 'INSERT INTO resumenes (usuario_id, libro_id, contenido, puntaje_otorgado) VALUES (?, ?, ?, ?)';
$stmt_resumen = mysqli_prepare($conexion, $sql_resumen);
if (!$stmt_resumen) {
    die('Error al preparar el registro del resumen: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt_resumen, 'iisi', $usuario_id, $libro_id, $resumen, $puntos_otorgados);
if (!mysqli_stmt_execute($stmt_resumen)) {
    mysqli_stmt_close($stmt_resumen);
    die('Error al registrar resumen: ' . mysqli_stmt_error($stmt_resumen));
}
mysqli_stmt_close($stmt_resumen);

// Registrar relación de lectura (idempotente)
$sql_lectura = 'INSERT INTO lecturas_usuarios (usuario_id, libro_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE fecha_actualizacion = CURRENT_TIMESTAMP';
$stmt_lectura = mysqli_prepare($conexion, $sql_lectura);
if ($stmt_lectura) {
    mysqli_stmt_bind_param($stmt_lectura, 'ii', $usuario_id, $libro_id);
    mysqli_stmt_execute($stmt_lectura);
    mysqli_stmt_close($stmt_lectura);
}

// Registrar puntos
$concepto = 'Resumen semanal: ' . $libro['titulo'];
$sql_puntos = 'INSERT INTO puntos (usuario_id, concepto, puntos) VALUES (?, ?, ?)';
$stmt_puntos = mysqli_prepare($conexion, $sql_puntos);
if (!$stmt_puntos) {
    die('Error al preparar el movimiento de puntos: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt_puntos, 'isi', $usuario_id, $concepto, $puntos_otorgados);
if (!mysqli_stmt_execute($stmt_puntos)) {
    mysqli_stmt_close($stmt_puntos);
    die('Error al registrar puntos: ' . mysqli_stmt_error($stmt_puntos));
}
mysqli_stmt_close($stmt_puntos);

// Calcular saldo total
$sql_saldo = 'SELECT COALESCE(SUM(puntos), 0) AS saldo FROM puntos WHERE usuario_id = ?';
$stmt_saldo = mysqli_prepare($conexion, $sql_saldo);
if (!$stmt_saldo) {
    die('Error al preparar consulta de saldo: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt_saldo, 'i', $usuario_id);
mysqli_stmt_execute($stmt_saldo);
$result_saldo = mysqli_stmt_get_result($stmt_saldo);
$saldo = mysqli_fetch_assoc($result_saldo);
$saldo_total = $saldo ? (int) $saldo['saldo'] : 0;
mysqli_stmt_close($stmt_saldo);

mysqli_close($conexion);

echo 'Resumen registrado y puntos sumados correctamente. Saldo actual: ' . $saldo_total . ' puntos.';
?>
