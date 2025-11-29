<?php
require_once __DIR__ . '/conexion.php';

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$rol = isset($_POST['rol']) ? trim($_POST['rol']) : 'estudiante';

$roles_permitidos = ['estudiante', 'docente', 'administrador'];

if ($nombre === '' || $email === '' || !in_array($rol, $roles_permitidos, true)) {
    die('Datos inválidos. Verifique nombre, email y rol.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('El correo electrónico no es válido.');
}

$sql = 'INSERT INTO usuarios (nombre, email, rol) VALUES (?, ?, ?)';
$stmt = mysqli_prepare($conexion, $sql);

if (!$stmt) {
    die('Error al preparar la consulta: ' . mysqli_error($conexion));
}

mysqli_stmt_bind_param($stmt, 'sss', $nombre, $email, $rol);

if (!mysqli_stmt_execute($stmt)) {
    die('Error al registrar usuario: ' . mysqli_stmt_error($stmt));
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);

echo 'Usuario registrado correctamente.';
?>
