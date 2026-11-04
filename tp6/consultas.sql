-- ============================================================
-- TechFix · TP6
-- Datos iniciales + operaciones CRUD de prueba (DML)
-- Ejecutar DESPUÉS de schema.sql.
--   mysql -u root -p < tp6/consultas.sql
-- ============================================================

USE techfix_db;
SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- CREATE · Datos maestros iniciales
-- ------------------------------------------------------------
INSERT INTO roles (id, nombre, descripcion) VALUES
    (1, 'admin',   'Acceso total al panel de administración'),
    (2, 'usuario', 'Cliente registrado del sitio')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);

-- Contraseñas de demostración:
--   admin@techfix.com.ar  → Admin1234!
--   cliente@demo.com      → Cliente1234!
-- Los hashes se generaron con password_hash(..., PASSWORD_BCRYPT).
INSERT INTO usuarios (id, nombre, email, password, telefono, rol_id, activo) VALUES
    (1, 'Marcos Núñez', 'admin@techfix.com.ar', '$2y$12$6RHScppEvgaP390XtLMkK.YXSgewo66V1XWeARRGrgU8SSEApdYii', '+54 11 5555-0101', 1, 1),
    (2, 'Lucía Fernández', 'cliente@demo.com', '$2y$12$zog7MsJ60Ab1YnUEQmHcdOsWLbQrLbY.at9/MG4Hlg/MEEdL1YLBO', '+54 11 4444-2020', 2, 1),
    (3, 'Diego Ramírez', 'diego@demo.com', '$2y$12$zog7MsJ60Ab1YnUEQmHcdOsWLbQrLbY.at9/MG4Hlg/MEEdL1YLBO', '+54 11 3333-3030', 2, 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO categorias_servicio (id, nombre, slug, descripcion, icono) VALUES
    (1, 'Celulares', 'celulares', 'Pantallas, baterías, placas y desbloqueos.', 'celular'),
    (2, 'Computadoras', 'computadoras', 'Notebooks, PC de escritorio y componentes.', 'laptop'),
    (3, 'Mantenimiento', 'mantenimiento', 'Limpieza, optimización y prevención.', 'config'),
    (4, 'Electrónica', 'electronica', 'Reparación de placas y soldadura.', 'rayo'),
    (5, 'Recuperación de datos', 'recuperacion-de-datos', 'Rescate de archivos y respaldos.', 'datos'),
    (6, 'Soporte técnico', 'soporte-tecnico', 'Asistencia remota y presencial.', 'soporte')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO estados_reparacion (id, nombre, slug, orden, color, descripcion) VALUES
    (1, 'Recibido', 'recibido', 1, '#175CD3', 'El equipo ingresó al taller y espera diagnóstico.'),
    (2, 'En diagnóstico', 'diagnostico', 2, '#A15C00', 'Estamos evaluando la falla y preparando el presupuesto.'),
    (3, 'En reparación', 'reparacion', 3, '#6941C6', 'El técnico está trabajando sobre el equipo.'),
    (4, 'Control de calidad', 'control-calidad', 4, '#0087A3', 'Probamos el funcionamiento antes de entregar.'),
    (5, 'Listo para retirar', 'listo', 5, '#12805C', 'El equipo está terminado y disponible en el local.'),
    (6, 'Entregado', 'entregado', 6, '#475467', 'El cliente retiró el equipo.')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO categorias_producto (id, nombre, slug, descripcion) VALUES
    (1, 'Repuestos para celulares', 'repuestos-celulares', 'Pantallas, baterías y flexores.'),
    (2, 'Repuestos para computadoras', 'repuestos-computadoras', 'Almacenamiento, memoria y baterías.'),
    (3, 'Periféricos', 'perifericos', 'Teclados, mouse y accesorios de escritorio.'),
    (4, 'Accesorios', 'accesorios', 'Cargadores, cables y audio.')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO etiquetas (id, nombre, slug) VALUES
    (1, 'Express', 'express'),
    (2, 'Con garantía', 'con-garantia'),
    (3, 'Repuesto original', 'repuesto-original'),
    (4, 'Económico', 'economico')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- ------------------------------------------------------------
-- CREATE · Servicios del taller
-- ------------------------------------------------------------
INSERT INTO servicios
    (id, categoria_id, nombre, slug, descripcion, precio_base, tiempo_estimado, garantia_dias, destacado, activo)
VALUES
    (1, 1, 'Cambio de pantalla', 'cambio-de-pantalla',
     'Reemplazamos la pantalla dañada por un repuesto de calidad, con prueba de táctil, brillo y calibración de colores antes de entregar.',
     85000.00, '2 a 4 horas', 90, 1, 1),
    (2, 1, 'Reemplazo de batería', 'reemplazo-de-bateria',
     'Cambiamos la batería agotada por una nueva y verificamos la carga, la autonomía y el estado del puerto de carga.',
     45000.00, '1 a 2 horas', 180, 1, 1),
    (3, 1, 'Reparación de placa', 'reparacion-de-placa',
     'Microsoldadura y diagnóstico a nivel componente para equipos que no encienden, no cargan o se reinician solos.',
     120000.00, '48 a 72 horas', 60, 0, 1),
    (4, 1, 'Desbloqueo de celular', 'desbloqueo-de-celular',
     'Liberación de patrones, cuentas y bloqueos de fábrica con verificación previa de titularidad del equipo.',
     38000.00, '2 a 6 horas', 30, 0, 1),
    (5, 2, 'Limpieza y mantenimiento', 'limpieza-y-mantenimiento',
     'Limpieza profunda de ventilación, cambio de pasta térmica y revisión de temperaturas para recuperar rendimiento.',
     35000.00, '2 a 3 horas', 60, 1, 1),
    (6, 2, 'Cambio de SSD y migración', 'cambio-de-ssd-y-migracion',
     'Instalamos un disco SSD nuevo, clonamos el sistema anterior y dejamos el equipo configurado y actualizado.',
     60000.00, '3 a 5 horas', 90, 1, 1),
    (7, 2, 'Reparación de bisagras y carcasa', 'reparacion-de-bisagras',
     'Reparamos bisagras flojas, carcasas partidas y marcos deformados para evitar daños mayores en la pantalla.',
     50000.00, '24 a 48 horas', 60, 0, 1),
    (8, 2, 'Reparación de notebook', 'reparacion-de-notebook',
     'Diagnóstico integral de notebooks: encendido, teclado, pantalla, carga y rendimiento general.',
     40000.00, '24 a 72 horas', 90, 0, 1),
    (9, 3, 'Optimización y formateo', 'optimizacion-y-formateo',
     'Respaldo de datos, instalación limpia del sistema, controladores y programas esenciales para dejar la PC como nueva.',
     30000.00, '3 a 5 horas', 30, 0, 1),
    (10, 3, 'Mantenimiento de consolas', 'mantenimiento-de-consolas',
     'Limpieza, cambio de pasta térmica y revisión de lector en consolas de última generación.',
     42000.00, '24 a 48 horas', 60, 0, 1),
    (11, 5, 'Recuperación de datos', 'recuperacion-de-datos',
     'Rescate de archivos de discos, pendrives y memorias dañadas. Evaluación previa sin cargo y presupuesto cerrado.',
     95000.00, '48 a 96 horas', 30, 0, 1),
    (12, 4, 'Reparación de electrónica general', 'reparacion-electronica-general',
     'Reparación de fuentes, placas de electrodomésticos y equipos de audio con instrumental de laboratorio.',
     55000.00, '48 a 72 horas', 60, 0, 1),
    (13, 6, 'Soporte técnico remoto', 'soporte-tecnico-remoto',
     'Asistencia a distancia para configuración, correo, redes y eliminación de virus, sin mover el equipo.',
     18000.00, '30 a 60 minutos', 15, 0, 1),
    (14, 4, 'Soldadura y microsoldadura', 'soldadura-y-microsoldadura',
     'Reparación de pistas, conectores y componentes SMD con estación de aire caliente y microscopio.',
     70000.00, '48 a 72 horas', 60, 0, 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT IGNORE INTO servicio_etiqueta (servicio_id, etiqueta_id) VALUES
    (1, 2), (1, 3), (2, 2), (2, 4), (3, 2), (5, 4), (6, 2), (11, 2);

-- ------------------------------------------------------------
-- CREATE · Productos de la tienda
-- ------------------------------------------------------------
INSERT INTO productos
    (id, categoria_id, nombre, descripcion, descripcion_corta, precio, stock, imagen, activo)
VALUES
    (1, 1, 'Batería para iPhone 11', 'Batería de litio con capacidad de 3.110 mAh y chip de gestión de carga. Incluye kit de instalación.', '3.110 mAh · Kit incluido', 45900.00, 12, 'bateria.svg', 1),
    (2, 1, 'Pantalla OLED Samsung A54', 'Módulo OLED completo con marco, táctil y flex de conexión. Compatible con Samsung Galaxy A54 5G.', 'OLED · Con marco', 89000.00, 6, 'pantalla.svg', 1),
    (3, 4, 'Cargador USB-C 65W', 'Cargador GaN de 65 W con dos puertos USB-C y uno USB-A. Protección contra sobrecarga.', 'GaN · 3 puertos', 28900.00, 25, 'cargador.svg', 1),
    (4, 3, 'Teclado mecánico TKL', 'Teclado compacto sin bloque numérico, switches táctiles y retroiluminación blanca regulable.', 'Switches táctiles · TKL', 74900.00, 8, 'teclado.svg', 1),
    (5, 3, 'Mouse inalámbrico', 'Mouse ergonómico de 2,4 GHz con sensor de 1.600 DPI y batería recargable por USB-C.', '2,4 GHz · Recargable', 21900.00, 30, 'mouse.svg', 1),
    (6, 2, 'SSD NVMe 1 TB', 'Unidad de estado sólido M.2 NVMe con lecturas de hasta 3.500 MB/s. Ideal para notebooks y PC.', 'M.2 NVMe · 3.500 MB/s', 112000.00, 10, 'ssd.svg', 1),
    (7, 2, 'Memoria RAM 16 GB DDR4', 'Módulo DDR4 de 16 GB a 3.200 MHz con disipador de aluminio y garantía de por vida.', 'DDR4 · 3.200 MHz', 68000.00, 15, 'memoria-ram.svg', 1),
    (8, 4, 'Auriculares Bluetooth', 'Auriculares inalámbricos con cancelación pasiva, estuche de carga y hasta 24 horas de autonomía.', 'Bluetooth 5.3 · 24 h', 39900.00, 3, 'auriculares.svg', 1),
    (9, 2, 'Batería para MacBook Air', 'Batería de reemplazo para MacBook Air 13" (2018-2020), con celdas de alta densidad y herramientas.', 'Compatible A1932 / A2179', 98000.00, 4, 'bateria.svg', 1),
    (10, 4, 'Cable USB-C trenzado', 'Cable de 2 metros con carga rápida de 100 W y transferencia de datos de 480 Mbps.', '2 m · 100 W', 9900.00, 40, 'cargador.svg', 1)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- ------------------------------------------------------------
-- Datos de demostración: reparaciones, pedidos y consultas
-- ------------------------------------------------------------
INSERT INTO reparaciones
    (id, codigo, usuario_id, servicio_id, estado_id, cliente_nombre, cliente_email, cliente_telefono,
     equipo_marca, equipo_modelo, problema, observaciones, costo_final, created_at)
VALUES
    (1, 'TF-260901-A1B2C3', 2, 1, 4, 'Lucía Fernández', 'cliente@demo.com', '+54 11 4444-2020',
     'Samsung', 'Galaxy A54', 'Pantalla rota por caída. El táctil responde de forma intermitente.',
     'Pantalla reemplazada. Se probaron táctil, brillo y calibración. Listo para control final.', 85000.00, '2026-09-01 10:20:00'),
    (2, 'TF-260902-D4E5F6', NULL, 6, 3, 'Julián Pérez', 'julian@demo.com', '+54 11 2222-9090',
     'Lenovo', 'IdeaPad 3', 'La notebook tarda mucho en iniciar y se traba al abrir programas.',
     'Se aprobó el cambio a SSD de 1 TB. En proceso de clonación del sistema.', 112000.00, '2026-09-02 15:45:00'),
    (3, 'TF-260903-G7H8I9', 2, 11, 5, 'Lucía Fernández', 'cliente@demo.com', '+54 11 4444-2020',
     'Kingston', 'Pendrive 64 GB', 'El pendrive no es reconocido y contiene fotos familiares.',
     'Recuperación exitosa: 2.480 archivos recuperados. Entregar con respaldo en disco externo.', 95000.00, '2026-09-03 09:10:00'),
    (4, 'TF-260904-J1K2L3', NULL, 2, 1, 'María Gómez', 'maria@demo.com', '+54 11 6666-7070',
     'Motorola', 'Moto G84', 'La batería dura menos de dos horas y el equipo se apaga al 20%.',
     'Equipo recibido. Pendiente de diagnóstico y presupuesto.', 0.00, '2026-09-04 17:30:00')
ON DUPLICATE KEY UPDATE codigo = VALUES(codigo);

INSERT INTO pedidos (id, numero, usuario_id, cliente_nombre, cliente_email, total, estado, created_at) VALUES
    (1, 'PED-260901-X1Y2Z3', 2, 'Lucía Fernández', 'cliente@demo.com', 72700.00, 'pagado', '2026-09-01 12:00:00'),
    (2, 'PED-260902-A4B5C6', NULL, 'Julián Pérez', 'julian@demo.com', 112000.00, 'pendiente', '2026-09-02 16:30:00')
ON DUPLICATE KEY UPDATE numero = VALUES(numero);

INSERT INTO detalle_pedidos (id, pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
    (1, 1, 3, 1, 28900.00, 28900.00),
    (2, 1, 5, 2, 21900.00, 43800.00),
    (3, 2, 6, 1, 112000.00, 112000.00)
ON DUPLICATE KEY UPDATE subtotal = VALUES(subtotal);

INSERT INTO consultas (id, nombre, email, telefono, asunto, mensaje, leido, created_at) VALUES
    (1, 'Ana Torres', 'ana@demo.com', '+54 11 7777-8080', 'Presupuesto de pantalla',
     'Hola, quería saber cuánto sale cambiar la pantalla de un iPhone 13 y si tienen el repuesto disponible.', 0, '2026-09-05 11:15:00'),
    (2, 'Pablo Díaz', 'pablo@demo.com', NULL, 'Consulta por garantía',
     'Compré una batería la semana pasada y quería consultar cómo funciona la garantía.', 1, '2026-09-06 09:40:00')
ON DUPLICATE KEY UPDATE asunto = VALUES(asunto);

-- ============================================================
-- PARTE 3 · CONSULTAS DE PRUEBA CRUD
-- ============================================================

-- CREATE · 1) Dos inserciones en tablas maestras
INSERT INTO categorias_producto (nombre, slug, descripcion) VALUES
    ('Gabinetes', 'gabinetes', 'Cajas y gabinetes para PC de escritorio'),
    ('Fuentes', 'fuentes', 'Fuentes de alimentación certificadas');

-- CREATE · 2) Dos inserciones en la tabla principal vinculando claves foráneas
INSERT INTO productos (categoria_id, nombre, descripcion_corta, precio, stock, imagen, activo)
SELECT id, 'Gabinete ATX con vidrio templado', 'ATX · Vidrio templado', 52000.00, 5, 'producto-generico.svg', 1
FROM categorias_producto WHERE slug = 'gabinetes';

INSERT INTO productos (categoria_id, nombre, descripcion_corta, precio, stock, imagen, activo)
SELECT id, 'Fuente 650 W 80 Plus Bronze', '650 W · 80+ Bronze', 61000.00, 7, 'producto-generico.svg', 1
FROM categorias_producto WHERE slug = 'fuentes';

-- READ · Filtrar con WHERE y ordenar con ORDER BY
SELECT id, nombre, precio, stock
FROM productos
WHERE activo = 1 AND precio > 20000
ORDER BY precio DESC;

-- READ · INNER JOIN entre reparaciones, servicios, estados y usuarios
SELECT
    r.codigo,
    s.nombre AS servicio,
    e.nombre AS estado,
    r.cliente_nombre,
    COALESCE(u.email, r.cliente_email) AS contacto,
    r.costo_final,
    r.created_at
FROM reparaciones r
INNER JOIN servicios s ON s.id = r.servicio_id
INNER JOIN estados_reparacion e ON e.id = r.estado_id
LEFT JOIN usuarios u ON u.id = r.usuario_id
ORDER BY r.created_at DESC;

-- READ · JOIN de pedidos con detalle y productos (relación N:M)
SELECT
    p.numero,
    p.cliente_nombre,
    pr.nombre AS producto,
    d.cantidad,
    d.precio_unitario,
    d.subtotal
FROM pedidos p
INNER JOIN detalle_pedidos d ON d.pedido_id = p.id
INNER JOIN productos pr ON pr.id = d.producto_id
WHERE p.estado = 'pagado'
ORDER BY p.numero, pr.nombre;

-- UPDATE · Modificar un registro específico usando WHERE
UPDATE productos
SET precio = 79900.00, stock = 9
WHERE id = 4;

-- DELETE · Dar de baja un registro de prueba usando WHERE
INSERT INTO productos (categoria_id, nombre, descripcion_corta, precio, stock, imagen, activo)
SELECT id, 'Producto de prueba (se elimina)', 'Registro temporal', 1000.00, 1, 'producto-generico.svg', 0
FROM categorias_producto WHERE slug = 'gabinetes';

DELETE FROM productos
WHERE nombre = 'Producto de prueba (se elimina)';

-- Verificación final: cantidad de registros por tabla principal
SELECT 'usuarios' AS tabla, COUNT(*) AS registros FROM usuarios
UNION ALL SELECT 'servicios', COUNT(*) FROM servicios
UNION ALL SELECT 'productos', COUNT(*) FROM productos
UNION ALL SELECT 'reparaciones', COUNT(*) FROM reparaciones
UNION ALL SELECT 'pedidos', COUNT(*) FROM pedidos;
