#!/usr/bin/env python3
"""Pantallas públicas y de marketing del prototipo TechFix."""

from proto_lib import (
    Screen, DARK, DARK_2, CYAN, CYAN_DARK, CYAN_SOFT, BG, WHITE, TEXT, MUTED,
    BORDER, GREEN, GREEN_SOFT, ORANGE, ORANGE_SOFT, RED, RED_SOFT, PURPLE,
    PURPLE_SOFT, BLUE_SOFT,
)

SERVICES = [
    ("Cambio de pantalla", "CELULARES", "Reemplazamos la pantalla dañada por un repuesto de calidad, con prueba de táctil, brillo y calibración de colores antes de entregar.", 85000, "phone"),
    ("Reemplazo de batería", "CELULARES", "Cambiamos la batería agotada por una nueva y verificamos la carga, la autonomía y el estado del puerto de carga.", 45000, "battery"),
    ("Desbloqueo de celular", "CELULARES", "Liberación de patrones, cuentas y bloqueos de fábrica con verificación previa de titularidad del equipo.", 38000, "phone"),
    ("Reparación de placa", "CELULARES", "Microsoldadura y diagnóstico a nivel componente para equipos que no encienden, no cargan o se reinician solos.", 120000, "phone"),
    ("Cambio de SSD y migración", "COMPUTADORAS", "Instalamos un disco SSD nuevo, clonamos el sistema anterior y dejamos el equipo configurado y actualizado.", 60000, "laptop"),
    ("Limpieza y mantenimiento", "COMPUTADORAS", "Limpieza profunda de ventilación, cambio de pasta térmica y revisión de temperaturas para recuperar rendimiento.", 35000, "laptop"),
    ("Reparación de bisagras y carcasa", "COMPUTADORAS", "Reparamos bisagras flojas, carcasas partidas y marcos deformados para evitar daños mayores en la pantalla.", 50000, "laptop"),
    ("Reparación de notebook", "COMPUTADORAS", "Diagnóstico integral de notebook: encendido, teclado, pantalla, carga y rendimiento general.", 40000, "laptop"),
    ("Mantenimiento de consolas", "MANTENIMIENTO", "Limpieza, cambio de pasta térmica y revisión de lectores en consolas de última generación.", 42000, "grid"),
    ("Optimización y formateo", "MANTENIMIENTO", "Respaldo de datos, instalación limpia del sistema, controladores y programas esenciales para dejar la PC como nueva.", 30000, "screen"),
    ("Reparación de electrónica general", "ELECTRÓNICA", "Reparación de fuentes, placas de electrodomésticos y equipos de audio con instrumental de laboratorio.", 55000, "bolt"),
    ("Soldadura y microsoldadura", "ELECTRÓNICA", "Reparación de pistas, conectores y componentes SMD con estación de aire caliente y microscopio.", 70000, "bolt"),
    ("Recuperación de datos", "RECUPERACIÓN DE DATOS", "Rescate de archivos de discos, pendrives y memorias dañadas. Evaluación previa sin cargo y presupuesto cerrado.", 95000, "data"),
    ("Soporte técnico remoto", "SOPORTE TÉCNICO", "Asistencia a distancia para configuración, correo, redes y eliminación de virus, sin mover el equipo.", 18000, "check"),
]

PRODUCTS = [
    ("SSD NVMe 1 TB", "Almacenamiento", 112000, "ssd"),
    ("Memoria RAM 16 GB", "Componentes", 78000, "ram"),
    ("Batería MacBook Air", "Repuestos", 65000, "battery"),
    ("Cargador USB-C 65 W", "Accesorios", 32000, "bolt"),
    ("Teclado mecánico RGB", "Periféricos", 54000, "grid"),
    ("Mouse inalámbrico", "Periféricos", 28000, "box"),
    ("Auriculares Bluetooth", "Audio", 39900, "phone"),
    ("Pantalla Full HD 24\"", "Monitores", 145000, "screen"),
]


def money(value):
    return "$ " + f"{value:,.0f}".replace(",", ".")


