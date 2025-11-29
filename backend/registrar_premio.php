<?php
require_once __DIR__ . '/conexion.php';

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
$costo = isset($_POST['costo_puntos']) ? (int) $_POST['costo_puntos'] : 0;

if ($nombre === '' || $costo <= 0) {
    die('Nombre y costo en puntos son obligatorios.');
}

$sql = 'INSERT INTO premios (nombre, descripcion, costo_puntos) VALUES (?, ?, ?)';
$stmt = mysqli_prepare($conexion, $sql);

if (!$stmt) {
    die('Error al preparar la consulta: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt, 'ssi', $nombre, $descripcion, $costo);

if (!mysqli_stmt_execute($stmt)) {
    die('Error al registrar premio: ' . mysqli_stmt_error($stmt));
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);

echo 'Premio registrado correctamente.';
?>
