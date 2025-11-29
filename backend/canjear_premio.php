<?php
require_once __DIR__ . '/conexion.php';

$usuario_id = isset($_POST['usuario_id']) ? (int) $_POST['usuario_id'] : 0;
$premio_id = isset($_POST['premio_id']) ? (int) $_POST['premio_id'] : 0;

if ($usuario_id <= 0 || $premio_id <= 0) {
    die('Usuario y premio son obligatorios.');
}

// Validar usuario
$query_usuario = 'SELECT id, nombre FROM usuarios WHERE id = ?';
$stmt_usuario = mysqli_prepare($conexion, $query_usuario);
if (!$stmt_usuario) {
    die('Error al validar usuario: ' . mysqli_error($conexion));
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

// Validar premio
$query_premio = 'SELECT id, nombre, costo_puntos FROM premios WHERE id = ?';
$stmt_premio = mysqli_prepare($conexion, $query_premio);
if (!$stmt_premio) {
    die('Error al validar premio: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt_premio, 'i', $premio_id);
mysqli_stmt_execute($stmt_premio);
$result_premio = mysqli_stmt_get_result($stmt_premio);
$premio = mysqli_fetch_assoc($result_premio);
if (!$premio) {
    mysqli_stmt_close($stmt_premio);
    die('El premio no existe.');
}
mysqli_stmt_close($stmt_premio);

// Consultar saldo actual
$query_saldo = 'SELECT COALESCE(SUM(puntos), 0) AS saldo FROM puntos WHERE usuario_id = ?';
$stmt_saldo = mysqli_prepare($conexion, $query_saldo);
if (!$stmt_saldo) {
    die('Error al consultar saldo: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt_saldo, 'i', $usuario_id);
mysqli_stmt_execute($stmt_saldo);
$result_saldo = mysqli_stmt_get_result($stmt_saldo);
$saldo = mysqli_fetch_assoc($result_saldo);
$saldo_actual = $saldo ? (int) $saldo['saldo'] : 0;
mysqli_stmt_close($stmt_saldo);

if ($saldo_actual < (int) $premio['costo_puntos']) {
    mysqli_close($conexion);
    die('Saldo insuficiente para canjear este premio.');
}

// Registrar canje
$sql_canje = 'INSERT INTO canjes (usuario_id, premio_id, puntos_usados) VALUES (?, ?, ?)';
$stmt_canje = mysqli_prepare($conexion, $sql_canje);
if (!$stmt_canje) {
    die('Error al preparar canje: ' . mysqli_error($conexion));
}

$puntos_usados = (int) $premio['costo_puntos'];
mysqli_stmt_bind_param($stmt_canje, 'iii', $usuario_id, $premio_id, $puntos_usados);
if (!mysqli_stmt_execute($stmt_canje)) {
    mysqli_stmt_close($stmt_canje);
    die('Error al registrar canje: ' . mysqli_stmt_error($stmt_canje));
}
mysqli_stmt_close($stmt_canje);

// Registrar descuento de puntos
$concepto = 'Canje de premio: ' . $premio['nombre'];
$sql_puntos = 'INSERT INTO puntos (usuario_id, concepto, puntos) VALUES (?, ?, ?)';
$stmt_puntos = mysqli_prepare($conexion, $sql_puntos);
if (!$stmt_puntos) {
    die('Error al preparar movimiento de puntos: ' . mysqli_error($conexion));
}

$puntos_descontados = -1 * $puntos_usados;
mysqli_stmt_bind_param($stmt_puntos, 'isi', $usuario_id, $concepto, $puntos_descontados);
if (!mysqli_stmt_execute($stmt_puntos)) {
    mysqli_stmt_close($stmt_puntos);
    die('Error al descontar puntos: ' . mysqli_stmt_error($stmt_puntos));
}
mysqli_stmt_close($stmt_puntos);

// Nuevo saldo
$query_nuevo_saldo = 'SELECT COALESCE(SUM(puntos), 0) AS saldo FROM puntos WHERE usuario_id = ?';
$stmt_nuevo_saldo = mysqli_prepare($conexion, $query_nuevo_saldo);
if (!$stmt_nuevo_saldo) {
    die('Error al calcular nuevo saldo: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt_nuevo_saldo, 'i', $usuario_id);
mysqli_stmt_execute($stmt_nuevo_saldo);
$result_nuevo = mysqli_stmt_get_result($stmt_nuevo_saldo);
$nuevo = mysqli_fetch_assoc($result_nuevo);
$nuevo_saldo = $nuevo ? (int) $nuevo['saldo'] : 0;
mysqli_stmt_close($stmt_nuevo_saldo);

mysqli_close($conexion);

echo 'Canje realizado con éxito. Saldo actual: ' . $nuevo_saldo . ' puntos.';
?>
