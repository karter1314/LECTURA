<?php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'sesi';

$conexion = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$conexion) {
    die('Error de conexión: ' . mysqli_connect_error());
}

mysqli_set_charset($conexion, 'utf8mb4');
?>
