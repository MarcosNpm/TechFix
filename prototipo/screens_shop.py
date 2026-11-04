#!/usr/bin/env python3
"""Pantallas de tienda, contacto, cuenta y mapa del sitio."""

from proto_lib import (
    Screen, DARK, DARK_2, CYAN, CYAN_DARK, CYAN_SOFT, BG, WHITE, TEXT, MUTED,
    BORDER, GREEN, GREEN_SOFT, ORANGE, ORANGE_SOFT, RED, RED_SOFT, PURPLE,
    PURPLE_SOFT, BLUE_SOFT,
)
from screens_public import PRODUCTS, money


def product_image(s, x, y, w, h, kind, scale=1.0):
    s.rect(x, y, w, h, "#F7FAFC", 14, BORDER)
    cx, cy = x + w / 2, y + h / 2
    if scale != 1:
        s.add(f'<g transform="translate({cx} {cy}) scale({scale}) translate({-cx} {-cy})">')
    if kind == "ssd":
        s.rect(cx - 52, cy - 30, 104, 60, DARK, 10)
        s.text(cx, cy + 6, "SSD", 16, CYAN, 700, "middle", 2)
        s.line(cx - 38, cy + 22, cx + 38, cy + 22, "#44506A", 3)
    elif kind == "ram":
        s.rect(cx - 58, cy - 24, 116, 48, "#1F3A5F", 8)
        for i in range(5):
            s.rect(cx - 48 + i * 20, cy - 14, 10, 28, CYAN, 2)
    elif kind == "battery":
        s.rect(cx - 48, cy - 28, 96, 56, "none", 10, CYAN_DARK, 3)
        s.rect(cx + 48, cy - 10, 8, 20, CYAN_DARK, 2)
        s.line(cx - 34, cy, cx + 34, cy, CYAN_DARK, 3)
    elif kind == "bolt":
        s.path(f"M {cx + 10} {cy - 38} L {cx - 26} {cy + 6} L {cx - 2} {cy + 6} L {cx - 10} {cy + 38} L {cx + 28} {cy - 10} L {cx + 4} {cy - 10} Z", CYAN_DARK)
    elif kind == "grid":
        s.rect(cx - 60, cy - 26, 120, 52, DARK, 8)
        for r in range(2):
            for c in range(5):
                s.rect(cx - 50 + c * 22, cy - 16 + r * 20, 14, 12, CYAN, 2)
    elif kind == "box":
        s.path(f"M {cx - 55} {cy - 20} L {cx} {cy - 42} L {cx + 55} {cy - 20} L {cx + 55} {cy + 22} L {cx} {cy + 44} L {cx - 55} {cy + 22} Z", "none", CYAN_DARK, 3)
        s.line(cx - 55, cy - 20, cx, cy + 2, CYAN_DARK, 3)
        s.line(cx + 55, cy - 20, cx, cy + 2, CYAN_DARK, 3)
    elif kind == "phone":
        s.rect(cx - 24, cy - 40, 48, 80, DARK, 14)
        s.rect(cx - 17, cy - 31, 34, 58, "#2A3A55", 6)
        s.circle(cx, cy + 34, 4, CYAN)
    elif kind == "screen":
        s.rect(cx - 58, cy - 36, 116, 66, DARK, 6)
        s.rect(cx - 50, cy - 28, 100, 50, "#2A3A55", 3)
        s.rect(cx - 12, cy + 30, 24, 6, DARK, 2)
        s.rect(cx - 32, cy + 36, 64, 5, DARK, 2)
    if scale != 1:
        s.add("</g>")


