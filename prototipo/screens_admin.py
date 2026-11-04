#!/usr/bin/env python3
"""Pantallas de cuenta, administración, módulos MVC/framework y móviles."""

from proto_lib import (
    Screen, DARK, DARK_2, CYAN, CYAN_DARK, CYAN_SOFT, BG, WHITE, TEXT, MUTED,
    BORDER, GREEN, GREEN_SOFT, ORANGE, ORANGE_SOFT, RED, RED_SOFT, PURPLE,
    PURPLE_SOFT, BLUE_SOFT,
)
from screens_public import PRODUCTS, money
from screens_shop import product_image


def client_sidebar(s, x, y, active="Resumen"):
    s.card(x, y, 250, 430, 18, WHITE, BORDER, True)
    s.avatar(x + 22, y + 22, 42, "L")
    s.text(x + 76, y + 42, "Lucía Fernández", 12, DARK, 700)
    s.text(x + 76, y + 58, "cliente@demo.com", 9, MUTED)
    s.line(x + 18, y + 82, x + 232, y + 82, BORDER, 1)
    items = [("Resumen", "grid"), ("Mis reparaciones", "wrench"), ("Mis pedidos", "cart"), ("Seguimiento", "search"), ("Cerrar sesión", "logout")]
    for i, (label, icon) in enumerate(items):
        yy = y + 102 + i * 54
        if label == active:
            s.rect(x + 14, yy - 18, 222, 40, CYAN_SOFT, 9)
            s.text(x + 52, yy + 7, label, 11, CYAN_DARK, 700)
            s.icon(icon, x + 26, yy - 8, 16, CYAN_DARK)
        else:
            s.text(x + 52, yy + 7, label, 11, TEXT, 500)
            s.icon(icon, x + 26, yy - 8, 16, MUTED)


def stats_row(s, x, y, items):
    for i, (label, value, note, icon) in enumerate(items):
        s.stat_card(x + i * 216, y, 208, 104, label, value, note, icon)


def screen_dashboard():
    h = 1120
    s = Screen("13-dashboard-cliente", "Mi panel · TechFix", height=1290)
    s.header("inicio", logged=True)
    s.rect(0, 104, 1440, 230, BG)
    s.breadcrumb(150, "Mi panel", "Inicio")
    s.pill(152, 178, 150, 28, "ÁREA PERSONAL", CYAN_SOFT, CYAN_DARK, size=10)
    s.text(152, 252, "Hola, Lucía", 44, DARK, 700, ls=-1.2)
    s.para(152, 284, "Desde acá podés seguir tus reparaciones, revisar tus pedidos y consultar tus datos de contacto.", 650, 13, 20, MUTED)

    client_sidebar(s, 152, 360)
    stats_row(s, 430, 360, [
        ("REPARACIONES", "2", "2 activas", "wrench"),
        ("LISTAS", "1", "para retirar", "check"),
        ("PEDIDOS", "1", "en la tienda", "cart"),
        ("COMPRAS", "$ 72.700", "total histórico", "box"),
    ])

    s.card(430, 492, 858, 286, 18, WHITE, BORDER, True)
    s.text(458, 530, "Mis reparaciones", 18, DARK, 700)
    s.text(458, 550, "Estado actualizado de cada equipo que pasó por el taller.", 10, MUTED)
    s.button(1100, 512, 160, 38, "Nueva solicitud", "primary", 10)
    s.rect(458, 572, 802, 34, "#F4F7F9", 8)
    for i, label in enumerate(["CÓDIGO", "EQUIPO", "SERVICIO", "ESTADO", "INGRESO"]):
        s.text(474 + [0, 128, 300, 500, 650][i], 594, label, 8, MUTED, 700, ls=0.8)
    rows = [("TF-260903-G7H8I9", "Kingston Pendrive 64 GB", "Recuperación de datos", "Listo", 640), ("TF-260901-A1B2C3", "Samsung Galaxy A54", "Cambio de pantalla", "En reparación", 700)]
    for i, (code, eq, service, state, y) in enumerate(rows):
        s.line(458, y - 26, 1260, y - 26, BORDER, 1)
        s.text(474, y, code, 9, DARK, 700)
        s.text(602, y, eq, 9, TEXT)
        s.text(774, y, service, 9, TEXT)
        s.badge(974, y - 16, state, GREEN if state == "Listo" else PURPLE, GREEN_SOFT if state == "Listo" else PURPLE_SOFT, 88 if state == "Listo" else 108)
        s.text(1108, y, "03/09/2026" if i == 0 else "01/09/2026", 9, MUTED)
        s.text(1226, y, "Ver", 9, CYAN_DARK, 700)

    s.card(430, 798, 858, 210, 18, WHITE, BORDER, True)
    s.text(458, 836, "Mis pedidos", 18, DARK, 700)
    s.text(458, 856, "Compras de repuestos y accesorios realizadas en la tienda.", 10, MUTED)
    s.button(1100, 820, 160, 36, "Ir a la tienda", "ghost", 10)
    s.rect(458, 880, 802, 32, "#F4F7F9", 8)
    for i, label in enumerate(["PEDIDO", "FECHA", "PRODUCTOS", "TOTAL", "ESTADO"]):
        s.text(474 + [0, 180, 370, 540, 680][i], 901, label, 8, MUTED, 700, ls=0.8)
    s.text(474, 946, "PED-260901-X1Y2Z3", 9, DARK, 700)
    s.text(654, 946, "01/09/2026", 9, TEXT)
    s.text(844, 946, "2", 9, TEXT)
    s.text(1014, 946, "$ 72.700", 9, DARK, 700)
    s.badge(1154, 930, "Pagado", CYAN_DARK, CYAN_SOFT, 80)

    s.footer(1050)
    return s