def phone_illustration(s, x, y):
    s.circle(x + 170, y + 215, 235, "#DDF4F9", opacity=0.85)
    s.circle(x + 170, y + 215, 175, "#EAF9FC", opacity=0.9)
    s.add(f'<g transform="rotate(-7 {x + 160} {y + 215})">')
    s.rect(x + 40, y + 10, 240, 430, "#111827", 36, "#0B1220", 4)
    s.rect(x + 50, y + 20, 220, 410, "#16233A", 30)
    s.rect(x + 118, y + 26, 84, 11, "#0B1220", 5)
    s.text(x + 70, y + 58, "", 8, "#8FA4BF", 700)
    for i in range(3):
        s.rect(x + 214 + i * 6, y + 52, 4, 6, "#8FA4BF", 1)
    s.text(x + 92, y + 96, "TECHFIX · TF-260901", 7, "#8FA4BF", 600, ls=1)
    s.text(x + 92, y + 134, "Control de", 19, WHITE, 700)
    s.text(x + 92, y + 156, "calidad", 19, WHITE, 700)
    s.para(x + 92, y + 180, "Tu reparación avanza paso a paso. Podés seguirla cuando quieras.", 132, 9, 14, "#9FB0C3", max_lines=2)

    s.circle(x + 160, y + 292, 58, "none", "#2A3A55", 11)
    s.path(f"M {x + 160} {y + 234} A 58 58 0 1 1 {x + 103} {y + 282}", "none", CYAN, 11)
    s.text(x + 160, y + 300, "78%", 28, WHITE, 700, "middle")
    s.text(x + 160, y + 320, "COMPLETADO", 7, "#8FA4BF", 700, "middle", 1.5)

    s.rect(x + 93, y + 382, 134, 28, "#123249", 14)
    s.circle(x + 108, y + 396, 3.5, CYAN)
    s.text(x + 160, y + 400, "EN REPARACIÓN", 7, CYAN, 700, "middle", 1)
    s.add("</g>")

    # Tarjetas flotantes
    s.card(x - 140, y + 44, 190, 64, 14, WHITE, BORDER, True)
    s.circle(x - 110, y + 76, 16, GREEN_SOFT)
    s.icon("check", x - 118, y + 68, 16, GREEN)
    s.text(x - 82, y + 68, "Diagnóstico listo", 11, DARK, 700)
    s.text(x - 82, y + 84, "Sin cargo y sin sorpresas", 9, MUTED)

    s.card(x + 236, y + 230, 172, 64, 14, WHITE, BORDER, True)
    s.circle(x + 264, y + 262, 16, CYAN_SOFT)
    s.icon("shield", x + 256, y + 254, 16, CYAN_DARK)
    s.text(x + 290, y + 254, "90 días", 11, DARK, 700)
    s.text(x + 290, y + 270, "de garantía", 9, MUTED)

    s.card(x - 78, y + 452, 190, 64, 14, WHITE, BORDER, True)
    s.circle(x - 48, y + 484, 16, CYAN_SOFT)
    s.icon("star", x - 56, y + 476, 16, "#F5B301")
    s.text(x - 20, y + 476, "4,9 / 5", 11, DARK, 700)
    s.text(x - 20, y + 492, "+320 opiniones", 9, MUTED)