def screen_tracking():
    s = Screen("06-seguimiento", "Seguimiento · TechFix", height=1240)
    s.header("seguimiento")
    s.breadcrumb(150, "Seguimiento")
    s.pill(152, 180, 210, 28, "SEGUIMIENTO ONLINE", CYAN_SOFT, CYAN_DARK, size=10)
    s.text(152, 260, "¿Cómo va tu reparación?", 44, DARK, 700, ls=-1.2)
    s.para(152, 292, "Ingresá el código que te dimos al recibir el equipo. Podés consultarlo las veces que quieras.", 620, 14, 22, MUTED)

    s.card(152, 356, 720, 92, 16, WHITE, BORDER, True)
    s.icon("search", 180, 382, 22, MUTED)
    s.text(218, 400, "TF-260901-A1B2C3", 15, DARK, 600)
    s.button(620, 378, 130, 48, "Buscar", "primary", 12)
    s.button(762, 378, 90, 48, "Limpiar", "ghost", 11)

    s.card(152, 490, 720, 400, 20, WHITE, BORDER, True)
    s.text(182, 530, "Reparación TF-260901-A1B2C3", 18, DARK, 700)
    s.badge(702, 512, "En reparación", PURPLE, PURPLE_SOFT, 120)
    s.line(182, 556, 842, 556, BORDER, 1)
    s.text(182, 586, "Samsung Galaxy A54", 16, DARK, 700)
    s.text(182, 608, "Cambio de pantalla · Ingresó el 01/09/2026", 11, MUTED)

    steps = [
        ("Recibido", "01/09 · 09:15", True),
        ("En diagnóstico", "01/09 · 11:40", True),
        ("En reparación", "02/09 · 10:20", True),
        ("Control de calidad", "Pendiente", False),
        ("Listo para retirar", "Pendiente", False),
    ]
    for i, (name, date, done) in enumerate(steps):
        y = 650 + i * 44
        color = CYAN if done else "#CDD5DF"
        s.circle(202, y, 10, color if done else WHITE, color if not done else None, 2)
        if done:
            s.icon("check", 195, y - 7, 14, WHITE)
        if i < len(steps) - 1:
            s.line(202, y + 12, 202, y + 32, color, 2)
        s.text(226, y + 5, name, 12, DARK if done else MUTED, 700 if done else 500)
        s.text(842, y + 5, date, 10, MUTED, 400, "end")

    s.card(902, 490, 386, 400, 20, WHITE, BORDER, True)
    s.text(932, 530, "Detalle del equipo", 16, DARK, 700)
    details = [("Cliente", "Lucía Fernández"), ("Equipo", "Samsung Galaxy A54"), ("Servicio", "Cambio de pantalla"), ("Costo estimado", "$ 85.000"), ("Garantía", "90 días")]
    for i, (k, v) in enumerate(details):
        s.text(932, 568 + i * 42, k, 10, MUTED, 700, ls=0.6)
        s.text(1258, 568 + i * 42, v, 11, DARK, 600, "end")
    s.line(932, 786, 1258, 786, BORDER, 1)
    s.text(932, 812, "¿Necesitás ayuda?", 11, DARK, 700)
    s.button(932, 826, 326, 38, "Contactar al taller", "outline", 10)

    s.rect(152, 930, 1136, 52, CYAN_SOFT, 14)
    s.text(176, 962, "El estado se actualiza cuando el técnico carga cada avance. Si tenés dudas, escribinos con el código a mano.", 11, CYAN_DARK)
    s.footer(1000)
    return s


def screen_store():
    s = Screen("07-tienda", "Tienda de repuestos · TechFix", height=1420)
    s.header("tienda")
    s.breadcrumb(150, "Tienda de repuestos")
    s.text(152, 248, "Repuestos y accesorios", 46, DARK, 700, ls=-1.5)
    s.para(152, 280, "Productos probados por el taller, con stock real y garantía. Hacé tu pedido online y retiralo cuando esté listo.", 600, 14, 22, MUTED)

    s.card(152, 344, 560, 54, 12, WHITE, BORDER, True)
    s.icon("search", 174, 360, 20, MUTED)
    s.text(204, 377, "Buscar por nombre, marca o categoría...", 12, "#98A2B3")
    s.button(728, 344, 110, 54, "Buscar", "primary", 12)
    s.pill(852, 354, 104, 34, "Stock", WHITE, CYAN_DARK, BORDER, size=10)
    s.pill(968, 354, 130, 34, "Ordenar", WHITE, TEXT, BORDER, size=10)
    s.chevron(1064, 368, MUTED, 9)

    cats = ["Todos", "Almacenamiento", "Componentes", "Repuestos", "Accesorios", "Periféricos", "Audio"]
    x = 152
    for i, label in enumerate(cats):
        w = len(label) * 7 + 30
        if i == 0:
            s.pill(x, 428, w, 32, label, DARK, WHITE, size=10)
        else:
            s.pill(x, 428, w, 32, label, WHITE, TEXT, BORDER, size=10)
        x += w + 10

    for idx, (name, cat, price, icon) in enumerate(PRODUCTS):
        col, row = idx % 4, idx // 4
        x = 152 + col * 288
        y = 496 + row * 330
        s.card(x, y, 272, 306, 16, WHITE, BORDER, True)
        product_image(s, x + 14, y + 14, 244, 140, icon)
        s.pill(x + 18, y + 168, len(cat) * 6.6 + 22, 21, cat.upper(), "#F4F7F9", MUTED, size=8)
        s.text(x + 18, y + 214, name, 14, DARK, 700)
        s.text(x + 18, y + 246, money(price), 18, DARK, 700)
        s.text(x + 18, y + 262, "Stock disponible" if idx not in (4, 6) else "Últimas unidades", 9, GREEN if idx not in (4, 6) else ORANGE, 600)
        s.button(x + 18, y + 272, 236, 28, "Agregar al carrito", "outline", 9)

    s.footer(1180)
    return s