def admin_shell(s, active, title, subtitle):
    s.header("inicio", logged=True, admin=True)
    s.rect(0, 104, 1440, 210, BG)
    s.breadcrumb(150, "Panel", "Inicio")
    s.pill(152, 174, 160, 26, "ROL ADMINISTRADOR", CYAN_SOFT, CYAN_DARK, size=9)
    s.text(152, 246, title, 40, DARK, 700, ls=-1.2)
    s.para(152, 276, subtitle, 700, 12, 19, MUTED)

    s.card(152, 334, 238, 500, 18, DARK, None)
    s.label(178, 370, "ADMINISTRACIÓN", "#8FA4BF", 9)
    items = [("Resumen", "grid"), ("Servicios", "wrench"), ("Reparaciones", "box"), ("Usuarios", "user"), ("Ver tienda", "cart"), ("Ir al sitio", "logout")]
    for i, (label, icon) in enumerate(items):
        yy = 406 + i * 54
        if label == active:
            s.rect(166, yy - 18, 210, 38, "#123249", 8)
            s.text(204, yy + 6, label, 11, CYAN, 700)
            s.icon(icon, 178, yy - 8, 16, CYAN)
        else:
            s.text(204, yy + 6, label, 11, "#C9D4E3", 500)
            s.icon(icon, 178, yy - 8, 16, "#8FA4BF")
    s.line(178, 760, 364, 760, "#343450", 1)
    s.text(178, 792, "Sesión de Marcos", 9, "#8FA4BF")
    s.text(178, 808, "admin@techfix.com.ar", 8, "#6F7C90")
    return 430, 334


