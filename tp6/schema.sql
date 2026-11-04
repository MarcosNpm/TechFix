-- ============================================================
-- TechFix · TP6
-- Esquema relacional normalizado (1FN, 2FN y 3FN)
-- Motor: MySQL 8 / MariaDB 10.4+
-- Ejecutar en phpMyAdmin, XAMPP, Laragon o consola:
--   mysql -u root -p < tp6/schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS techfix_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE techfix_db;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS servicio_etiqueta;
DROP TABLE IF EXISTS etiquetas;
DROP TABLE IF EXISTS detalle_pedidos;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS consultas;
DROP TABLE IF EXISTS reparaciones;
DROP TABLE IF EXISTS estados_reparacion;
DROP TABLE IF EXISTS servicios;
DROP TABLE IF EXISTS categorias_servicio;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias_producto;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 1. Roles de usuario (tabla maestra)
-- ------------------------------------------------------------
CREATE TABLE roles (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(30) NOT NULL UNIQUE,
    descripcion VARCHAR(150) NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. Usuarios (autenticación y datos personales)
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    telefono   VARCHAR(30) NULL,
    rol_id     INT UNSIGNED NOT NULL,
    activo     TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_rol
        FOREIGN KEY (rol_id) REFERENCES roles (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_usuarios_email (email),
    INDEX idx_usuarios_rol (rol_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. Categorías de servicios (tabla maestra)
-- ------------------------------------------------------------
CREATE TABLE categorias_servicio (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80) NOT NULL,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    icono       VARCHAR(40) NOT NULL DEFAULT 'herramienta',
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categorias_servicio_slug (slug)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. Servicios del taller (tabla principal de negocio)
-- ------------------------------------------------------------
CREATE TABLE servicios (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id    INT UNSIGNED NOT NULL,
    nombre          VARCHAR(120) NOT NULL,
    slug            VARCHAR(140) NOT NULL UNIQUE,
    descripcion     TEXT NOT NULL,
    precio_base     DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    tiempo_estimado VARCHAR(50) NOT NULL DEFAULT 'A convenir',
    garantia_dias   INT UNSIGNED NOT NULL DEFAULT 90,
    destacado       TINYINT(1) NOT NULL DEFAULT 0,
    activo          TINYINT(1) NOT NULL DEFAULT 1,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_servicios_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias_servicio (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_servicios_categoria_activo (categoria_id, activo),
    INDEX idx_servicios_destacado (destacado),
    INDEX idx_servicios_nombre (nombre)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. Estados del flujo de reparación (tabla maestra)
-- ------------------------------------------------------------
CREATE TABLE estados_reparacion (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(60) NOT NULL,
    slug        VARCHAR(60) NOT NULL UNIQUE,
    orden       TINYINT UNSIGNED NOT NULL DEFAULT 1,
    color       VARCHAR(7) NOT NULL DEFAULT '#667085',
    descripcion VARCHAR(255) NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estados_orden (orden)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. Reparaciones (entidad principal del taller)
-- ------------------------------------------------------------
CREATE TABLE reparaciones (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo           VARCHAR(20) NOT NULL UNIQUE,
    usuario_id       INT UNSIGNED NULL,
    servicio_id      INT UNSIGNED NOT NULL,
    estado_id        INT UNSIGNED NOT NULL,
    cliente_nombre   VARCHAR(100) NOT NULL,
    cliente_email    VARCHAR(150) NOT NULL,
    cliente_telefono VARCHAR(30) NOT NULL,
    equipo_marca     VARCHAR(60) NOT NULL,
    equipo_modelo    VARCHAR(100) NOT NULL,
    problema         TEXT NOT NULL,
    observaciones    TEXT NULL,
    costo_final      DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_reparaciones_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_reparaciones_servicio
        FOREIGN KEY (servicio_id) REFERENCES servicios (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_reparaciones_estado
        FOREIGN KEY (estado_id) REFERENCES estados_reparacion (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_reparaciones_codigo (codigo),
    INDEX idx_reparaciones_email (cliente_email),
    INDEX idx_reparaciones_estado (estado_id),
    INDEX idx_reparaciones_fecha (created_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. Categorías de productos (tabla maestra)
-- ------------------------------------------------------------
CREATE TABLE categorias_producto (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80) NOT NULL,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. Productos / repuestos (catálogo de la tienda)
-- ------------------------------------------------------------
CREATE TABLE productos (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id      INT UNSIGNED NOT NULL,
    nombre            VARCHAR(120) NOT NULL,
    descripcion       TEXT NULL,
    descripcion_corta VARCHAR(180) NULL,
    precio            DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    stock             INT NOT NULL DEFAULT 0,
    imagen            VARCHAR(120) NOT NULL DEFAULT 'producto-generico.svg',
    activo            TINYINT(1) NOT NULL DEFAULT 1,
    created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_productos_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias_producto (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_productos_categoria_activo (categoria_id, activo),
    INDEX idx_productos_nombre (nombre),
    INDEX idx_productos_precio (precio)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. Pedidos de la tienda
-- ------------------------------------------------------------
CREATE TABLE pedidos (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero         VARCHAR(20) NOT NULL UNIQUE,
    usuario_id     INT UNSIGNED NULL,
    cliente_nombre VARCHAR(100) NOT NULL,
    cliente_email  VARCHAR(150) NOT NULL,
    total          DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    estado         VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_pedidos_numero (numero),
    INDEX idx_pedidos_email (cliente_email),
    INDEX idx_pedidos_estado (estado)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 10. Detalle de pedidos (relación N:M entre pedidos y productos)
-- ------------------------------------------------------------
CREATE TABLE detalle_pedidos (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id      INT UNSIGNED NOT NULL,
    producto_id    INT UNSIGNED NOT NULL,
    cantidad       INT UNSIGNED NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal       DECIMAL(10, 2) NOT NULL,
    created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_detalle_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_detalle_producto
        FOREIGN KEY (producto_id) REFERENCES productos (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY uq_detalle_pedido_producto (pedido_id, producto_id),
    INDEX idx_detalle_pedido (pedido_id),
    INDEX idx_detalle_producto (producto_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 11. Etiquetas y relación N:M con servicios
-- ------------------------------------------------------------
CREATE TABLE etiquetas (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(50) NOT NULL UNIQUE,
    slug       VARCHAR(60) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE servicio_etiqueta (
    servicio_id INT UNSIGNED NOT NULL,
    etiqueta_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (servicio_id, etiqueta_id),
    CONSTRAINT fk_servicio_etiqueta_servicio
        FOREIGN KEY (servicio_id) REFERENCES servicios (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_servicio_etiqueta_etiqueta
        FOREIGN KEY (etiqueta_id) REFERENCES etiquetas (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 12. Consultas del formulario de contacto
-- ------------------------------------------------------------
CREATE TABLE consultas (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    telefono   VARCHAR(30) NULL,
    asunto     VARCHAR(120) NOT NULL,
    mensaje    TEXT NOT NULL,
    leido      TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_consultas_email (email),
    INDEX idx_consultas_leido (leido),
    INDEX idx_consultas_fecha (created_at)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