def screen_cart():
    s = Screen("08-carrito", "Carrito · TechFix", height=1040)
    s.header("tienda")
    s.breadcrumb(150, "Carrito")
    s.text(152, 236, "Tu carrito", 42, DARK, 700, ls=-1.2)
    s.text(1080, 236, "2 productos", 12, MUTED, 600, "end")

    s.card(152, 280, 760, 420, 18, WHITE, BORDER, True)
    rows = [("SSD NVMe 1 TB", "Almacenamiento", 112000, 1, "ssd"), ("Memoria RAM 16 GB", "Componentes", 78000, 1, "ram")]
    for i, (name, cat, price, qty, icon) in enumerate(rows):
        y = 304 + i * 130
        product_image(s, 176, y, 100, 90, icon)
        s.text(296, y + 28, name, 15, DARK, 700)
        s.text(296, y + 50, cat, 10, MUTED)
        s.text(296, y + 76, "Garantía 90 días", 9, CYAN_DARK, 700)
        s.rect(640, y + 34, 76, 32, WHITE, 8, BORDER)
        s.text(660, y + 55, "-", 14, MUTED, 700)
        s.text(678, y + 55, str(qty), 12, DARK, 700, "middle")
        s.text(702, y + 55, "+", 14, CYAN_DARK, 700)
        s.text(892, y + 55, money(price), 14, DARK, 700, "end")
        if i == 0:
            s.line(176, 418, 892, 418, BORDER, 1)
    s.text(176, 670, "‹ Seguir comprando", 11, CYAN_DARK, 700)

    s.card(940, 280, 348, 306, 18, WHITE, BORDER, True)
    s.text(968, 320, "Resumen del pedido", 16, DARK, 700)
    for i, (k, v) in enumerate([("Subtotal", "$ 190.000"), ("Envío", "A coordinar"), ("Descuento", "—")]):
        s.text(968, 358 + i * 34, k, 11, MUTED)
        s.text(1260, 358 + i * 34, v, 11, DARK, 600, "end")
    s.line(968, 462, 1260, 462, BORDER, 1)
    s.text(968, 496, "Total", 14, DARK, 700)
    s.text(1260, 496, "$ 190.000", 20, DARK, 700, "end")
    s.button(968, 520, 292, 42, "Finalizar pedido")
    s.text(1114, 578, "Retiro en el taller sin cargo", 9, MUTED, 400, "middle")

    s.rect(940, 600, 348, 100, CYAN_SOFT, 16)
    s.icon("shield", 960, 620, 22, CYAN_DARK)
    s.text(992, 636, "Compra protegida", 12, DARK, 700)
    s.text(960, 662, "Garantía de 90 días y devolución si el producto no es compatible.", 10, MUTED)

    s.footer(800)
    return s