def screen_admin_panel():
    s = Screen("14-admin-panel", "Panel de administración · TechFix", height=1290)
    cx, cy = admin_shell(s, "Resumen", "Panel de administración", "Resumen del taller, el catálogo y los pedidos. Desde acá podés gestionar los servicios y el estado de las reparaciones.")
    stats_row(s, cx, cy, [
        ("REPARACIONES ACTIVAS", "4", "en el taller", "wrench"),
        ("VENTAS", "$ 184.700", "pedidos no cancelados", "cart"),
        ("USUARIOS", "3", "cuentas activas", "user"),
        ("CONSULTAS", "1", "sin leer", "mail"),
    ])
    s.card(cx, 462, 858, 300, 18, WHITE, BORDER, True)
    s.text(cx + 28, 500, "Últimas reparaciones", 18, DARK, 700)
    s.text(cx + 28, 520, "Seguimiento rápido de los ingresos más recientes.", 10, MUTED)
    s.button(cx + 700, 482, 140, 38, "Gestionar", "primary", 10)
    s.rect(cx + 28, 544, 802, 32, "#F4F7F9", 8)
    heads = ["CÓDIGO", "CLIENTE", "EQUIPO", "SERVICIO", "ESTADO", "INGRESO"]
    xs = [0, 120, 250, 410, 610, 720]
    for i, label in enumerate(heads):
        s.text(cx + 44 + xs[i], 565, label, 8, MUTED, 700, ls=0.8)
    rows = [
        ("TF-260904-J1K2L3", "María Gómez", "Motorola Moto G84", "Reemplazo de batería", "Recibido", CYAN_DARK, CYAN_SOFT, "04/09/2026"),
        ("TF-260903-G7H8I9", "Lucía Fernández", "Kingston Pendrive 64 GB", "Recuperación de datos", "Listo", GREEN, GREEN_SOFT, "03/09/2026"),
        ("TF-260902-D4E5F6", "Julián Pérez", "Lenovo IdeaPad 3", "Cambio de SSD", "En reparación", PURPLE, PURPLE_SOFT, "02/09/2026"),
        ("TF-260901-A1B2C3", "Lucía Fernández", "Samsung Galaxy A54", "Cambio de pantalla", "Control de calidad", CYAN_DARK, CYAN_SOFT, "01/09/2026"),
    ]
    for i, (code, client, eq, service, state, color, fill, date) in enumerate(rows):
        y = 610 + i * 38
        s.line(cx + 28, y - 18, cx + 830, y - 18, BORDER, 1)
        s.text(cx + 44, y, code, 8, DARK, 700)
        s.text(cx + 164, y, client, 8, TEXT)
        s.text(cx + 294, y, eq, 8, TEXT)
        s.text(cx + 454, y, service, 8, TEXT)
        s.badge(cx + 630, y - 14, state, color, fill, len(state) * 5.8 + 20)
        s.text(cx + 764, y, date, 8, MUTED)

    s.card(cx, 782, 418, 200, 18, WHITE, BORDER, True)
    s.text(cx + 26, 818, "Stock bajo", 16, DARK, 700)
    s.text(cx + 26, 838, "Productos con 5 unidades o menos.", 10, MUTED)
    s.button(cx + 300, 808, 100, 30, "Ver tienda", "ghost", 9)
    for i, (name, qty) in enumerate([("Auriculares Bluetooth", 3), ("Batería para MacBook Air", 4), ("Gabinete ATX con vidrio templado", 5)]):
        y = 872 + i * 34
        s.text(cx + 26, y, name, 10, TEXT)
        s.pill(cx + 330, y - 13, 62, 22, f"{qty} u.", ORANGE_SOFT, ORANGE, size=8)

    s.card(cx + 440, 782, 418, 200, 18, WHITE, BORDER, True)
    s.text(cx + 466, 818, "Catálogo", 16, DARK, 700)
    s.text(cx + 466, 838, "Servicios disponibles en el sitio.", 10, MUTED)
    s.button(cx + 730, 808, 104, 30, "Nuevo servicio", "primary", 9)
    s.text(cx + 466, 870, "Servicios activos", 10, MUTED)
    s.text(cx + 820, 870, "14", 11, DARK, 700, "end")
    s.text(cx + 466, 894, "Productos activos", 10, MUTED)
    s.text(cx + 820, 894, "12", 11, DARK, 700, "end")
    s.rect(cx + 466, 914, 368, 50, CYAN_SOFT, 10)
    s.text(cx + 480, 936, "El CRUD usa PDO con consultas preparadas.", 9, CYAN_DARK)

    s.footer(1050)
    return s


def admin_table_screen(slug, title, subtitle, active, headers, rows, widths, badge_col=None):
    n = len(rows)
    card_h = 200 + n * 54
    s = Screen(slug, f"{title} · TechFix", height=334 + card_h + 280)
    cx, cy = admin_shell(s, active, title, subtitle)
    s.card(cx, cy, 858, card_h, 18, WHITE, BORDER, True)
    s.button(cx + 674, cy + 24, 158, 40, "Nuevo registro", "primary", 10)
    s.rect(cx + 26, cy + 74, 806, 34, "#F4F7F9", 8)
    s.icon("search", cx + 46, cy + 85, 13, "#98A2B3")
    s.text(cx + 68, cy + 98, "Buscar en la tabla...", 9, "#98A2B3")
    s.text(cx + 820, cy + 98, "Filtrar", 9, MUTED, 700, "end")
    for i, head in enumerate(headers):
        s.text(cx + 42 + widths[i], cy + 132, head, 8, MUTED, 700, ls=0.8)
    for idx, row in enumerate(rows):
        y = cy + 178 + idx * 54
        s.line(cx + 26, y - 24, cx + 832, y - 24, BORDER, 1)
        for i, value in enumerate(row):
            if badge_col is not None and i == badge_col:
                if value in ("Activo", "Listo", "Pagado"):
                    color, fill = GREEN, GREEN_SOFT
                elif value in ("Inactivo", "Entregado"):
                    color, fill = MUTED, "#EEF2F6"
                elif "reparación" in value or "calidad" in value:
                    color, fill = PURPLE, PURPLE_SOFT
                else:
                    color, fill = CYAN_DARK, CYAN_SOFT
                s.badge(cx + 42 + widths[i], y - 15, value, color, fill, len(value) * 5.8 + 20)
            else:
                s.text(cx + 42 + widths[i], y, value, 9, DARK if i == 0 else TEXT, 700 if i == 0 else 400)
    pag_y = cy + 160 + n * 54
    s.line(cx + 26, pag_y - 24, cx + 832, pag_y - 24, BORDER, 1)
    s.text(cx + 42, pag_y, f"Mostrando {n} registros de {n}", 9, MUTED)
    s.rect(cx + 660, pag_y - 16, 28, 26, "#F4F7F9", 7)
    s.text(cx + 674, pag_y + 2, "‹", 11, MUTED, 700, "middle")
    s.rect(cx + 694, pag_y - 16, 28, 26, CYAN_SOFT, 7)
    s.text(cx + 708, pag_y + 2, "1", 9, CYAN_DARK, 700, "middle")
    s.rect(cx + 728, pag_y - 16, 28, 26, "#F4F7F9", 7)
    s.text(cx + 742, pag_y + 2, "›", 11, MUTED, 700, "middle")
    s.button(cx + 766, pag_y - 16, 66, 26, "Exportar", "ghost", 8)
    s.footer(cy + card_h + 40)
    return s


