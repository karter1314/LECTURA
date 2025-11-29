<?php
require_once __DIR__ . '/../backend/conexion.php';

// Consultar listados para los formularios
$usuarios = mysqli_query($conexion, "SELECT id, nombre, rol FROM usuarios ORDER BY nombre");
$libros = mysqli_query($conexion, "SELECT id, titulo, autor FROM libros ORDER BY titulo");
$premios = mysqli_query($conexion, "SELECT id, nombre, costo_puntos FROM premios ORDER BY nombre");

// Saldo por usuario
$saldo_query = "SELECT u.id, u.nombre, COALESCE(SUM(p.puntos), 0) AS saldo FROM usuarios u LEFT JOIN puntos p ON u.id = p.usuario_id GROUP BY u.id ORDER BY u.nombre";
$saldos = mysqli_query($conexion, $saldo_query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SESI - Sistema de Lecturas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<header>
    <h1>SESI - Sistema de Seguimiento de Lectura</h1>
</header>
<main>
    <nav class="nav">
        <a href="#usuarios">Registrar usuario</a>
        <a href="#libros">Registrar libro</a>
        <a href="#premios">Registrar premio</a>
        <a href="form_resumen.php">Enviar resumen semanal</a>
        <a href="#canjes">Canjear premios</a>
        <a href="#saldos">Saldos de puntos</a>
    </nav>

    <section id="usuarios">
        <h2>Registrar usuario</h2>
        <form action="../backend/registrar_usuario.php" method="post">
            <label for="nombre">Nombre completo</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" required>

            <label for="rol">Rol</label>
            <select id="rol" name="rol" required>
                <option value="estudiante">Estudiante</option>
                <option value="docente">Docente</option>
                <option value="administrador">Administrador</option>
            </select>

            <input type="submit" value="Guardar usuario">
        </form>
    </section>

    <section id="libros">
        <h2>Registrar libro</h2>
        <form action="../backend/registrar_libro.php" method="post">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" required>

            <label for="autor">Autor</label>
            <input type="text" id="autor" name="autor" required>

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3"></textarea>

            <input type="submit" value="Guardar libro">
        </form>
    </section>

    <section id="premios">
        <h2>Registrar premio</h2>
        <form action="../backend/registrar_premio.php" method="post">
            <label for="nombre_premio">Nombre del premio</label>
            <input type="text" id="nombre_premio" name="nombre" required>

            <label for="descripcion_premio">Descripción</label>
            <textarea id="descripcion_premio" name="descripcion" rows="3"></textarea>

            <label for="costo_puntos">Costo en puntos</label>
            <input type="number" id="costo_puntos" name="costo_puntos" min="1" required>

            <input type="submit" value="Guardar premio">
        </form>
    </section>

    <section id="canjes">
        <h2>Canjear premio</h2>
        <?php if (mysqli_num_rows($usuarios) === 0 || mysqli_num_rows($premios) === 0): ?>
            <p class="notice">Registre al menos un usuario y un premio para habilitar los canjes.</p>
        <?php else: ?>
            <form action="../backend/canjear_premio.php" method="post">
                <label for="usuario_canje">Estudiante</label>
                <select id="usuario_canje" name="usuario_id" required>
                    <?php while ($u = mysqli_fetch_assoc($usuarios)): ?>
                        <option value="<?php echo (int) $u['id']; ?>"><?php echo htmlspecialchars($u['nombre']); ?> (<?php echo htmlspecialchars($u['rol']); ?>)</option>
                    <?php endwhile; ?>
                </select>

                <label for="premio_canje">Premio</label>
                <select id="premio_canje" name="premio_id" required>
                    <?php while ($p = mysqli_fetch_assoc($premios)): ?>
                        <option value="<?php echo (int) $p['id']; ?>"><?php echo htmlspecialchars($p['nombre']); ?> - Costo: <?php echo (int) $p['costo_puntos']; ?> pts</option>
                    <?php endwhile; ?>
                </select>

                <input type="submit" value="Confirmar canje">
            </form>
        <?php endif; ?>
    </section>

    <section id="saldos">
        <h2>Saldos de puntos</h2>
        <?php if ($saldos && mysqli_num_rows($saldos) > 0): ?>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($s = mysqli_fetch_assoc($saldos)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['nombre']); ?></td>
                            <td><?php echo (int) $s['saldo']; ?> pts</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="notice">No hay usuarios registrados.</p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
<?php mysqli_close($conexion); ?>
