-- Base de datos SESI
CREATE DATABASE IF NOT EXISTS sesi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sesi;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    rol ENUM('estudiante','docente','administrador') NOT NULL DEFAULT 'estudiante',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de libros
CREATE TABLE IF NOT EXISTS libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    autor VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Relación de lecturas por usuario
CREATE TABLE IF NOT EXISTS lecturas_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    libro_id INT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_lectura (usuario_id, libro_id),
    CONSTRAINT fk_lectura_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_lectura_libro FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de resúmenes
CREATE TABLE IF NOT EXISTS resumenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    libro_id INT NOT NULL,
    contenido TEXT NOT NULL,
    puntaje_otorgado INT NOT NULL DEFAULT 10,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_resumen_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_resumen_libro FOREIGN KEY (libro_id) REFERENCES libros(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Movimientos de puntos
CREATE TABLE IF NOT EXISTS puntos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    puntos INT NOT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_puntos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabla de premios
CREATE TABLE IF NOT EXISTS premios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    costo_puntos INT NOT NULL CHECK (costo_puntos > 0),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de canjes de premios
CREATE TABLE IF NOT EXISTS canjes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    premio_id INT NOT NULL,
    puntos_usados INT NOT NULL,
    fecha_canje TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_canje_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_canje_premio FOREIGN KEY (premio_id) REFERENCES premios(id) ON DELETE CASCADE
) ENGINE=InnoDB;