def screen_admin_services():
    rows = [
        ("Cambio de pantalla", "Celulares", "$ 85.000", "90 min", "Activo"),
        ("Reemplazo de batería", "Celulares", "$ 45.000", "45 min", "Activo"),
        ("Cambio de SSD y migración", "Computadoras", "$ 60.000", "120 min", "Activo"),
        ("Limpieza y mantenimiento", "Computadoras", "$ 35.000", "90 min", "Activo"),
        ("Recuperación de datos", "Datos", "$ 95.000", "48 h", "Activo"),
        ("Soporte técnico remoto", "Soporte", "$ 18.000", "30 min", "Inactivo"),
    ]
    return admin_table_screen("15-admin-servicios", "Gestión de servicios", "Creá, editá y dá de baja los servicios del catálogo. Los cambios se reflejan en el sitio público.", "Servicios", ["SERVICIO", "CATEGORÍA", "PRECIO", "DURACIÓN", "ESTADO"], rows, [0, 250, 420, 560, 680], badge_col=4)


def screen_admin_repairs():
    rows = [
        ("TF-260904-J1K2L3", "María Gómez", "Motorola Moto G84", "Reemplazo de batería", "Recibido", "$ —"),
        ("TF-260903-G7H8I9", "Lucía Fernández", "Kingston Pendrive 64 GB", "Recuperación de datos", "Listo", "$ 95.000"),
        ("TF-260902-D4E5F6", "Julián Pérez", "Lenovo IdeaPad 3", "Cambio de SSD", "En reparación", "$ 112.000"),
        ("TF-260901-A1B2C3", "Lucía Fernández", "Samsung Galaxy A54", "Cambio de pantalla", "Control de calidad", "$ 85.000"),
        ("TF-260831-M4N5O6", "Carlos Ruiz", "iPhone 13", "Cambio de batería", "Entregado", "$ 78.000"),
    ]
    return admin_table_screen("16-admin-reparaciones", "Gestión de reparaciones", "Actualizá el estado, las observaciones técnicas y el costo final. El cliente ve los cambios en el seguimiento público.", "Reparaciones", ["CÓDIGO", "CLIENTE", "EQUIPO", "SERVICIO", "ESTADO", "COSTO"], rows, [0, 118, 226, 390, 560, 690], badge_col=4)


def screen_admin_users():
    rows = [
        ("Lucía Fernández", "cliente@demo.com", "Cliente", "Activo", "03/09/2026"),
        ("Marcos Núñez", "admin@techfix.com.ar", "Administrador", "Activo", "01/09/2026"),
        ("Julián Pérez", "julian@demo.com", "Cliente", "Activo", "28/08/2026"),
        ("María Gómez", "maria@demo.com", "Cliente", "Inactivo", "15/08/2026"),
    ]
    return admin_table_screen("17-admin-usuarios", "Usuarios y roles", "Administrá las cuentas del sitio. Solo el rol administrador puede acceder a este panel.", "Usuarios", ["NOMBRE", "CORREO", "ROL", "ESTADO", "ALTA"], rows, [0, 190, 390, 530, 660], badge_col=3)


