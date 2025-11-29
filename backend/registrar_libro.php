<?php
require_once __DIR__ . '/conexion.php';

$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
$autor = isset($_POST['autor']) ? trim($_POST['autor']) : '';
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;

if ($titulo === '' || $autor === '') {
    die('Título y autor son obligatorios.');
}

$sql = 'INSERT INTO libros (titulo, autor, descripcion) VALUES (?, ?, ?)';
$stmt = mysqli_prepare($conexion, $sql);

if (!$stmt) {
    die('Error al preparar la consulta: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt, 'sss', $titulo, $autor, $descripcion);

if (!mysqli_stmt_execute($stmt)) {
    die('Error al registrar libro: ' . mysqli_stmt_error($stmt));
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);

echo 'Libro registrado correctamente.';
?>
