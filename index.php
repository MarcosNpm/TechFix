<?php
/**
 * index.php · Inicio
 * Renderizado server-side con plantillas modulares (TP4).
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/includes/componentes.php';

$titulo_pagina = 'Inicio';
$descripcion_pagina = 'TechFix: reparación de celulares, computadoras y electrónica con presupuesto claro, garantía y seguimiento online.';
$pagina_activa = 'inicio';

$serviciosDestacados = $pdo->query(
    "SELECT s.id, s.nombre, s.descripcion, s.precio_base, s.tiempo_estimado,
            c.nombre AS categoria, c.icono
     FROM servicios s
     INNER JOIN categorias_servicio c ON c.id = s.categoria_id
     WHERE s.activo = 1 AND s.destacado = 1
     ORDER BY s.id
     LIMIT 6"
)->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="contenedor hero__grid">
        <div class="hero__content">
            <span class="eyebrow"><?= icono('rayo') ?> Servicio técnico especializado</span>
            <h1>Tu equipo en manos <em>expertas</em>.</h1>
            <p class="hero__lead">
                Diagnóstico honesto, reparación con repuestos de calidad y seguimiento online.
                Cuidamos tus celulares, computadoras y equipos de electrónica como si fueran nuestros.
            </p>
            <div class="hero__actions">
                <a class="btn btn--primary btn--lg" href="<?= e(url('solicitud.php')) ?>">
                    Pedir presupuesto <?= icono('flecha') ?>
                </a>
                <a class="btn btn--ghost btn--lg" href="<?= e(url('servicios.php')) ?>">Ver servicios</a>
            </div>
            <div class="hero__trust">
                <span class="hero__trust-item"><?= icono('check-circle') ?> Garantía escrita</span>
                <span class="hero__trust-item"><?= icono('reloj') ?> Diagnóstico en 24 h</span>
                <span class="hero__trust-item"><?= icono('escudo') ?> Repuestos originales</span>
            </div>
        </div>

        <div class="hero__visual" aria-hidden="true">
            <div class="hero-art">
                <div class="hero-art__blob"></div>

                <div class="floating-card floating-card--1">
                    <span class="floating-card__icon"><?= icono('check-circle') ?></span>
                    <span><strong>Diagnóstico listo</strong><small>Sin cargo y sin sorpresas</small></span>
                </div>

                <div class="hero-art__device">
                    <div class="hero-art__screen">
                        <span class="hero-art__notch"></span>
                        <span class="hero-art__screen-label"><?= icono('chip') ?> TechFix · Orden TF-4821</span>
                        <h3>Control de calidad</h3>
                        <p>Tu reparación avanza paso a paso. Podés seguirla cuando quieras.</p>
                        <div class="hero-art__ring">
                            <span>78%<small>completado</small></span>
                        </div>
                        <div class="hero-art__line"><i></i></div>
                    </div>
                </div>

                <div class="floating-card floating-card--2">
                    <span class="floating-card__icon"><?= icono('escudo') ?></span>
                    <span><strong>90 días</strong><small>de garantía</small></span>
                </div>

                <div class="floating-card floating-card--3">
                    <span class="floating-card__icon"><?= icono('estrella') ?></span>
                    <span><strong>4,9 / 5</strong><small>+320 opiniones</small></span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="stats" aria-label="TechFix en números">
    <div class="contenedor stats__grid">
        <div class="stat"><span class="stat__num">2.400<span>+</span></span><span class="stat__label">Equipos reparados</span></div>
        <div class="stat"><span class="stat__num">4,9<span>/5</span></span><span class="stat__label">Valoración de clientes</span></div>
        <div class="stat"><span class="stat__num">90<span> días</span></span><span class="stat__label">Garantía escrita</span></div>
        <div class="stat"><span class="stat__num">24<span> h</span></span><span class="stat__label">Diagnóstico promedio</span></div>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="section-head">
            <div class="section-head__text">
                <span class="section-kicker">Lo que hacemos</span>
                <h2>Servicios pensados para que tu equipo vuelva a funcionar</h2>
                <p>Desde un cambio de pantalla hasta una reparación de placa. Siempre con presupuesto previo y explicación clara.</p>
            </div>
            <a class="section-head__link" href="<?= e(url('servicios.php')) ?>">Ver todos <?= icono('flecha') ?></a>
        </div>

        <div class="grid-3">
            <?php foreach ($serviciosDestacados as $servicio): ?>
                <?= tarjeta_servicio($servicio) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--soft">
    <div class="contenedor">
        <div class="section-head">
            <div class="section-head__text">
                <span class="section-kicker">Cómo trabajamos</span>
                <h2>Un proceso simple, sin letra chica</h2>
                <p>Sabés qué se va a hacer, cuánto sale y cuándo está listo antes de dejar tu equipo.</p>
            </div>
        </div>

        <div class="steps">
            <article class="step reveal">
                <h3>Nos contás la falla</h3>
                <p>Completás la solicitud con el modelo del equipo y una descripción del problema.</p>
            </article>
            <article class="step reveal">
                <h3>Diagnosticamos</h3>
                <p>Revisamos el equipo y te enviamos el presupuesto con el detalle de la reparación.</p>
            </article>
            <article class="step reveal">
                <h3>Reparamos</h3>
                <p>Con tu aprobación, el técnico trabaja con repuestos de calidad y herramientas adecuadas.</p>
            </article>
            <article class="step reveal">
                <h3>Hacés seguimiento</h3>
                <p>Con el código de orden consultás el estado y retirás cuando está listo.</p>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="grid-2" style="align-items:center; gap:56px">
            <div>
                <span class="section-kicker">Por qué TechFix</span>
                <h2>La confianza también se repara</h2>
                <p class="muted">
                    Sabemos que dejar un equipo es una decisión incómoda. Por eso mostramos precios
                    orientativos, explicamos cada paso y entregamos una garantía por escrito.
                </p>
                <ul class="detail-list" style="margin-top:24px">
                    <li><?= icono('check-circle') ?><span><strong>Presupuesto sin cargo.</strong> Si no aprobás, no pagás el diagnóstico.</span></li>
                    <li><?= icono('check-circle') ?><span><strong>Repuestos de calidad.</strong> Originales o compatibles certificados.</span></li>
                    <li><?= icono('check-circle') ?><span><strong>Seguimiento online.</strong> Consultá el avance con tu código.</span></li>
                    <li><?= icono('check-circle') ?><span><strong>Datos protegidos.</strong> Respaldo y confidencialidad en cada equipo.</span></li>
                </ul>
                <a class="btn btn--dark mt-24" href="<?= e(url('sobre-el-taller.php')) ?>">Conocer el taller <?= icono('flecha') ?></a>
            </div>
            <div class="card" style="padding:34px;background:linear-gradient(145deg,var(--navy),#242442);color:#fff;border:0;box-shadow:var(--shadow-lg)">
                <span class="eyebrow" style="background:rgba(0,168,204,.16);border-color:rgba(0,168,204,.35);color:#7fe3f4">Garantía TechFix</span>
                <h3 style="color:#fff;font-size:1.6rem;margin-top:10px">Si la falla vuelve, la revisamos sin costo.</h3>
                <p style="color:rgba(255,255,255,.65)">Todas las reparaciones incluyen entre 30 y 180 días de garantía según el servicio. La cobertura alcanza al trabajo realizado y al repuesto instalado.</p>
                <div style="display:grid;gap:12px;margin-top:24px">
                    <div style="display:flex;gap:10px;align-items:center;color:rgba(255,255,255,.8)"><?= icono('check') ?> Mano de obra incluida</div>
                    <div style="display:flex;gap:10px;align-items:center;color:rgba(255,255,255,.8)"><?= icono('check') ?> Pruebas documentadas</div>
                    <div style="display:flex;gap:10px;align-items:center;color:rgba(255,255,255,.8)"><?= icono('check') ?> Atención post-venta</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section section--soft">
    <div class="contenedor">
        <div class="section-head">
            <div class="section-head__text">
                <span class="section-kicker">Opiniones</span>
                <h2>Clientes que ya pasaron por el taller</h2>
            </div>
            <a class="section-head__link" href="<?= e(url('contacto.php')) ?>">Dejar mi opinión <?= icono('flecha') ?></a>
        </div>

        <div class="grid-3">
            <article class="testimonial reveal">
                <div class="testimonial__stars" aria-label="5 de 5 estrellas"><?= str_repeat(icono('estrella'), 5) ?></div>
                <blockquote>“Me cambiaron la pantalla del celular en el día y me explicaron todo el proceso. El precio fue exactamente el del presupuesto.”</blockquote>
                <div class="testimonial__author">
                    <span class="testimonial__avatar">L</span>
                    <div><strong>Lucía F.</strong><small>Cambio de pantalla · Samsung A54</small></div>
                </div>
            </article>
            <article class="testimonial reveal">
                <div class="testimonial__stars" aria-label="5 de 5 estrellas"><?= str_repeat(icono('estrella'), 5) ?></div>
                <blockquote>“Pensé que había perdido las fotos del pendrive. Recuperaron todo y me lo entregaron con un respaldo extra.”</blockquote>
                <div class="testimonial__author">
                    <span class="testimonial__avatar">M</span>
                    <div><strong>Martín R.</strong><small>Recuperación de datos</small></div>
                </div>
            </article>
            <article class="testimonial reveal">
                <div class="testimonial__stars" aria-label="5 de 5 estrellas"><?= str_repeat(icono('estrella'), 5) ?></div>
                <blockquote>“La notebook quedó como nueva después del cambio de SSD. Muy buena atención y seguimiento por el código de orden.”</blockquote>
                <div class="testimonial__author">
                    <span class="testimonial__avatar">J</span>
                    <div><strong>Julián P.</strong><small>Cambio de SSD · Lenovo IdeaPad</small></div>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="contenedor cta-band__grid">
        <div>
            <h2>¿Tu equipo no arranca o hace algo raro?</h2>
            <p>Contanos qué pasa y te respondemos con un diagnóstico y un presupuesto claro. Sin compromiso.</p>
        </div>
        <div class="cta-band__actions">
            <a class="btn btn--light btn--lg" href="<?= e(url('solicitud.php')) ?>">Solicitar reparación</a>
            <a class="btn btn--outline btn--lg" href="https://wa.me/<?= e(WHATSAPP) ?>" target="_blank" rel="noopener" style="color:#fff;border-color:rgba(255,255,255,.35)"><?= icono('whatsapp') ?> WhatsApp</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
