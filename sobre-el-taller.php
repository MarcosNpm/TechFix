<?php
/**
 * sobre-el-taller.php · Historia, equipo y términos del taller.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$titulo_pagina = 'Sobre el taller';
$descripcion_pagina = 'Conocé la historia de TechFix, cómo trabajamos y el equipo detrás del taller.';
$pagina_activa = 'taller';

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="contenedor">
        <nav class="breadcrumb" aria-label="Miga de pan">
            <a href="<?= e(url('index.php')) ?>">Inicio</a> <?= icono('flecha') ?>
            <span>Sobre el taller</span>
        </nav>
        <span class="eyebrow"><?= icono('herramienta') ?> Desde 2018</span>
        <h1>Un taller chico, con vocación por reparar</h1>
        <p>TechFix nació para resolver lo que otros descartan: entender la falla, explicarla y devolverle la vida útil al equipo.</p>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="grid-2" style="align-items:center;gap:54px">
            <div>
                <span class="section-kicker">Nuestra historia</span>
                <h2>Empezamos con una mesa, un soldador y muchas ganas</h2>
                <p class="muted">
                    En 2018 Marcos empezó reparando celulares de amigos en el garage de su casa. Lo que era un hobby
                    se transformó en un taller con banco de trabajo, instrumental de medición y un flujo de trabajo
                    pensado para no improvisar.
                </p>
                <p class="muted">
                    Hoy atendemos celulares, computadoras, consolas y equipos de electrónica. La idea sigue siendo
                    la misma: diagnóstico honesto, presupuesto claro y un trabajo que aguante el uso diario.
                </p>
                <div class="grid-2" style="gap:14px;margin-top:24px">
                    <div class="card" style="padding:18px">
                        <span style="color:var(--cyan-dark);font-weight:800;font-size:1.4rem">+2.400</span>
                        <p class="muted" style="margin:0;font-size:.8rem">equipos reparados</p>
                    </div>
                    <div class="card" style="padding:18px">
                        <span style="color:var(--cyan-dark);font-weight:800;font-size:1.4rem">7 años</span>
                        <p class="muted" style="margin:0;font-size:.8rem">de experiencia</p>
                    </div>
                </div>
            </div>
            <div class="card" style="padding:0;overflow:hidden;border:0;box-shadow:var(--shadow-lg);background:var(--navy)">
                <img src="<?= e(url('assets/img/taller.svg')) ?>" alt="Ilustración del banco de trabajo de TechFix" style="display:block;width:100%">
            </div>
        </div>
    </div>
</section>

<section class="section section--soft">
    <div class="contenedor">
        <div class="section-head">
            <div class="section-head__text">
                <span class="section-kicker">Cómo pensamos</span>
                <h2>Cuatro principios que no negociamos</h2>
            </div>
        </div>
        <div class="grid-4">
            <article class="step reveal">
                <div style="color:var(--cyan-dark);margin-bottom:12px"><?= icono('ojo', 'icono') ?></div>
                <h3>Transparencia</h3>
                <p>Te mostramos el presupuesto antes de tocar el equipo y explicamos qué se va a cambiar.</p>
            </article>
            <article class="step reveal">
                <div style="color:var(--cyan-dark);margin-bottom:12px"><?= icono('escudo', 'icono') ?></div>
                <h3>Responsabilidad</h3>
                <p>Si algo no sale como esperábamos, lo asumimos. La garantía está escrita y se cumple.</p>
            </article>
            <article class="step reveal">
                <div style="color:var(--cyan-dark);margin-bottom:12px"><?= icono('chip', 'icono') ?></div>
                <h3>Precisión</h3>
                <p>Usamos instrumental adecuado y probamos cada función antes de entregar.</p>
            </article>
            <article class="step reveal">
                <div style="color:var(--cyan-dark);margin-bottom:12px"><?= icono('soporte', 'icono') ?></div>
                <h3>Cercanía</h3>
                <p>Respondemos consultas después de la entrega. Un equipo reparado no termina en la puerta.</p>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="contenedor">
        <div class="section-head">
            <div class="section-head__text">
                <span class="section-kicker">El equipo</span>
                <h2>Quiénes están detrás del mostrador</h2>
                <p>Un equipo chico que se reparte el diagnóstico, la reparación y la atención al cliente.</p>
            </div>
        </div>
        <div class="grid-3">
            <article class="testimonial reveal">
                <div class="testimonial__avatar" style="width:56px;height:56px;font-size:1.2rem;margin-bottom:14px">M</div>
                <h3>Marcos Núñez</h3>
                <p class="muted" style="font-size:.8rem;margin-bottom:12px">Fundador · Técnico en electrónica</p>
                <p style="font-size:.86rem;color:var(--muted);margin:0">Especialista en microsoldadura y recuperación de datos. Le gusta entender por qué falla cada equipo.</p>
            </article>
            <article class="testimonial reveal">
                <div class="testimonial__avatar" style="width:56px;height:56px;font-size:1.2rem;margin-bottom:14px;background:#F0EAFF;color:#6941C6">S</div>
                <h3>Sofía Ríos</h3>
                <p class="muted" style="font-size:.8rem;margin-bottom:12px">Atención al cliente</p>
                <p style="font-size:.86rem;color:var(--muted);margin:0">Coordina presupuestos, repuestos y entregas. Es la voz que te responde por WhatsApp.</p>
            </article>
            <article class="testimonial reveal">
                <div class="testimonial__avatar" style="width:56px;height:56px;font-size:1.2rem;margin-bottom:14px;background:#FFF4DE;color:#A15C00">D</div>
                <h3>Diego Ramírez</h3>
                <p class="muted" style="font-size:.8rem;margin-bottom:12px">Técnico de computadoras</p>
                <p style="font-size:.86rem;color:var(--muted);margin:0">Armado, diagnóstico de notebooks y optimización de sistemas. Fanático de los benchmarks.</p>
            </article>
        </div>
    </div>
</section>

<section class="section section--soft">
    <div class="contenedor">
        <div class="grid-2" style="align-items:center;gap:54px">
            <div>
                <span class="section-kicker">Nuestro proceso</span>
                <h2>Así cuidamos tu equipo</h2>
                <div class="timeline" style="margin-top:28px">
                    <div class="timeline__item completado">
                        <span class="timeline__dot"><?= icono('check') ?></span>
                        <div class="timeline__content"><strong>Recepción y registro</strong><small>Anotamos el estado del equipo y los datos de contacto.</small></div>
                    </div>
                    <div class="timeline__item completado">
                        <span class="timeline__dot"><?= icono('check') ?></span>
                        <div class="timeline__content"><strong>Diagnóstico técnico</strong><small>Medimos, desarmamos y detectamos el origen de la falla.</small></div>
                    </div>
                    <div class="timeline__item actual">
                        <span class="timeline__dot">3</span>
                        <div class="timeline__content"><strong>Presupuesto y aprobación</strong><small>Te enviamos el detalle y esperamos tu OK antes de reparar.</small></div>
                    </div>
                    <div class="timeline__item">
                        <span class="timeline__dot">4</span>
                        <div class="timeline__content"><strong>Reparación y control</strong><small>Cambiamos el repuesto y probamos todas las funciones.</small></div>
                    </div>
                    <div class="timeline__item">
                        <span class="timeline__dot">5</span>
                        <div class="timeline__content"><strong>Entrega con garantía</strong><small>Te explicamos qué se hizo y entregamos la garantía escrita.</small></div>
                    </div>
                </div>
            </div>
            <div class="form-card">
                <h3>Datos del taller</h3>
                <ul class="footer-contact" style="margin-top:18px;color:var(--text)">
                    <li><?= icono('pin') ?><span><strong>Dirección</strong><br><?= e(SHOP_ADDRESS) ?></span></li>
                    <li><?= icono('telefono') ?><span><strong>Teléfono</strong><br><?= e(SUPPORT_PHONE) ?></span></li>
                    <li><?= icono('mail') ?><span><strong>Correo</strong><br><?= e(SUPPORT_EMAIL) ?></span></li>
                    <li><?= icono('reloj') ?><span><strong>Horarios</strong><br>Lunes a viernes de 9 a 19 h<br>Sábados de 9 a 13 h</span></li>
                </ul>
                <a class="btn btn--primary btn--block" href="<?= e(url('contacto.php')) ?>" style="margin-top:20px">Contactar al taller</a>
            </div>
        </div>
    </div>
</section>

<section class="section" id="terminos">
    <div class="contenedor" style="max-width:900px">
        <span class="section-kicker">Legales</span>
        <h2>Términos y condiciones del servicio</h2>
        <div class="form-card" style="margin-top:24px">
            <h3>1. Diagnóstico y presupuesto</h3>
            <p class="muted">El diagnóstico permite determinar la falla y el costo de la reparación. Si el cliente rechaza el presupuesto, no abona el diagnóstico. El equipo debe retirarse dentro de los 30 días.</p>
            <h3>2. Garantía</h3>
            <p class="muted">La garantía cubre la mano de obra y el repuesto instalado durante el plazo informado en cada servicio (entre 15 y 180 días). No cubre daños por golpes, humedad, intervención de terceros o uso indebido.</p>
            <h3>3. Datos personales</h3>
            <p class="muted">Los datos de contacto se usan exclusivamente para gestionar la reparación, el presupuesto y el seguimiento. No se comparten con terceros ajenos al servicio.</p>
            <h3>4. Equipos no retirados</h3>
            <p class="muted">Después de 90 días de la notificación de "listo para retirar", el taller podrá aplicar un cargo por almacenamiento o disponer del equipo según la normativa vigente.</p>
            <h3>5. Respaldo de información</h3>
            <p class="muted">Recomendamos respaldar los datos antes de dejar el equipo. TechFix no se responsabiliza por pérdidas de información derivadas de fallas previas del dispositivo.</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
