<?php
/**
 * includes/footer.php
 * Pie de página común e inclusión de los scripts JavaScript.
 */
?>
</main>

<footer class="site-footer">
    <div class="contenedor footer-grid">
        <div class="footer-brand">
            <a class="logo logo--footer" href="<?= e(url('index.php')) ?>">
                <span class="logo__mark" aria-hidden="true">
                    <svg viewBox="0 0 40 40" fill="none">
                        <rect x="2" y="2" width="36" height="36" rx="11" fill="#FFFFFF" opacity=".08"/>
                        <path d="M23.8 10.5a6.4 6.4 0 0 0-8.1 8.1L9 25.3v4.4h4.4l6.7-6.7a6.4 6.4 0 0 0 8.1-8.1l-3.4 3.4-3.1-3.1 2.1-4.7Z" fill="#00A8CC"/>
                    </svg>
                </span>
                <span class="logo__text logo__text--footer">Tech<span>Fix</span></span>
            </a>
            <p>Reparamos celulares, computadoras y electrónica con diagnóstico claro, garantía escrita y seguimiento online.</p>
            <div class="footer-social">
                <a href="https://wa.me/<?= e(WHATSAPP) ?>" aria-label="WhatsApp" target="_blank" rel="noopener"><?= icono('whatsapp') ?></a>
                <a href="https://instagram.com" aria-label="Instagram" target="_blank" rel="noopener"><?= icono('instagram') ?></a>
                <a href="https://facebook.com" aria-label="Facebook" target="_blank" rel="noopener"><?= icono('facebook') ?></a>
                <a href="mailto:<?= e(SUPPORT_EMAIL) ?>" aria-label="Correo electrónico"><?= icono('mail') ?></a>
            </div>
        </div>

        <div>
            <h2 class="footer-title">Navegación</h2>
            <ul class="footer-links">
                <li><a href="<?= e(url('servicios.php')) ?>">Servicios</a></li>
                <li><a href="<?= e(url('tienda.php')) ?>">Tienda de repuestos</a></li>
                <li><a href="<?= e(url('seguimiento.php')) ?>">Seguimiento</a></li>
                <li><a href="<?= e(url('sobre-el-taller.php')) ?>">Sobre el taller</a></li>
                <li><a href="<?= e(url('contacto.php')) ?>">Contacto</a></li>
            </ul>
        </div>

        <div>
            <h2 class="footer-title">Servicios</h2>
            <ul class="footer-links">
                <li><a href="<?= e(url('servicios.php?categoria=celulares')) ?>">Reparación de celulares</a></li>
                <li><a href="<?= e(url('servicios.php?categoria=computadoras')) ?>">Reparación de computadoras</a></li>
                <li><a href="<?= e(url('servicios.php?categoria=mantenimiento')) ?>">Mantenimiento preventivo</a></li>
                <li><a href="<?= e(url('servicios.php?categoria=recuperacion-de-datos')) ?>">Recuperación de datos</a></li>
                <li><a href="<?= e(url('servicios.php?categoria=electronica')) ?>">Electrónica y soldadura</a></li>
            </ul>
        </div>

        <div>
            <h2 class="footer-title">Contacto</h2>
            <ul class="footer-contact">
                <li><?= icono('pin') ?><span><?= e(SHOP_ADDRESS) ?></span></li>
                <li><?= icono('telefono') ?><a href="tel:<?= e(preg_replace('/\s+/', '', SUPPORT_PHONE)) ?>"><?= e(SUPPORT_PHONE) ?></a></li>
                <li><?= icono('mail') ?><a href="mailto:<?= e(SUPPORT_EMAIL) ?>"><?= e(SUPPORT_EMAIL) ?></a></li>
                <li><?= icono('reloj') ?><span>Lun a Vie 9–19 h · Sáb 9–13 h</span></li>
            </ul>
            <a class="btn btn--outline btn--sm footer-cta" href="<?= e(url('solicitud.php')) ?>">Pedir presupuesto</a>
        </div>
    </div>

    <div class="contenedor footer-bottom">
        <p>© <?= date('Y') ?> <?= e(APP_NAME) ?>. Todos los derechos reservados.</p>
        <p class="footer-bottom__meta">
            Programación Web · Núñez Marcos Agustín ·
            <a href="<?= e(url('clase5/index.php')) ?>">Demo Clase 5</a> ·
            <a href="<?= e(url('mvc/index.php')) ?>">Versión MVC</a>
        </p>
    </div>
</footer>

<?php if (!empty($script_pagina)): ?>
    <script src="<?= e(url($script_pagina)) ?>" defer></script>
<?php endif; ?>
</body>
</html>