def screen_system():
    s = Screen("00-sistema-diseno", "Sistema de diseño", height=1000, group="Sistema")
    s.header("inicio")
    s.text(152, 168, "Sistema de diseño", 42, DARK, 700, ls=-1.2)
    s.para(152, 196, "Colores, tipografía, componentes y estados usados en todas las pantallas del prototipo TechFix.", 650, 14, 22, MUTED)

    s.label(152, 280, "Paleta", CYAN_DARK, 10)
    colors = [("#1A1A2E", "Tinta"), ("#00A8CC", "Cian"), ("#F4F7F9", "Fondo"), ("#FFFFFF", "Blanco"), ("#333333", "Texto")]
    for i, (hexv, name) in enumerate(colors):
        x = 152 + i * 150
        s.rect(x, 300, 126, 96, hexv, 14, BORDER if hexv in (WHITE, BG) else None)
        s.text(x, 418, name, 12, DARK, 700)
        s.text(x, 436, hexv, 10, MUTED)

    s.label(152, 500, "Tipografía", CYAN_DARK, 10)
    s.text(152, 558, "Título principal", 44, DARK, 700, ls=-1.2)
    s.text(152, 596, "Subtítulo de sección", 24, DARK, 700)
    s.text(152, 626, "Texto de párrafo con una jerarquía clara y legible.", 15, TEXT)
    s.text(152, 650, "ETIQUETA / METADATO", 10, CYAN_DARK, 700, ls=1.4)
    s.text(700, 542, "Inter · 700", 13, MUTED, 600)
    s.text(700, 566, "Títulos 48 / 42 px", 11, MUTED)
    s.text(700, 586, "Secciones 24 / 20 px", 11, MUTED)
    s.text(700, 606, "Cuerpo 14 / 15 px", 11, MUTED)
    s.text(700, 626, "Metadatos 10 / 11 px", 11, MUTED)

    s.label(900, 500, "Botones", CYAN_DARK, 10)
    s.button(900, 520, 160, 44, "Pedir presupuesto")
    s.button(1076, 520, 140, 44, "Ver servicios", "ghost")
    s.button(900, 580, 160, 44, "Crear cuenta", "outline")
    s.button(1076, 580, 140, 44, "Enviar", "dark")
    s.label(900, 660, "Estados", CYAN_DARK, 10)
    s.badge(900, 680, "Recibido", CYAN_DARK, CYAN_SOFT, 92)
    s.badge(1004, 680, "En reparación", PURPLE, PURPLE_SOFT, 122)
    s.badge(1138, 680, "Listo", GREEN, GREEN_SOFT, 72)
    s.badge(1222, 680, "Entregado", MUTED, "#EEF2F6", 96)

    s.label(152, 730, "Formularios", CYAN_DARK, 10)
    s.input(152, 750, 330, "Nombre completo", "Ej.: Lucía Fernández")
    s.input(506, 750, 330, "Correo electrónico", "nombre@dominio.com")
    s.rect(860, 760, 430, 44, WHITE, 10, CYAN, 2)
    s.text(874, 787, "Escribí tu consulta...", 12, "#98A2B3")
    s.rect(860, 838, 140, 40, CYAN, 20)
    s.text(930, 863, "Enviar consulta", 12, WHITE, 700, "middle")

    s.label(152, 880, "Tarjetas", CYAN_DARK, 10)
    s.card(152, 900, 330, 72, 14, WHITE, BORDER, True)
    s.icon_box(168, 916, 40)
    s.text(220, 930, "Diagnóstico honesto", 12, DARK, 700)
    s.text(220, 948, "Revisión previa sin cargo", 10, MUTED)
    s.card(506, 900, 330, 72, 14, WHITE, BORDER, True)
    s.icon_box(522, 916, 40, "shield")
    s.text(574, 930, "Garantía escrita", 12, DARK, 700)
    s.text(574, 948, "90 días en todas las reparaciones", 10, MUTED)
    return s