def screen_contact():
    s = Screen("09-contacto", "Contacto · TechFix", height=1240)
    s.header("contacto")
    s.breadcrumb(150, "Contacto")
    s.pill(152, 180, 210, 28, "HABLEMOS DE TU EQUIPO", CYAN_SOFT, CYAN_DARK, size=10)
    s.text(152, 262, "¿En qué podemos ayudarte?", 44, DARK, 700, ls=-1.2)
    s.para(152, 294, "Escribinos por una reparación, un repuesto o una consulta técnica. Respondemos en menos de 24 horas hábiles.", 600, 14, 22, MUTED)

    s.card(152, 360, 760, 610, 20, WHITE, BORDER, True)
    s.text(182, 402, "Formulario de contacto", 18, DARK, 700)
    s.input(182, 430, 330, "Nombre *", "Ej.: Lucía Fernández")
    s.input(532, 430, 350, "Correo *", "nombre@dominio.com")
    s.input(182, 520, 330, "Teléfono", "+54 11 5555-0101")
    s.text(532, 530, "Asunto *", 11, DARK, 700)
    s.rect(532, 540, 350, 44, WHITE, 10, BORDER, 1)
    s.text(546, 567, "Consulta por una reparación", 12, TEXT)
    s.text(182, 604, "Mensaje *", 11, DARK, 700)
    s.rect(182, 614, 700, 160, WHITE, 10, BORDER, 1)
    s.para(196, 640, "Hola, tengo un problema con la batería de mi notebook. Se descarga muy rápido y a veces no carga. ¿Pueden revisarla?", 660, 12, 19, TEXT)
    s.checkbox(182, 806, "Acepto la política de privacidad *")
    s.button(182, 840, 180, 46, "Enviar mensaje")
    s.text(182, 916, "Los campos con * son obligatorios. Nunca compartimos tus datos.", 9, MUTED)

    s.card(940, 360, 348, 170, 18, WHITE, BORDER, True)
    s.icon("pin", 966, 386, 22, CYAN_DARK)
    s.text(998, 404, "Visitanos", 12, DARK, 700)
    s.text(966, 434, "Av. Rivadavia 1234, CABA", 11, TEXT)
    s.text(966, 454, "Lun a Vie · 9 a 19 h · Sáb · 9 a 13 h", 10, MUTED)

    s.card(940, 550, 348, 170, 18, WHITE, BORDER, True)
    s.icon("phone-call", 966, 576, 22, CYAN_DARK)
    s.text(998, 594, "Llamanos", 12, DARK, 700)
    s.text(966, 624, "+54 11 5555-0101", 16, DARK, 700)
    s.text(966, 646, "WhatsApp: 549 11 5555-0101", 10, MUTED)

    s.card(940, 740, 348, 170, 18, DARK, None)
    s.icon("mail", 966, 766, 22, CYAN)
    s.text(998, 784, "Escribinos", 12, WHITE, 700)
    s.text(966, 814, "soporte@techfix.com.ar", 12, "#C9D4E3")
    s.text(966, 836, "Respondemos dentro de 24 h.", 10, "#8FA4BF")
    s.button(966, 862, 150, 30, "Abrir correo", "outline", 9)

    s.footer(1000)
    return s


