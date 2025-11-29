<?php
require_once __DIR__ . '/../backend/conexion.php';

$usuarios = mysqli_query($conexion, "SELECT id, nombre FROM usuarios WHERE rol = 'estudiante' ORDER BY nombre");
$libros = mysqli_query($conexion, "SELECT id, titulo FROM libros ORDER BY titulo");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar resumen semanal</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<header>
    <h1>Enviar resumen semanal</h1>
</header>
<main>
    <nav class="nav">
        <a href="index.php">Volver al inicio</a>
    </nav>

    <section>
        <p class="notice">Cada resumen aprobado suma 10 puntos al saldo del estudiante.</p>
        <?php if (mysqli_num_rows($usuarios) === 0 || mysqli_num_rows($libros) === 0): ?>
            <p class="notice">Registre al menos un estudiante y un libro antes de enviar resúmenes.</p>
        <?php else: ?>
            <form action="../backend/registrar_resumen.php" method="post">
                <label for="usuario_id">Estudiante</label>
                <select id="usuario_id" name="usuario_id" required>
                    <?php while ($u = mysqli_fetch_assoc($usuarios)): ?>
                        <option value="<?php echo (int) $u['id']; ?>"><?php echo htmlspecialchars($u['nombre']); ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="libro_id">Libro</label>
                <select id="libro_id" name="libro_id" required>
                    <?php while ($l = mysqli_fetch_assoc($libros)): ?>
                        <option value="<?php echo (int) $l['id']; ?>"><?php echo htmlspecialchars($l['titulo']); ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="resumen">Resumen semanal</label>
                <textarea id="resumen" name="resumen" rows="6" required></textarea>

                <input type="submit" value="Enviar resumen">
            </form>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
<?php mysqli_close($conexion); ?>