def screen_home():
    s = Screen("01-inicio", "Inicio · TechFix", height=1140)
    s.header("inicio")
    # Grilla suave del hero
    for x in range(0, 1441, 48):
        s.line(x, 104, x, 700, "#EAF0F4", 1)
    for y in range(104, 701, 48):
        s.line(0, y, 1440, y, "#EAF0F4", 1)
    s.rect(0, 104, 1440, 596, BG, opacity=0.72)

    s.pill(152, 162, 270, 30, "SERVICIO TÉCNICO ESPECIALIZADO", CYAN_SOFT, CYAN_DARK, size=10)
    s.text(152, 264, "Tu equipo en", 54, DARK, 700, ls=-2)
    s.text(152, 330, "manos", 54, DARK, 700, ls=-2)
    s.text(332, 330, "expertas.", 54, CYAN, 700, ls=-2)
    s.para(152, 386, "Diagnóstico honesto, reparación con repuestos de calidad y seguimiento online. Cuidamos tus celulares, computadoras y equipos de electrónica como si fueran nuestros.", 610, 16, 25, MUTED)
    s.button(152, 496, 232, 52, "Pedir presupuesto  ›")
    s.button(400, 496, 170, 52, "Ver servicios", "outline")
    s.icon("check", 152, 590, 16, CYAN_DARK)
    s.text(176, 603, "Garantía escrita", 12, TEXT, 500)
    s.icon("clock", 320, 590, 16, CYAN_DARK)
    s.text(344, 603, "Diagnóstico en 24 h", 12, TEXT, 500)
    s.icon("shield", 530, 590, 16, CYAN_DARK)
    s.text(554, 603, "Repuestos originales", 12, TEXT, 500)

    phone_illustration(s, 900, 170)

    s.rect(0, 700, 1440, 200, DARK)
    stats = [("2.400+", "Equipos reparados"), ("4,9/5", "Valoración de clientes"), ("90 días", "Garantía escrita"), ("24 h", "Diagnóstico promedio")]
    for i, (value, label) in enumerate(stats):
        x = 250 + i * 250
        s.text(x, 780, value, 34, WHITE, 700, "middle")
        s.text(x, 810, label, 12, "#AAB4C4", 500, "middle")
        if i < 3:
            s.line(x + 115, 744, x + 115, 824, "#343450", 1)
    s.footer(900)
    return s


def service_card(s, x, y, w, h, item, idx):
    name, cat, desc, price, icon = item
    s.card(x, y, w, h, 16, WHITE, BORDER, True)
    s.rect(x + 18, y + 18, 42, 42, CYAN_SOFT, 12)
    s.icon(icon, x + 27, y + 27, 24, CYAN_DARK)
    s.pill(x + w - 118, y + 18, 100, 21, cat, "#F4F7F9", MUTED, size=8)
    s.text(x + 18, y + 86, name, 16, DARK, 700)
    s.para(x + 18, y + 106, desc, w - 36, 11, 16, MUTED, max_lines=2)
    s.text(x + 18, y + h - 24, money(price), 17, DARK, 700)
    s.text(x + 18, y + h - 9, "PRECIO DESDE", 7.5, MUTED, 700, ls=0.8)
    s.text(x + w - 20, y + h - 20, "Ver detalle  ›", 10, CYAN_DARK, 700, "end")


def screen_services():
    h = 1380
    s = Screen("02-servicios", "Servicios · TechFix", height=1800)
    s.header("servicios")
    s.breadcrumb(150, "Servicios")
    s.pill(152, 180, 190, 28, "TALLER ESPECIALIZADO", CYAN_SOFT, CYAN_DARK, size=10)
    s.text(152, 262, "Todos nuestros servicios", 48, DARK, 700, ls=-1.5)
    s.para(152, 292, "Elegí la categoría que necesitás. Todos los trabajos incluyen diagnóstico previo y presupuesto sin cargo.", 600, 14, 22, MUTED)

    filters = ["Todos · 14", "Celulares · 4", "Computadoras · 4", "Mantenimiento · 2", "Electrónica · 2", "Recuperación de datos · 1", "Soporte · 1"]
    x = 152
    for i, label in enumerate(filters):
        w = len(label) * 6.4 + 34
        if i == 0:
            s.pill(x, 340, w, 34, label, DARK, WHITE, size=11)
        else:
            s.pill(x, 340, w, 34, label, WHITE, TEXT, BORDER, size=11)
        x += w + 12

    for idx, item in enumerate(SERVICES):
        col, row = idx % 3, idx // 3
        service_card(s, 152 + col * 374, 400 + row * 198, 352, 180, item, idx)

    s.rect(152, 1420, 1136, 100, DARK, 22)
    s.text(190, 1467, "¿No sabés qué servicio necesitás?", 24, WHITE, 700)
    s.text(190, 1492, "Describinos la falla y te orientamos. El diagnóstico inicial no tiene costo.", 12, "#AAB4C4")
    s.button(1080, 1446, 170, 46, "Contar mi problema", "outline")
    s.footer(1560)
    return s