def screen_mvc_catalog():
    s = Screen("18-mvc-catalogo", "Catálogo MVC · TechFix", height=1120 or 1120)
    s.rect(0, 0, 1440, 64, WHITE)
    s.line(0, 63, 1440, 63, BORDER, 1)
    s.rect(152, 14, 36, 36, DARK, 11)
    s.icon("wrench", 161, 23, 18, CYAN)
    s.text(198, 41, "Tech", 21, DARK, 700)
    s.text(246, 41, "Fix", 21, CYAN, 700)
    s.pill(300, 18, 86, 28, "MVC", CYAN_SOFT, CYAN_DARK, size=9)
    s.text(640, 40, "Catálogo", 12, CYAN_DARK, 700)
    s.text(720, 40, "Detalle", 12, MUTED, 500)
    s.text(800, 40, "API JSON", 12, MUTED, 500)
    s.text(1240, 40, "Volver al sitio  ›", 11, CYAN_DARK, 700, "end")
    s.text(152, 132, "Catálogo MVC", 38, DARK, 700, ls=-1)
    s.para(152, 160, "Vista generada por TiendaController usando el modelo Producto y PDO. La búsqueda se resuelve por query string.", 620, 12, 20, MUTED)
    s.card(152, 214, 520, 46, 10, WHITE, BORDER)
    s.icon("search", 170, 226, 17, MUTED)
    s.text(198, 242, "Buscar producto...", 11, "#98A2B3")
    s.button(690, 214, 100, 46, "Buscar", "primary", 10)
    s.text(1258, 242, "8 productos", 10, MUTED, 600, "end")

    for idx, (name, cat, price, icon) in enumerate(PRODUCTS):
        col, row = idx % 4, idx // 4
        x, y = 152 + col * 288, 300 + row * 330
        s.card(x, y, 272, 306, 16, WHITE, BORDER, True)
        product_image(s, x + 14, y + 14, 244, 140, icon)
        s.pill(x + 18, y + 170, len(cat) * 6.6 + 22, 20, cat.upper(), "#F4F7F9", MUTED, size=8)
        s.text(x + 18, y + 216, name, 14, DARK, 700)
        s.text(x + 18, y + 248, money(price), 17, DARK, 700)
        s.text(x + 18, y + 268, "Ver detalle  ›", 10, CYAN_DARK, 700)
    s.rect(0, 1020, 1440, 100, DARK)
    s.text(720, 1070, "Módulo MVC nativo · app/Models · app/Views · app/Controllers", 12, "#C9D4E3", 500, "middle")
    s.text(720, 1092, "mvc/index.php?accion=index", 10, CYAN, 600, "middle")
    return s


def screen_mvc_detail():
    s = Screen("19-mvc-detalle", "Detalle MVC · TechFix", height=900)
    s.rect(0, 0, 1440, 64, WHITE)
    s.line(0, 63, 1440, 63, BORDER, 1)
    s.rect(152, 14, 36, 36, DARK, 11)
    s.icon("wrench", 161, 23, 18, CYAN)
    s.text(198, 41, "Tech", 21, DARK, 700)
    s.text(246, 41, "Fix", 21, CYAN, 700)
    s.pill(300, 18, 86, 28, "MVC", CYAN_SOFT, CYAN_DARK, size=9)
    s.text(1240, 40, "Volver al catálogo  ›", 11, CYAN_DARK, 700, "end")
    s.text(152, 130, "Catálogo  ›  Detalle", 11, MUTED)
    s.text(152, 190, "SSD NVMe 1 TB", 42, DARK, 700, ls=-1.2)
    s.pill(152, 210, 130, 26, "ALMACENAMIENTO", CYAN_SOFT, CYAN_DARK, size=9)
    s.rect(152, 270, 660, 420, "#F7FAFC", 20, BORDER)
    product_image(s, 202, 310, 560, 340, "ssd", scale=2.4)
    s.card(860, 270, 428, 420, 20, WHITE, BORDER, True)
    s.text(890, 314, "Precio", 10, MUTED, 700)
    s.text(890, 356, "$ 112.000", 34, DARK, 700)
    s.text(890, 382, "Stock: 10 unidades", 10, GREEN, 700)
    s.para(890, 416, "Unidad de estado sólido M.2 NVMe de 1 TB, lectura de hasta 3.500 MB/s. Ideal para actualizar notebooks y PCs.", 368, 11, 18, MUTED, max_lines=3)
    s.button(890, 500, 368, 46, "Agregar al carrito")
    s.button(890, 558, 368, 40, "Consultar compatibilidad", "ghost")
    s.text(890, 630, "Garantía de 90 días · Retiro en el taller", 9, MUTED)
    s.text(152, 740, "Productos relacionados", 22, DARK, 700)
    for i, (name, cat, price, icon) in enumerate(PRODUCTS[1:4]):
        x = 152 + i * 288
        s.card(x, 770, 264, 92, 14, WHITE, BORDER)
        product_image(s, x + 10, 780, 70, 72, icon, scale=0.52)
        s.text(x + 94, 810, name, 10, DARK, 700)
        s.text(x + 94, 830, money(price), 11, CYAN_DARK, 700)
    s.rect(0, 880, 1440, 20, DARK)
    return s


