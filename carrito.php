<?php
/**
 * carrito.php · Carrito de compras con sesión y pedido persistido en MySQL.
 * Demuestra estado en servidor (TP8) y operaciones transaccionales (TP7).
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';

$titulo_pagina = 'Mi carrito';
$descripcion_pagina = 'Revisá los repuestos seleccionados y confirmá tu pedido.';
$pagina_activa = 'tienda';

if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

/** Devuelve los productos actuales del carrito con stock validado. */
function obtener_carrito(PDO $pdo, array $carrito): array
{
    $ids = array_values(array_filter(array_map('intval', array_keys($carrito))));
    if (empty($ids)) {
        return [];
    }

    $marcadores = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare(
        "SELECT p.id, p.nombre, p.precio, p.stock, p.imagen, c.nombre AS categoria
         FROM productos p
         INNER JOIN categorias_producto c ON c.id = p.categoria_id
         WHERE p.activo = 1 AND p.id IN ($marcadores)"
    );
    $stmt->execute($ids);
    $productos = $stmt->fetchAll();

    $items = [];
    $idsValidos = [];
    foreach ($productos as $producto) {
        $id = (int) $producto['id'];
        $stock = (int) $producto['stock'];

        // Un producto sin stock no se muestra ni se mantiene en la sesión.
        if ($stock <= 0) {
            continue;
        }

        $idsValidos[] = $id;
        $cantidad = max(1, min((int) ($carrito[$id] ?? 1), $stock));
        $items[] = [
            'producto' => $producto,
            'cantidad' => $cantidad,
            'subtotal' => $cantidad * (float) $producto['precio'],
        ];
    }

    // Elimina de la sesión los productos que ya no existen o están inactivos.
    foreach (array_keys($carrito) as $id) {
        if (!in_array((int) $id, $idsValidos, true)) {
            unset($_SESSION['carrito'][(int) $id]);
        }
    }

    return $items;
}