def screen_service_detail():
    s = Screen("03-servicio-detalle", "Cambio de pantalla · TechFix", height=1240)
    s.header("servicios")
    s.breadcrumb(150, "Cambio de pantalla")
    s.text(152, 232, "Cambio de pantalla", 42, DARK, 700, ls=-1.2)
    s.pill(152, 252, 116, 26, "CELULARES", CYAN_SOFT, CYAN_DARK, size=9)
    s.text(282, 270, "Duración estimada: 60 a 90 minutos", 11, MUTED)

    s.card(152, 310, 720, 330, 18, WHITE, BORDER, True)
    s.text(182, 350, "Qué incluye", 20, DARK, 700)
    items = [
        "Diagnóstico previo sin cargo y presupuesto cerrado.",
        "Pantalla de repuesto con calibración de color y brillo.",
        "Prueba táctil, sensores, cámara frontal y altavoz.",
        "Limpieza interna y revisión de la batería.",
        "Garantía escrita de 90 días sobre el repuesto.",
    ]
    for i, item in enumerate(items):
        s.circle(198, 388 + i * 40, 10, CYAN_SOFT)
        s.icon("check", 191, 381 + i * 40, 14, CYAN_DARK)
        s.text(220, 393 + i * 40, item, 13, TEXT)
    s.text(182, 610, "El precio final puede variar según el modelo y el estado del equipo. Siempre te avisamos antes de intervenir.", 10, MUTED)

    s.card(906, 310, 382, 330, 18, WHITE, BORDER, True)
    s.text(936, 350, "Desde", 11, MUTED, 700)
    s.text(936, 390, "$ 85.000", 34, DARK, 700)
    s.text(936, 416, "Mano de obra y repuesto incluidos", 10, MUTED)
    s.button(936, 444, 322, 46, "Pedir este servicio")
    s.button(936, 502, 322, 42, "Consultar por WhatsApp", "ghost")
    s.text(936, 570, "Stock sujeto a modelo. Consultá disponibilidad.", 9, MUTED)

    s.text(152, 700, "Cómo trabajamos", 24, DARK, 700)
    steps = [("01", "Recibimos tu equipo", "Registramos la falla y el estado estético."), ("02", "Diagnosticamos", "Revisión técnica y presupuesto sin cargo."), ("03", "Reparamos", "Repuesto de calidad y pruebas de control."), ("04", "Entregamos", "Garantía escrita y seguimiento online.")]
    for i, (num, title, desc) in enumerate(steps):
        x = 152 + i * 286
        s.card(x, 730, 260, 130, 14, WHITE, BORDER)
        s.text(x + 18, 764, num, 11, CYAN_DARK, 700, ls=1)
        s.text(x + 18, 790, title, 13, DARK, 700)
        s.para(x + 18, 810, desc, 220, 10, 14, MUTED, max_lines=2)
        if i < 3:
            s.line(x + 260, 795, x + 286, 795, BORDER, 1, "4 3")

    s.rect(152, 900, 1136, 76, CYAN_SOFT, 18)
    s.text(180, 936, "¿Tenés otra falla?", 18, DARK, 700)
    s.text(180, 958, "Contanos qué pasa y te orientamos sin cargo.", 11, MUTED)
    s.button(1060, 916, 200, 42, "Ver todos los servicios", "outline", 11)
    s.footer(1000)
    return s