def screen_framework_report():
    s = Screen("20-framework-reporte", "Reporte de stock · Fat-Free", height=900)
    s.rect(0, 0, 1440, 78, DARK)
    s.text(152, 48, "Fat-Free Framework", 24, WHITE, 700)
    s.pill(500, 24, 110, 30, "3.9.3", "#22304D", CYAN, size=9)
    s.text(1180, 48, "Reporte de stock  ›", 12, CYAN, 700, "end")
    s.text(152, 142, "Reporte de stock", 40, DARK, 700, ls=-1.2)
    s.para(152, 174, "Módulo independiente con routing, controlador, modelo ORM DB\\SQL\\Mapper y vistas con template engine.", 700, 12, 20, MUTED)
    s.card(152, 230, 1136, 420, 18, WHITE, BORDER, True)
    s.rect(176, 254, 1088, 40, "#F4F7F9", 8)
    for i, (label, x) in enumerate([("ID", 192), ("PRODUCTO", 260), ("CATEGORÍA", 620), ("PRECIO", 850), ("STOCK", 1030), ("ESTADO", 1140)]):
        s.text(x, 279, label, 8, MUTED, 700, ls=0.8)
    rows = [("1", "SSD NVMe 1 TB", "Almacenamiento", "$ 112.000", "10", "Disponible"), ("2", "Memoria RAM 16 GB", "Componentes", "$ 78.000", "8", "Disponible"), ("3", "Batería MacBook Air", "Repuestos", "$ 65.000", "4", "Stock bajo"), ("4", "Cargador USB-C 65 W", "Accesorios", "$ 32.000", "15", "Disponible"), ("5", "Teclado mecánico RGB", "Periféricos", "$ 54.000", "6", "Disponible"), ("6", "Auriculares Bluetooth", "Audio", "$ 39.900", "3", "Stock bajo")]
    for idx, row in enumerate(rows):
        y = 330 + idx * 52
        s.line(176, y - 20, 1264, y - 20, BORDER, 1)
        for i, (value, x) in enumerate(zip(row, [192, 260, 620, 850, 1030, 1140])):
            if i == 5:
                s.badge(x, y - 16, value, ORANGE if value == "Stock bajo" else GREEN, ORANGE_SOFT if value == "Stock bajo" else GREEN_SOFT, 86)
            else:
                s.text(x, y, value, 10, DARK if i in (1, 3, 4) else TEXT, 700 if i in (1, 3, 4) else 400)
    s.text(152, 750, "Rutas: /framework/reportes · /framework/reportes/4 · /framework/api/stock", 11, MUTED)
    s.rect(0, 800, 1440, 100, DARK)
    s.text(720, 845, "Fat-Free Framework · routing + controlador + ORM + vistas", 12, "#C9D4E3", 500, "middle")
    s.text(720, 866, "GET /framework/api/stock › JSON", 10, CYAN, 600, "middle")
    return s


def screen_framework_detail():
    s = Screen("21-framework-detalle", "Detalle de stock · Fat-Free", height=850)
    s.rect(0, 0, 1440, 78, DARK)
    s.text(152, 48, "Fat-Free Framework", 24, WHITE, 700)
    s.pill(500, 24, 110, 30, "3.9.3", "#22304D", CYAN, size=9)
    s.text(1180, 48, "‹ Volver al reporte", 12, CYAN, 700, "end")
    s.text(152, 150, "Detalle de producto", 38, DARK, 700, ls=-1.2)
    s.text(152, 180, "Reporte / Producto 4", 11, MUTED)
    s.card(152, 230, 560, 420, 20, WHITE, BORDER, True)
    product_image(s, 192, 270, 480, 340, "bolt", scale=2.4)
    s.card(760, 230, 528, 420, 20, WHITE, BORDER, True)
    s.text(794, 274, "Cargador USB-C 65 W", 28, DARK, 700)
    s.pill(794, 294, 120, 24, "ACCESORIOS", CYAN_SOFT, CYAN_DARK, size=8)
    s.text(794, 356, "$ 32.000", 34, DARK, 700)
    s.text(794, 384, "Stock: 15 unidades", 10, GREEN, 700)
    s.para(794, 420, "Cargador compacto de 65 W con Power Delivery. Compatible con notebooks, tablets y celulares USB-C.", 450, 11, 18, MUTED, max_lines=3)
    s.line(794, 500, 1254, 500, BORDER, 1)
    for i, (k, v) in enumerate([("Categoría", "Accesorios"), ("Estado", "Disponible"), ("Garantía", "90 días")]):
        s.text(794, 530 + i * 30, k, 10, MUTED)
        s.text(1254, 530 + i * 30, v, 10, DARK, 700, "end")
    s.text(152, 720, "GET /framework/reportes/4 · modelo Producto (DB\\SQL\\Mapper) · vista reportes/detalle.html", 11, MUTED)
    s.rect(0, 780, 1440, 70, DARK)
    s.text(720, 820, "Fat-Free Framework · ruta dinámica y plantilla HTML", 11, "#C9D4E3", 500, "middle")
    return s