$mensajeGeneral = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificar_csrf()) {
        $mensajeGeneral = 'La sesión expiró. Recargá la página e intentá nuevamente.';
    } else {
        $accion = limpiar($_POST['accion'] ?? '');

        if ($accion === 'agregar') {
            $productoId = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT) ?: 0;
            $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT) ?: 1;
            $cantidad = max(1, min(99, $cantidad));

            $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = :id AND activo = 1 LIMIT 1");
            $stmt->execute([':id' => $productoId]);
            $producto = $stmt->fetch();

            if (!$producto) {
                flash('error', 'El producto no existe o ya no está disponible.');
            } elseif ((int) $producto['stock'] <= 0) {
                flash('error', 'Ese producto no tiene stock en este momento.');
            } else {
                $actual = (int) ($_SESSION['carrito'][$productoId] ?? 0);
                $_SESSION['carrito'][$productoId] = min((int) $producto['stock'], $actual + $cantidad);
                flash('success', 'Producto agregado al carrito.');
            }
            redirigir('carrito.php');
        }

        if ($accion === 'actualizar') {
            $cantidades = $_POST['cantidades'] ?? [];
            if (is_array($cantidades)) {
                $stmtStock = $pdo->prepare("SELECT stock FROM productos WHERE id = :id AND activo = 1 LIMIT 1");
                foreach ($cantidades as $id => $cantidad) {
                    $id = (int) $id;
                    $cantidad = (int) $cantidad;

                    if ($cantidad <= 0) {
                        unset($_SESSION['carrito'][$id]);
                        continue;
                    }

                    $stmtStock->execute([':id' => $id]);
                    $stock = $stmtStock->fetchColumn();

                    if ($stock === false || (int) $stock <= 0) {
                        unset($_SESSION['carrito'][$id]);
                        continue;
                    }

                    $_SESSION['carrito'][$id] = min((int) $stock, $cantidad, 99);
                }
                flash('success', 'Cantidades actualizadas.');
            }
            redirigir('carrito.php');
        }

        if ($accion === 'eliminar') {
            $productoId = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT) ?: 0;
            unset($_SESSION['carrito'][$productoId]);
            flash('info', 'Producto quitado del carrito.');
            redirigir('carrito.php');
        }

        if ($accion === 'confirmar') {
            $nombre = limpiar($_POST['cliente_nombre'] ?? '');
            $email = limpiar($_POST['cliente_email'] ?? '');
            $erroresCheckout = [];

            if (strlen($nombre) < 3) {
                $erroresCheckout[] = 'Ingresá tu nombre completo.';
            }
            if (!email_valido($email)) {
                $erroresCheckout[] = 'Ingresá un correo electrónico válido.';
            }
            if (empty($_SESSION['carrito'])) {
                $erroresCheckout[] = 'El carrito está vacío.';
            }

            if (!empty($erroresCheckout)) {
                $mensajeGeneral = implode(' ', $erroresCheckout);
            } else {
                $items = obtener_carrito($pdo, $_SESSION['carrito']);
                if (empty($items)) {
                    $mensajeGeneral = 'No hay productos disponibles en el carrito.';
                } else {
                    try {
                        $pdo->beginTransaction();
                        $numero = 'PED-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
                        $total = array_sum(array_column($items, 'subtotal'));
                        $usuario = usuario_actual();

                        $stmtPedido = $pdo->prepare(
                            "INSERT INTO pedidos (numero, usuario_id, cliente_nombre, cliente_email, total, estado)
                             VALUES (:numero, :usuario_id, :nombre, :email, :total, 'pendiente')"
                        );
                        $stmtPedido->execute([
                            ':numero'     => $numero,
                            ':usuario_id' => $usuario['id'] ?? null,
                            ':nombre'     => $nombre,
                            ':email'      => $email,
                            ':total'      => $total,
                        ]);
                        $pedidoId = (int) $pdo->lastInsertId();

                        $stmtDetalle = $pdo->prepare(
                            "INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
                             VALUES (:pedido_id, :producto_id, :cantidad, :precio, :subtotal)"
                        );
                        $stmtStock = $pdo->prepare(
                            "UPDATE productos SET stock = stock - :cantidad WHERE id = :id AND stock >= :cantidad"
                        );

                        foreach ($items as $item) {
                            $producto = $item['producto'];
                            $stmtStock->execute([
                                ':cantidad' => $item['cantidad'],
                                ':id'       => (int) $producto['id'],
                            ]);

                            if ($stmtStock->rowCount() !== 1) {
                                throw new RuntimeException('Stock insuficiente para ' . $producto['nombre']);
                            }

                            $stmtDetalle->execute([
                                ':pedido_id'   => $pedidoId,
                                ':producto_id' => (int) $producto['id'],
                                ':cantidad'    => $item['cantidad'],
                                ':precio'      => $producto['precio'],
                                ':subtotal'    => $item['subtotal'],
                            ]);
                        }

                        $pdo->commit();
                        $_SESSION['carrito'] = [];
                        flash('success', 'Pedido registrado correctamente.');
                        redirigir('confirmacion.php?pedido=' . urlencode($numero));
                    } catch (Throwable $e) {
                        if ($pdo->inTransaction()) {
                            $pdo->rollBack();
                        }
                        error_log('[TechFix] Error al confirmar pedido: ' . $e->getMessage());
                        $mensajeGeneral = 'No pudimos cerrar el pedido. Revisá el stock e intentá nuevamente.';
                    }
                }
            }
        }
    }
}