def screen_request():
    s = Screen("04-solicitud", "Solicitud de reparación · TechFix", height=1360)
    s.header("servicios")
    s.breadcrumb(150, "Solicitud de reparación")
    s.pill(152, 180, 220, 28, "PRESUPUESTO SIN CARGO", CYAN_SOFT, CYAN_DARK, size=10)
    s.text(152, 260, "Pedí tu presupuesto", 44, DARK, 700, ls=-1.2)
    s.para(152, 292, "Completá los datos y te contactamos dentro de las próximas 24 horas hábiles.", 580, 14, 22, MUTED)

    s.card(152, 350, 830, 700, 20, WHITE, BORDER, True)
    s.text(182, 392, "Datos de contacto", 18, DARK, 700)
    s.input(182, 418, 370, "Nombre y apellido *", "Ej.: Lucía Fernández")
    s.input(582, 418, 370, "Correo electrónico *", "nombre@dominio.com")
    s.input(182, 508, 370, "Teléfono *", "+54 11 5555-0101")
    s.text(182, 598, "Servicio *", 11, DARK, 700)
    s.rect(182, 608, 770, 44, WHITE, 10, BORDER, 1)
    s.text(196, 635, "Cambio de pantalla", 12, TEXT)
    s.chevron(918, 630, MUTED, 10)
    s.input(182, 690, 370, "Equipo · Marca *", "Samsung")
    s.input(582, 690, 370, "Modelo *", "Galaxy A54")
    s.text(182, 780, "Contanos la falla *", 11, DARK, 700)
    s.rect(182, 790, 770, 120, WHITE, 10, BORDER, 1)
    s.para(196, 816, "La pantalla se rompió después de una caída. El táctil funciona, pero se ve una mancha negra en la esquina superior.", 720, 12, 19, TEXT)
    s.checkbox(182, 940, "Acepto la política de privacidad y el tratamiento de mis datos. *")
    s.button(182, 982, 210, 46, "Enviar solicitud")
    s.text(410, 1010, "Te respondemos en menos de 24 h hábiles.", 10, MUTED)

    s.card(1022, 350, 266, 360, 18, DARK, None)
    s.text(1050, 390, "Diagnóstico sin cargo", 18, WHITE, 700)
    s.para(1050, 416, "Revisamos tu equipo y te pasamos un presupuesto claro antes de reparar.", 210, 11, 17, "#AAB4C4")
    s.line(1050, 486, 1260, 486, "#343450", 1)
    for i, (label, value) in enumerate([("Garantía", "90 días"), ("Diagnóstico", "24 h"), ("Respuesta", "< 24 h")]):
        s.text(1050, 520 + i * 46, label, 10, "#8FA4BF", 700)
        s.text(1260, 520 + i * 46, value, 12, WHITE, 700, "end")
    s.text(1050, 668, "También podés escribirnos a soporte@techfix.com.ar", 9, "#AAB4C4")

    s.card(1022, 736, 266, 190, 18, WHITE, BORDER, True)
    s.icon("phone-call", 1048, 762, 24, CYAN_DARK)
    s.text(1080, 782, "¿Preferís llamar?", 14, DARK, 700)
    s.text(1048, 814, "+54 11 5555-0101", 18, DARK, 700)
    s.text(1048, 838, "Lun a Vie · 9 a 19 h", 10, MUTED)
    s.button(1048, 862, 214, 38, "Escribir por WhatsApp", "outline", 10)

    s.footer(1120)
    return s


def screen_confirm():
    s = Screen("05-confirmacion", "Solicitud confirmada · TechFix", height=1140)
    s.header("servicios")
    s.card(300, 190, 840, 540, 24, WHITE, BORDER, True)
    s.circle(720, 270, 44, GREEN_SOFT)
    s.icon("check", 700, 250, 40, GREEN)
    s.text(720, 350, "¡Solicitud recibida!", 34, DARK, 700, "middle")
    s.para(720, 380, "Ya registramos tu pedido. Nuestro equipo va a revisar la información y te va a contactar dentro de las próximas 24 horas hábiles.", 580, 13, 21, MUTED)
    s.text(720, 470, "TU CÓDIGO DE SEGUIMIENTO", 10, MUTED, 700, "middle", 1.4)
    s.rect(500, 486, 440, 62, CYAN_SOFT, 12)
    s.text(720, 526, "TF-260901-A1B2C3", 24, CYAN_DARK, 700, "middle", 2)
    s.button(520, 584, 190, 44, "Seguir mi reparación", "primary", 11)
    s.button(730, 584, 190, 44, "Volver al inicio", "ghost", 11)
    s.text(720, 660, "Guardá este código: lo vas a necesitar para consultar el estado.", 10, MUTED, 400, "middle")
    s.rect(152, 790, 1136, 76, WHITE, 18, BORDER)
    s.icon("mail", 180, 812, 22, CYAN_DARK)
    s.text(212, 824, "También te enviamos el resumen por correo.", 13, DARK, 700)
    s.text(212, 846, "Revisá tu bandeja de entrada y la carpeta de spam.", 11, MUTED)
    s.footer(900)
    return s