def mobile_header(s, title="TechFix", back=False):
    s.rect(0, 0, 390, 32, DARK)
    s.text(16, 21, "Av. Rivadavia 1234 · CABA", 8, "#C9D4E3")
    s.text(374, 21, "+54 11 5555-0101", 8, "#C9D4E3", 400, "end")
    s.rect(0, 32, 390, 58, WHITE)
    s.line(0, 89, 390, 89, BORDER, 1)
    if back:
        s.text(18, 69, "‹", 18, DARK, 700)
    tx = 44 if back else 18
    s.rect(tx, 46, 30, 30, DARK, 9)
    s.icon("wrench", tx + 7, 53, 15, CYAN)
    s.text(tx + 38, 68, "Tech", 17, DARK, 700)
    s.text(tx + 78, 68, "Fix", 17, CYAN, 700)
    for i in range(3):
        s.line(352, 61 + i * 5, 372, 61 + i * 5, DARK, 2)


def mobile_bottom(s, active="Inicio"):
    s.rect(0, 780, 390, 64, WHITE)
    s.line(0, 780, 390, 780, BORDER, 1)
    items = [("Inicio", "grid"), ("Servicios", "wrench"), ("Tienda", "cart"), ("Cuenta", "user")]
    for i, (label, icon) in enumerate(items):
        x = 48 + i * 98
        color = CYAN_DARK if label == active else MUTED
        s.icon(icon, x - 10, 794, 18, color)
        s.text(x, 826, label, 8, color, 700 if label == active else 500, "middle")


def screen_mobile_home():
    s = Screen("24-mobile-inicio", "Inicio móvil · TechFix", width=390, height=844, group="Móvil")
    mobile_header(s)
    s.pill(18, 112, 190, 24, "SERVICIO TÉCNICO", CYAN_SOFT, CYAN_DARK, size=8)
    s.text(18, 172, "Tu equipo en", 32, DARK, 700, ls=-1.2)
    s.text(18, 208, "manos expertas.", 32, DARK, 700, ls=-1.2)
    s.text(18, 208 + 0, "", 1)
    s.para(18, 236, "Diagnóstico honesto, repuestos de calidad y seguimiento online.", 340, 11, 17, MUTED)
    s.button(18, 300, 170, 42, "Pedir presupuesto", "primary", 10)
    s.button(198, 300, 120, 42, "Ver servicios", "outline", 10)
    s.rect(18, 370, 354, 210, "#DDF4F9", 18)
    s.rect(142, 388, 106, 174, DARK, 20)
    s.circle(195, 475, 34, "none", "#2A3A55", 7)
    s.text(195, 481, "78%", 16, WHITE, 700, "middle")
    s.card(28, 400, 108, 46, 10, WHITE, BORDER, True)
    s.text(38, 418, "Diagnóstico", 8, DARK, 700)
    s.text(38, 432, "sin cargo", 8, MUTED)
    s.card(258, 492, 104, 46, 10, WHITE, BORDER, True)
    s.text(268, 510, "90 días", 9, DARK, 700)
    s.text(268, 524, "garantía", 8, MUTED)
    s.text(18, 626, "Servicios destacados", 17, DARK, 700)
    for i, (name, desc, icon) in enumerate([("Cambio de pantalla", "Desde $ 85.000", "phone"), ("Cambio de SSD", "Desde $ 60.000", "laptop")]):
        y = 650 + i * 58
        s.card(18, y, 354, 48, 10, WHITE, BORDER)
        s.icon_box(28, y + 8, 32, icon)
        s.text(70, y + 21, name, 10, DARK, 700)
        s.text(70, y + 37, desc, 8, MUTED)
        s.text(358, y + 29, "›", 12, CYAN_DARK, 700, "end")
    mobile_bottom(s)
    return s