$items = obtener_carrito($pdo, $_SESSION['carrito']);
$total = array_sum(array_column($items, 'subtotal'));
$usuario = usuario_actual();

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('index.php')) ?>">Inicio</a> <?= icono('flecha') ?>
            <a href="<?= e(url('tienda.php')) ?>">Tienda</a> <?= icono('flecha') ?>
            <span>Carrito</span>
        </nav>
        <span class="eyebrow"><?= icono('carrito') ?> Revisá tu compra</span>
        <h1>Mi carrito</h1>
        <p>Verificá los productos, las cantidades y el total antes de confirmar el pedido.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <?php if ($mensajeGeneral !== ''): ?>
            <div class="alert alert--error">
                <?= icono('info') ?>
                <div><strong>Revisá los datos</strong><?= e($mensajeGeneral) ?></div>
            </div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <div class="empty-state">
                <div class="empty-state__icon"><?= icono('carrito') ?></div>
                <h3>Tu carrito está vacío</h3>
                <p>Agregá repuestos o accesorios desde la tienda para comenzar.</p>
                <a class="btn btn--primary" href="<?= e(url('tienda.php')) ?>">Ir a la tienda <?= icono('flecha') ?></a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <div>
                    <div class="cart-list">
                        <?php foreach ($items as $item): ?>
                            <?php $producto = $item['producto']; ?>
                            <form class="cart-item" method="post" action="<?= e(url('carrito.php')) ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="producto_id" value="<?= (int) $producto['id'] ?>">

                                <div class="cart-item__media">
                                    <img src="<?= e(url('assets/img/' . $producto['imagen'])) ?>" alt="<?= e($producto['nombre']) ?>">
                                </div>

                                <div>
                                    <h3><?= e($producto['nombre']) ?></h3>
                                    <p class="cart-item__meta"><?= e($producto['categoria']) ?> · <?= e(precio($producto['precio'])) ?> c/u</p>
                                    <p class="cart-item__meta">Stock disponible: <?= (int) $producto['stock'] ?></p>
                                </div>

                                <div class="cart-item__controls">
                                    <div class="qty" data-qty>
                                        <button type="button" data-qty-menos aria-label="Restar unidad">−</button>
                                        <label class="sr-only" for="cantidad-<?= (int) $producto['id'] ?>">Cantidad</label>
                                        <input type="number" id="cantidad-<?= (int) $producto['id'] ?>" name="cantidades[<?= (int) $producto['id'] ?>]"
                                               value="<?= (int) $item['cantidad'] ?>" min="1" max="<?= (int) $producto['stock'] ?>" inputmode="numeric">
                                        <button type="button" data-qty-mas aria-label="Sumar unidad">+</button>
                                    </div>
                                    <div>
                                        <div class="cart-item__subtotal"><?= e(precio($item['subtotal'])) ?></div>
                                        <button class="cart-item__remove" type="submit" name="accion" value="eliminar">
                                            <?= icono('eliminar') ?> Quitar
                                        </button>
                                    </div>
                                </div>

                                <div style="grid-column:1/-1;display:flex;justify-content:flex-end">
                                    <button class="btn btn--ghost btn--sm" type="submit" name="accion" value="actualizar">Actualizar cantidad</button>
                                </div>
                            </form>
                        <?php endforeach; ?>
                    </div>

                    <div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:18px">
                        <a class="btn btn--ghost" href="<?= e(url('tienda.php')) ?>"><?= icono('flecha-izq') ?> Seguir comprando</a>
                    </div>
                </div>

                <aside class="summary-card">
                    <h2>Resumen del pedido</h2>
                    <?php foreach ($items as $item): ?>
                        <div class="summary-row">
                            <span><?= (int) $item['cantidad'] ?> × <?= e($item['producto']['nombre']) ?></span>
                            <span><?= e(precio($item['subtotal'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="summary-row summary-row--total">
                        <span>Total</span>
                        <strong><?= e(precio($total)) ?></strong>
                    </div>

                    <form method="post" action="<?= e(url('carrito.php')) ?>" style="margin-top:20px">
                        <?= csrf_field() ?>
                        <input type="hidden" name="accion" value="confirmar">

                        <div class="form-group" style="margin-bottom:14px">
                            <label for="cliente_nombre">Nombre y apellido</label>
                            <input class="form-control" type="text" id="cliente_nombre" name="cliente_nombre"
                                   value="<?= e($usuario['nombre'] ?? '') ?>" placeholder="Tu nombre" maxlength="100" required>
                        </div>
                        <div class="form-group" style="margin-bottom:16px">
                            <label for="cliente_email">Correo electrónico</label>
                            <input class="form-control" type="email" id="cliente_email" name="cliente_email"
                                   value="<?= e($usuario['email'] ?? '') ?>" placeholder="nombre@correo.com" maxlength="150" required>
                        </div>

                        <button class="btn btn--primary btn--block btn--lg" type="submit">Confirmar pedido <?= icono('flecha') ?></button>
                        <p class="form-help" style="text-align:center;margin-top:10px">No se cobra nada en este paso. Coordinamos el pago por correo o WhatsApp.</p>
                    </form>

                    <div class="tracking-note" style="margin-top:18px">
                        <?= icono('escudo') ?>
                        <span>Compra protegida. Si un producto no tiene stock, te avisamos antes de cobrar.</span>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