def screen_about():
    s = Screen("10-sobre-el-taller", "Sobre el taller · TechFix", height=1420)
    s.header("taller")
    s.rect(0, 104, 1440, 330, DARK)
    s.pill(152, 164, 210, 28, "DESDE 2014 EN CABA", "#22304D", CYAN, size=10)
    s.text(152, 250, "Un taller de barrio,", 44, WHITE, 700, ls=-1.2)
    s.text(152, 308, "con estándares de laboratorio.", 44, WHITE, 700, ls=-1.2)
    s.para(152, 350, "Somos un equipo de técnicos obsesionados con reparar bien: diagnóstico honesto, repuestos de calidad y garantía escrita.", 620, 14, 22, "#AAB4C4")

    stats = [("10+", "Años de experiencia"), ("2.400+", "Equipos reparados"), ("4,9/5", "Valoración promedio"), ("90", "Días de garantía")]
    for i, (value, label) in enumerate(stats):
        x = 152 + i * 286
        s.text(x, 570, value, 34, DARK, 700)
        s.text(x, 598, label, 11, MUTED, 500)
        if i < 3:
            s.line(x + 250, 536, x + 250, 600, BORDER, 1)

    s.text(152, 680, "Lo que nos define", 28, DARK, 700)
    values = [("Diagnóstico honesto", "Si no conviene reparar, te lo decimos antes de cobrar.", "check"), ("Repuestos de calidad", "Trabajamos con proveedores homologados y stock verificado.", "shield"), ("Seguimiento online", "Cada reparación tiene un código y un estado actualizado.", "search"), ("Garantía escrita", "90 días sobre mano de obra y repuesto, sin letra chica.", "star")]
    for i, (title, desc, icon) in enumerate(values):
        x = 152 + (i % 2) * 580
        y = 720 + (i // 2) * 150
        s.card(x, y, 556, 128, 16, WHITE, BORDER)
        s.icon_box(x + 20, y + 22, 44, icon)
        s.text(x + 82, y + 40, title, 15, DARK, 700)
        s.para(x + 82, y + 62, desc, 440, 11, 17, MUTED, max_lines=2)

    s.rect(152, 1040, 1136, 100, CYAN_SOFT, 18)
    s.text(180, 1080, "¿Querés conocer el taller?", 20, DARK, 700)
    s.text(180, 1104, "Podés pasar por Av. Rivadavia 1234 o escribirnos antes de venir.", 11, MUTED)
    s.button(1050, 1064, 210, 44, "Coordinar una visita", "outline", 11)
    s.footer(1180)
    return s


def screen_login():
    s = Screen("11-login", "Ingresar · TechFix", height=900)
    s.rect(0, 0, 1440, 900, WHITE)
    s.rect(0, 0, 580, 900, DARK)
    s.rect(54, 54, 40, 40, DARK_2, 13)
    s.icon("wrench", 64, 64, 20, CYAN)
    s.text(104, 82, "Tech", 24, WHITE, 700)
    s.text(160, 82, "Fix", 24, CYAN, 700)
    s.text(54, 296, "Tu equipo vuelve", 37, WHITE, 700, ls=-1.2)
    s.text(54, 344, "a estar en buenas manos.", 37, WHITE, 700, ls=-1.2)
    s.para(54, 392, "Seguí tus reparaciones, revisá tus pedidos y consultá tus datos desde un solo lugar.", 450, 14, 22, "#AAB4C4")
    s.card(54, 520, 430, 150, 16, "#23233A", "#343450")
    s.icon("star", 78, 544, 18, "#F5B301")
    s.para(108, 552, "“Repararon la placa de mi notebook en dos días y me explicaron todo. Impecable.”", 350, 11, 17, "#C9D4E3", max_lines=3)
    s.text(78, 636, "Lucía F. · cliente desde 2023", 9, "#8FA4BF", 600)
    s.text(54, 820, "TechFix · Av. Rivadavia 1234, CABA", 10, "#7D8799")

    s.text(880, 230, "Hola de nuevo", 36, DARK, 700, "middle", -1)
    s.para(880, 258, "Ingresá para ver el estado de tus reparaciones y tus pedidos.", 420, 12, 19, MUTED)
    s.input(690, 340, 380, "Correo electrónico", "admin@techfix.com.ar")
    s.input(690, 430, 380, "Contraseña", "••••••••")
    s.checkbox(690, 520, "Recordarme")
    s.text(1070, 520, "¿Olvidaste tu contraseña?", 10, CYAN_DARK, 700, "end")
    s.button(690, 548, 380, 48, "Ingresar")
    s.line(690, 626, 1070, 626, BORDER, 1)
    s.text(880, 652, "¿Todavía no tenés cuenta?", 10, MUTED, 400, "middle")
    s.button(690, 672, 380, 44, "Crear una cuenta", "ghost")
    s.text(880, 760, "Al ingresar aceptás los términos y la política de privacidad.", 9, MUTED, 400, "middle")
    return s


def screen_register():
    s = Screen("12-registro", "Crear cuenta · TechFix", height=1000)
    s.rect(0, 0, 1440, 1000, WHITE)
    s.rect(0, 0, 520, 1000, DARK)
    s.rect(48, 48, 38, 38, DARK_2, 12)
    s.icon("wrench", 57, 57, 19, CYAN)
    s.text(96, 75, "Tech", 22, WHITE, 700)
    s.text(149, 75, "Fix", 22, CYAN, 700)
    s.text(48, 250, "Creá tu cuenta", 38, WHITE, 700, ls=-1)
    s.text(48, 296, "y seguí todo desde acá.", 38, WHITE, 700, ls=-1)
    s.para(48, 348, "Guardá tus reparaciones, tus pedidos y tus datos de contacto en un solo lugar.", 390, 13, 21, "#AAB4C4")
    for i, item in enumerate(["Seguimiento de reparaciones", "Historial de pedidos", "Datos de contacto siempre a mano"]):
        s.circle(60, 520 + i * 44, 11, "#22304D")
        s.icon("check", 53, 513 + i * 44, 14, CYAN)
        s.text(84, 525 + i * 44, item, 12, "#C9D4E3")
    s.text(48, 900, "TechFix · Taller de reparaciones", 10, "#7D8799")

    s.text(810, 150, "Crear cuenta", 32, DARK, 700, "middle", -0.8)
    s.para(810, 178, "Completá tus datos. Solo te va a llevar un minuto.", 480, 12, 19, MUTED)
    s.input(560, 260, 230, "Nombre *", "Lucía")
    s.input(810, 260, 230, "Apellido *", "Fernández")
    s.input(560, 350, 480, "Correo electrónico *", "nombre@dominio.com")
    s.input(560, 440, 480, "Teléfono", "+54 11 5555-0101")
    s.input(560, 530, 480, "Contraseña *", "Mínimo 8 caracteres")
    s.input(560, 620, 480, "Repetir contraseña *", "••••••••")
    s.checkbox(560, 710, "Acepto la política de privacidad y los términos de uso. *")
    s.button(560, 738, 480, 48, "Crear mi cuenta")
    s.line(560, 816, 1040, 816, BORDER, 1)
    s.text(800, 842, "¿Ya tenés cuenta?", 10, MUTED, 400, "middle")
    s.button(650, 858, 300, 40, "Ingresar", "ghost", 11)
    return s


def screen_404():
    s = Screen("22-404", "Página no encontrada · TechFix", height=940)
    s.header("inicio")
    s.text(720, 310, "404", 120, CYAN_SOFT, 700, "middle", 4)
    s.text(720, 390, "No encontramos esta página", 36, DARK, 700, "middle", -1)
    s.para(720, 422, "Puede que el enlace esté vencido o que la página se haya movido. Volvé al inicio y seguí navegando.", 540, 13, 21, MUTED)
    s.button(570, 510, 160, 46, "Volver al inicio", "primary", 11)
    s.button(746, 510, 160, 46, "Ver servicios", "ghost", 11)
    s.text(720, 610, "Si el problema continúa, escribinos a soporte@techfix.com.ar", 10, MUTED, 400, "middle")
    s.footer(700)
    return s


def screen_sitemap():
    s = Screen("23-mapa-sitio", "Mapa del sitio · TechFix", height=1140)
    s.header("inicio")
    s.text(152, 180, "Mapa del sitio", 42, DARK, 700, ls=-1.2)
    s.para(152, 208, "Arquitectura de navegación del prototipo. Cada bloque corresponde a una pantalla del sitio.", 620, 13, 21, MUTED)

    s.card(560, 280, 320, 64, 14, DARK, None)
    s.text(720, 320, "Inicio", 18, WHITE, 700, "middle")
    s.line(720, 344, 720, 390, "#B8C4D4", 2)

    groups = [
        (100, "Público", ["Servicios", "Tienda", "Seguimiento", "Sobre el taller", "Contacto"], CYAN),
        (470, "Cuenta", ["Ingresar", "Crear cuenta", "Mi panel", "Mis reparaciones", "Mis pedidos"], PURPLE),
        (840, "Administración", ["Panel admin", "Servicios", "Reparaciones", "Usuarios", "Stock"], ORANGE),
        (1210, "Módulos", ["MVC catálogo", "MVC detalle", "Framework", "API JSON", "404"], GREEN),
    ]
    for x, title, items, color in groups:
        s.rect(x - 90, 390, 180, 38, color, 10)
        s.text(x, 415, title, 12, WHITE, 700, "middle")
        s.line(x, 428, x, 450, "#B8C4D4", 2)
        for i, item in enumerate(items):
            y = 458 + i * 62
            s.rect(x - 88, y - 8, 176, 44, WHITE, 10, BORDER)
            s.text(x, y + 18, item, 10, DARK, 600, "middle")
            if i < len(items) - 1:
                s.line(x, y + 36, x, y + 54, "#B8C4D4", 2)

    s.rect(152, 810, 1136, 70, CYAN_SOFT, 16)
    s.text(176, 842, "Flujo principal", 12, DARK, 700)
    s.text(176, 862, "Inicio › Servicios › Detalle › Solicitud › Confirmación › Seguimiento", 11, CYAN_DARK)
    s.footer(900)
    return s