def screen_mobile_store():
    s = Screen("25-mobile-tienda", "Tienda móvil · TechFix", width=390, height=844, group="Móvil")
    mobile_header(s)
    s.text(18, 126, "Tienda", 28, DARK, 700, ls=-0.8)
    s.text(18, 148, "Repuestos y accesorios", 10, MUTED)
    s.card(18, 168, 354, 42, 10, WHITE, BORDER)
    s.icon("search", 32, 180, 16, MUTED)
    s.text(58, 194, "Buscar productos...", 10, "#98A2B3")
    for i, label in enumerate(["Todos", "Almacenamiento", "Accesorios"]):
        x = 18 + i * (70 + (0 if i == 0 else 42))
        s.pill(x, 226, 64 if i == 0 else 120, 26, label, DARK if i == 0 else WHITE, WHITE if i == 0 else TEXT, None if i == 0 else BORDER, size=8)
    for idx, (name, cat, price, icon) in enumerate(PRODUCTS[:4]):
        col, row = idx % 2, idx // 2
        x, y = 18 + col * 182, 272 + row * 218
        s.card(x, y, 172, 200, 12, WHITE, BORDER, True)
        product_image(s, x + 8, y + 8, 156, 100, icon)
        s.text(x + 10, y + 130, name, 9, DARK, 700)
        s.text(x + 10, y + 156, money(price), 12, DARK, 700)
        s.text(x + 10, y + 174, "Ver producto  ›", 8, CYAN_DARK, 700)
    mobile_bottom(s, "Tienda")
    return s


def screen_mobile_request():
    s = Screen("26-mobile-solicitud", "Solicitud móvil · TechFix", width=390, height=844, group="Móvil")
    mobile_header(s, back=True)
    s.text(18, 124, "Pedir presupuesto", 26, DARK, 700, ls=-0.8)
    s.text(18, 146, "Te respondemos en menos de 24 h.", 9, MUTED)
    s.input(18, 170, 354, "Nombre *", "Lucía Fernández")
    s.input(18, 250, 354, "Correo *", "nombre@dominio.com")
    s.input(18, 330, 354, "Teléfono *", "+54 11 5555-0101")
    s.text(18, 418, "Servicio *", 9, DARK, 700)
    s.rect(18, 428, 354, 40, WHITE, 9, BORDER)
    s.text(30, 453, "Cambio de pantalla", 10, TEXT)
    s.chevron(346, 448, MUTED, 10)
    s.text(18, 492, "Contanos la falla *", 9, DARK, 700)
    s.rect(18, 502, 354, 90, WHITE, 9, BORDER)
    s.para(30, 524, "La pantalla se rompió después de una caída. El táctil funciona, pero se ve una mancha negra.", 326, 9, 14, TEXT, max_lines=3)
    s.checkbox(18, 618, "Acepto la política de privacidad *", size=9)
    s.button(18, 646, 354, 44, "Enviar solicitud", "primary", 11)
    s.text(195, 712, "También podés llamarnos al +54 11 5555-0101", 8, MUTED, 400, "middle")
    mobile_bottom(s)
    return s


def all_screens():
    from screens_public import screen_system, screen_home, screen_services, screen_service_detail, screen_request, screen_confirm
    from screens_shop import screen_tracking, screen_store, screen_cart, screen_contact, screen_about, screen_login, screen_register, screen_404, screen_sitemap
    from screens_admin import (
        screen_dashboard, screen_admin_panel, screen_admin_services, screen_admin_repairs,
        screen_admin_users, screen_mvc_catalog, screen_mvc_detail, screen_framework_report,
        screen_framework_detail, screen_mobile_home, screen_mobile_store, screen_mobile_request,
    )

    return [
        screen_system(), screen_home(), screen_services(), screen_service_detail(),
        screen_request(), screen_confirm(), screen_tracking(), screen_store(),
        screen_cart(), screen_contact(), screen_about(), screen_login(),
        screen_register(), screen_dashboard(), screen_admin_panel(), screen_admin_services(),
        screen_admin_repairs(), screen_admin_users(), screen_mvc_catalog(), screen_mvc_detail(),
        screen_framework_report(), screen_framework_detail(), screen_404(), screen_sitemap(),
        screen_mobile_home(), screen_mobile_store(), screen_mobile_request(),
    ]
