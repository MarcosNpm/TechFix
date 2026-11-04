#!/usr/bin/env python3
"""Biblioteca de componentes para el prototipo vectorial de TechFix."""

import html
from pathlib import Path

FONT = "Inter, Segoe UI, Arial, sans-serif"
DARK = "#1A1A2E"
DARK_2 = "#22223B"
CYAN = "#00A8CC"
CYAN_DARK = "#007A99"
CYAN_SOFT = "#E6F7FB"
BG = "#F4F7F9"
WHITE = "#FFFFFF"
TEXT = "#333333"
MUTED = "#667085"
BORDER = "#E2E8F0"
GREEN = "#12B76A"
GREEN_SOFT = "#E7F8F0"
ORANGE = "#F79009"
ORANGE_SOFT = "#FFF4E5"
RED = "#B42318"
RED_SOFT = "#FDECEA"
PURPLE = "#7A5AF8"
PURPLE_SOFT = "#F0EDFF"
BLUE_SOFT = "#EAF4FF"

NAV = [
    ("inicio", "Inicio", "01-inicio"),
    ("servicios", "Servicios", "02-servicios"),
    ("tienda", "Tienda", "07-tienda"),
    ("seguimiento", "Seguimiento", "06-seguimiento"),
    ("taller", "Sobre el taller", "10-sobre-el-taller"),
    ("contacto", "Contacto", "09-contacto"),
]


def esc(value):
    return html.escape(str(value), quote=True)


def wrap(text, max_chars):
    words = str(text).split()
    lines, current = [], ""
    for word in words:
        candidate = f"{current} {word}".strip()
        if len(candidate) <= max_chars or not current:
            current = candidate
        else:
            lines.append(current)
            current = word
    if current:
        lines.append(current)
    return lines


class Screen:
    def __init__(self, slug, title, width=1440, height=900, bg=BG, group="Escritorio"):
        self.slug = slug
        self.title = title
        self.w = width
        self.h = height
        self.bg = bg
        self.group = group
        self.parts = []
        self.defs = []

    # ------------------------------------------------------------ primitivas
    def add(self, markup):
        self.parts.append(markup)

    def rect(self, x, y, w, h, fill=WHITE, rx=0, stroke=None, sw=1, opacity=1, extra=""):
        attrs = f'x="{x}" y="{y}" width="{w}" height="{h}" fill="{fill}" rx="{rx}"'
        if stroke:
            attrs += f' stroke="{stroke}" stroke-width="{sw}"'
        if opacity != 1:
            attrs += f' opacity="{opacity}"'
        self.add(f"<rect {attrs} {extra}/>")

    def circle(self, cx, cy, r, fill=WHITE, stroke=None, sw=1, opacity=1):
        attrs = f'cx="{cx}" cy="{cy}" r="{r}" fill="{fill}"'
        if stroke:
            attrs += f' stroke="{stroke}" stroke-width="{sw}"'
        if opacity != 1:
            attrs += f' opacity="{opacity}"'
        self.add(f"<circle {attrs}/>")

    def path(self, d, fill="none", stroke=None, sw=2, opacity=1, cap="round", join="round"):
        attrs = f'd="{d}" fill="{fill}"'
        if stroke:
            attrs += f' stroke="{stroke}" stroke-width="{sw}" stroke-linecap="{cap}" stroke-linejoin="{join}"'
        if opacity != 1:
            attrs += f' opacity="{opacity}"'
        self.add(f"<path {attrs}/>")

    def line(self, x1, y1, x2, y2, color=BORDER, sw=1, dash=None):
        attrs = f'x1="{x1}" y1="{y1}" x2="{x2}" y2="{y2}" stroke="{color}" stroke-width="{sw}"'
        if dash:
            attrs += f' stroke-dasharray="{dash}"'
        self.add(f"<line {attrs}/>")

    def text(self, x, y, value, size=14, color=TEXT, weight=400, anchor="start", ls=0, opacity=1):
        attrs = f'x="{x}" y="{y}" font-family="{FONT}" font-size="{size}" fill="{color}"'
        if weight != 400:
            attrs += f' font-weight="{weight}"'
        if anchor != "start":
            attrs += f' text-anchor="{anchor}"'
        if ls:
            attrs += f' letter-spacing="{ls}"'
        if opacity != 1:
            attrs += f' opacity="{opacity}"'
        self.add(f'<text {attrs}>{esc(value)}</text>')

    def para(self, x, y, value, width, size=14, lh=None, color=TEXT, weight=400, max_lines=None):
        lh = lh or int(size * 1.55)
        chars = max(8, int(width / (size * 0.53)))
        lines = wrap(value, chars)
        if max_lines:
            lines = lines[:max_lines]
        for i, line in enumerate(lines):
            self.text(x, y + i * lh, line, size, color, weight)
        return y + len(lines) * lh

    def label(self, x, y, value, color=CYAN_DARK, size=10, weight=700):
        self.text(x, y, value.upper(), size, color, weight, ls=1.4)

    # ------------------------------------------------------------ componentes
    def pill(self, x, y, w, h, label, fill=CYAN_SOFT, color=CYAN_DARK, stroke=None, size=11, weight=700):
        self.rect(x, y, w, h, fill, h / 2, stroke)
        self.text(x + w / 2, y + h / 2 + size * 0.35, label, size, color, weight, "middle", ls=0.4)

    def button(self, x, y, w, h, label, variant="primary", size=13):
        if variant == "primary":
            self.rect(x, y, w, h, CYAN, h / 2)
            color, weight = WHITE, 700
        elif variant == "dark":
            self.rect(x, y, w, h, DARK, h / 2)
            color, weight = WHITE, 700
        elif variant == "outline":
            self.rect(x, y, w, h, WHITE, h / 2, CYAN, 1.5)
            color, weight = CYAN_DARK, 700
        elif variant == "ghost":
            self.rect(x, y, w, h, WHITE, h / 2, BORDER, 1)
            color, weight = TEXT, 600
        else:
            self.rect(x, y, w, h, CYAN_SOFT, h / 2)
            color, weight = CYAN_DARK, 700
        self.text(x + w / 2, y + h / 2 + size * 0.35, label, size, color, weight, "middle")

    def icon_box(self, x, y, size, kind="wrench", fill=CYAN_SOFT, color=CYAN_DARK):
        self.rect(x, y, size, size, fill, size * 0.28)
        self.icon(kind, x + size * 0.24, y + size * 0.24, size * 0.52, color)

    def icon(self, kind, x, y, size, color=CYAN_DARK):
        s = size
        if kind in ("phone", "celular"):
            self.rect(x + s * 0.26, y, s * 0.48, s, "none", s * 0.12, color, max(1.2, s * 0.07))
            self.line(x + s * 0.4, y + s * 0.82, x + s * 0.6, y + s * 0.82, color, max(1.2, s * 0.07))
        elif kind in ("laptop", "computadora"):
            self.rect(x + s * 0.14, y + s * 0.08, s * 0.72, s * 0.5, "none", s * 0.05, color, max(1.2, s * 0.07))
            self.path(f"M {x + s * 0.04} {y + s * 0.72} L {x + s * 0.96} {y + s * 0.72} L {x + s * 0.84} {y + s * 0.9} L {x + s * 0.16} {y + s * 0.9} Z", "none", color, max(1.2, s * 0.07))
        elif kind == "battery":
            self.rect(x + s * 0.08, y + s * 0.22, s * 0.72, s * 0.56, "none", s * 0.1, color, max(1.2, s * 0.07))
            self.rect(x + s * 0.8, y + s * 0.38, s * 0.12, s * 0.24, color, s * 0.03)
            self.line(x + s * 0.24, y + s * 0.5, x + s * 0.64, y + s * 0.5, color, max(1.2, s * 0.07))
        elif kind == "screen":
            self.rect(x + s * 0.08, y + s * 0.12, s * 0.84, s * 0.62, "none", s * 0.05, color, max(1.2, s * 0.07))
            self.line(x + s * 0.5, y + s * 0.74, x + s * 0.5, y + s * 0.9, color, max(1.2, s * 0.07))
            self.line(x + s * 0.3, y + s * 0.9, x + s * 0.7, y + s * 0.9, color, max(1.2, s * 0.07))
        elif kind == "data":
            self.path(f"M {x + s * 0.16} {y + s * 0.2} A {s * 0.34} {s * 0.14} 0 0 0 {x + s * 0.84} {y + s * 0.2}", "none", color, max(1.2, s * 0.07))
            self.line(x + s * 0.16, y + s * 0.2, x + s * 0.16, y + s * 0.8, color, max(1.2, s * 0.07))
            self.line(x + s * 0.84, y + s * 0.2, x + s * 0.84, y + s * 0.8, color, max(1.2, s * 0.07))
            self.path(f"M {x + s * 0.16} {y + s * 0.5} A {s * 0.34} {s * 0.14} 0 0 0 {x + s * 0.84} {y + s * 0.5}", "none", color, max(1.2, s * 0.07))
            self.path(f"M {x + s * 0.16} {y + s * 0.8} A {s * 0.34} {s * 0.14} 0 0 0 {x + s * 0.84} {y + s * 0.8}", "none", color, max(1.2, s * 0.07))
        elif kind == "bolt":
            self.path(f"M {x + s * 0.58} {y} L {x + s * 0.18} {y + s * 0.56} L {x + s * 0.48} {y + s * 0.56} L {x + s * 0.38} {y + s} L {x + s * 0.84} {y + s * 0.38} L {x + s * 0.54} {y + s * 0.38} Z", color)
        elif kind == "wrench":
            self.path(f"M {x + s * 0.62} {y + s * 0.05} A {s * 0.22} {s * 0.22} 0 1 0 {x + s * 0.95} {y + s * 0.38} L {x + s * 0.46} {y + s * 0.88} A {s * 0.13} {s * 0.13} 0 1 1 {x + s * 0.16} {y + s * 0.58} Z", color)
        elif kind == "search":
            self.circle(x + s * 0.42, y + s * 0.42, s * 0.3, "none", color, max(1.2, s * 0.09))
            self.line(x + s * 0.64, y + s * 0.64, x + s * 0.94, y + s * 0.94, color, max(1.2, s * 0.09))
        elif kind == "cart":
            self.path(f"M {x + s * 0.05} {y + s * 0.08} L {x + s * 0.22} {y + s * 0.08} L {x + s * 0.36} {y + s * 0.66} L {x + s * 0.82} {y + s * 0.66} L {x + s * 0.94} {y + s * 0.24} L {x + s * 0.26} {y + s * 0.24}", "none", color, max(1.2, s * 0.08))
            self.circle(x + s * 0.38, y + s * 0.85, s * 0.08, color)
            self.circle(x + s * 0.78, y + s * 0.85, s * 0.08, color)
        elif kind == "check":
            self.path(f"M {x + s * 0.08} {y + s * 0.52} L {x + s * 0.4} {y + s * 0.84} L {x + s * 0.94} {y + s * 0.14}", "none", color, max(1.5, s * 0.11))
        elif kind == "clock":
            self.circle(x + s / 2, y + s / 2, s * 0.42, "none", color, max(1.2, s * 0.08))
            self.path(f"M {x + s / 2} {y + s * 0.24} L {x + s / 2} {y + s * 0.52} L {x + s * 0.7} {y + s * 0.64}", "none", color, max(1.2, s * 0.08))
        elif kind == "shield":
            self.path(f"M {x + s / 2} {y + s * 0.05} L {x + s * 0.9} {y + s * 0.22} L {x + s * 0.82} {y + s * 0.66} L {x + s / 2} {y + s * 0.94} L {x + s * 0.18} {y + s * 0.66} L {x + s * 0.1} {y + s * 0.22} Z", "none", color, max(1.2, s * 0.08))
        elif kind == "user":
            self.circle(x + s / 2, y + s * 0.3, s * 0.2, "none", color, max(1.2, s * 0.08))
            self.path(f"M {x + s * 0.12} {y + s * 0.94} C {x + s * 0.12} {y + s * 0.6} {x + s * 0.88} {y + s * 0.6} {x + s * 0.88} {y + s * 0.94}", "none", color, max(1.2, s * 0.08))
        elif kind == "grid":
            for dx, dy in [(0.08, 0.08), (0.56, 0.08), (0.08, 0.56), (0.56, 0.56)]:
                self.rect(x + s * dx, y + s * dy, s * 0.32, s * 0.32, "none", s * 0.06, color, max(1.2, s * 0.07))
        elif kind == "box":
            self.path(f"M {x + s * 0.08} {y + s * 0.28} L {x + s / 2} {y + s * 0.08} L {x + s * 0.92} {y + s * 0.28} L {x + s * 0.92} {y + s * 0.76} L {x + s / 2} {y + s * 0.96} L {x + s * 0.08} {y + s * 0.76} Z", "none", color, max(1.2, s * 0.07))
            self.line(x + s * 0.08, y + s * 0.28, x + s / 2, y + s * 0.5, color, max(1.2, s * 0.07))
            self.line(x + s * 0.92, y + s * 0.28, x + s / 2, y + s * 0.5, color, max(1.2, s * 0.07))
        elif kind == "logout":
            self.path(f"M {x + s * 0.46} {y + s * 0.08} L {x + s * 0.12} {y + s * 0.08} L {x + s * 0.12} {y + s * 0.92} L {x + s * 0.46} {y + s * 0.92}", "none", color, max(1.2, s * 0.08))
            self.line(x + s * 0.36, y + s * 0.5, x + s * 0.94, y + s * 0.5, color, max(1.2, s * 0.08))
            self.path(f"M {x + s * 0.72} {y + s * 0.28} L {x + s * 0.94} {y + s * 0.5} L {x + s * 0.72} {y + s * 0.72}", "none", color, max(1.2, s * 0.08))
        elif kind == "pin":
            self.path(f"M {x + s / 2} {y + s * 0.05} C {x + s * 0.12} {y + s * 0.05} {x + s * 0.08} {y + s * 0.62} {x + s / 2} {y + s * 0.95} C {x + s * 0.92} {y + s * 0.62} {x + s * 0.88} {y + s * 0.05} {x + s / 2} {y + s * 0.05} Z", "none", color, max(1.2, s * 0.07))
            self.circle(x + s / 2, y + s * 0.4, s * 0.12, color)
        elif kind == "phone-call":
            self.path(f"M {x + s * 0.14} {y + s * 0.08} L {x + s * 0.34} {y + s * 0.08} L {x + s * 0.42} {y + s * 0.34} L {x + s * 0.28} {y + s * 0.46} C {x + s * 0.38} {y + s * 0.7} {x + s * 0.62} {y + s * 0.82} {x + s * 0.78} {y + s * 0.86} L {x + s * 0.88} {y + s * 0.7} L {x + s * 0.94} {y + s * 0.76} L {x + s * 0.92} {y + s * 0.94} C {x + s * 0.58} {y + s * 0.94} {x + s * 0.06} {y + s * 0.42} {x + s * 0.06} {y + s * 0.18} Z", "none", color, max(1.2, s * 0.07))
        elif kind == "mail":
            self.rect(x + s * 0.06, y + s * 0.2, s * 0.88, s * 0.6, "none", s * 0.06, color, max(1.2, s * 0.07))
            self.path(f"M {x + s * 0.08} {y + s * 0.24} L {x + s / 2} {y + s * 0.56} L {x + s * 0.92} {y + s * 0.24}", "none", color, max(1.2, s * 0.07))
        elif kind == "star":
            self.path(f"M {x + s / 2} {y + s * 0.04} L {x + s * 0.62} {y + s * 0.36} L {x + s * 0.96} {y + s * 0.38} L {x + s * 0.7} {y + s * 0.6} L {x + s * 0.79} {y + s * 0.94} L {x + s / 2} {y + s * 0.75} L {x + s * 0.21} {y + s * 0.94} L {x + s * 0.3} {y + s * 0.6} L {x + s * 0.04} {y + s * 0.38} L {x + s * 0.38} {y + s * 0.36} Z", color)
        else:
            self.circle(x + s / 2, y + s / 2, s * 0.32, "none", color, max(1.2, s * 0.08))

    def badge(self, x, y, label, color=CYAN_DARK, fill=CYAN_SOFT, w=None):
        w = w or (len(label) * 7.2 + 24)
        self.pill(x, y, w, 24, label, fill, color, size=10)

    def card(self, x, y, w, h, rx=18, fill=WHITE, stroke=BORDER, shadow=False):
        if shadow:
            self.rect(x + 3, y + 8, w, h, "#0F172A", rx, opacity=0.06)
        self.rect(x, y, w, h, fill, rx, stroke, 1)

    def avatar(self, x, y, size, label="L", color=CYAN_DARK):
        self.circle(x + size / 2, y + size / 2, size / 2, color)
        self.text(x + size / 2, y + size / 2 + size * 0.16, label, size * 0.42, WHITE, 700, "middle")

    # ------------------------------------------------------------ navegación
    def header(self, active="inicio", logged=False, admin=False):
        self.rect(0, 0, self.w, 36, DARK)
        self.icon("pin", 150, 10, 15, "#9FB0C3")
        self.text(172, 23, "Av. Rivadavia 1234, CABA", 12, "#C9D4E3")
        self.icon("clock", 365, 10, 15, "#9FB0C3")
        self.text(388, 23, "Lun a Vie · 9 a 19 h · Sáb · 9 a 13 h", 12, "#C9D4E3")
        self.icon("phone-call", 1080, 10, 15, "#9FB0C3")
        self.text(1102, 23, "+54 11 5555-0101", 12, "#C9D4E3")

        self.rect(0, 36, self.w, 68, WHITE)
        self.line(0, 103, self.w, 103, BORDER, 1)
        self.add('<a href="../index.html#01-inicio" target="_top">')
        self.rect(152, 50, 40, 40, DARK, 13)
        self.icon("wrench", 162, 59, 20, CYAN)
        self.text(202, 79, "Tech", 25, DARK, 700, ls=-0.8)
        self.text(259, 79, "Fix", 25, CYAN, 700, ls=-0.8)
        self.add("</a>")

        x = 330
        for key, label, target in NAV:
            width = 64 if label != "Sobre el taller" else 118
            if label == "Seguimiento":
                width = 92
            color = CYAN_DARK if key == active else TEXT
            self.add(f'<a href="../index.html#{target}" target="_top">')
            self.text(x, 78, label, 14, color, 700 if key == active else 500)
            if key == active:
                self.rect(x, 92, width - 10, 3, CYAN, 2)
            self.add("</a>")
            x += width + 14

        self.add('<a href="../index.html#08-carrito" target="_top">')
        self.circle(1030, 70, 22, WHITE, BORDER, 1)
        self.icon("cart", 1020, 60, 20, DARK)
        self.add("</a>")
        if logged:
            self.circle(1084, 70, 18, CYAN_DARK)
            self.text(1084, 76, "L", 14, WHITE, 700, "middle")
            self.text(1110, 76, "Lucía" if not admin else "Marcos", 13, DARK, 700)
            self.add('<a href="../index.html#01-inicio" target="_top">')
            self.icon("logout", 1240, 62, 16, MUTED)
            self.text(1264, 76, "Salir", 13, MUTED, 600)
            self.add("</a>")
        else:
            self.add('<a href="../index.html#11-login" target="_top">')
            self.button(1070, 53, 86, 34, "Ingresar", "ghost", 12)
            self.add("</a>")
            self.add('<a href="../index.html#12-registro" target="_top">')
            self.button(1166, 53, 132, 34, "Crear cuenta", "primary", 12)
            self.add("</a>")

    def footer(self, y=None, compact=False):
        y = y if y is not None else self.h - 200
        self.rect(0, y, self.w, self.h - y, DARK)
        self.rect(152, y + 34, 34, 34, DARK_2, 11)
        self.icon("wrench", 160, y + 41, 18, CYAN)
        self.text(196, y + 59, "Tech", 20, WHITE, 700)
        self.text(243, y + 59, "Fix", 20, CYAN, 700)
        self.para(152, y + 84, "Reparamos celulares, computadoras y electrónica con diagnóstico claro, garantía escrita y seguimiento online.", 260, 11, 17, "#AAB4C4", max_lines=3)
        for i, label in enumerate(["W", "IG", "FB", "@"], 0):
            self.rect(152 + i * 38, y + 140, 28, 28, "#25253D", 8)
            self.text(166 + i * 38, y + 158, label, 9, "#C9D4E3", 700, "middle")

        cols = [
            (470, "NAVEGACIÓN", ["Servicios", "Tienda de repuestos", "Seguimiento", "Sobre el taller", "Contacto"]),
            (650, "SERVICIOS", ["Reparación de celulares", "Reparación de computadoras", "Mantenimiento preventivo", "Recuperación de datos", "Electrónica y soldadura"]),
        ]
        for cx, title, items in cols:
            self.label(cx, y + 44, title, color="#C9D4E3", size=10)
            for i, item in enumerate(items):
                self.text(cx, y + 72 + i * 24, item, 11, "#AAB4C4")

        cx = 900
        self.label(cx, y + 44, "CONTACTO", color="#C9D4E3", size=10)
        self.icon("pin", cx, y + 60, 13, CYAN)
        self.text(cx + 20, y + 72, "Av. Rivadavia 1234, CABA", 11, "#AAB4C4")
        self.icon("phone-call", cx, y + 86, 13, CYAN)
        self.text(cx + 20, y + 98, "+54 11 5555-0101", 11, "#AAB4C4")
        self.icon("mail", cx, y + 112, 13, CYAN)
        self.text(cx + 20, y + 124, "soporte@techfix.com.ar", 11, "#AAB4C4")
        self.button(cx, y + 146, 142, 32, "Pedir presupuesto", "outline", 11)

        if not compact:
            self.line(152, self.h - 48, self.w - 152, self.h - 48, "#2E2E4A", 1)
            self.text(152, self.h - 24, "© 2026 TechFix. Todos los derechos reservados.", 10, "#7D8799")
            self.text(1180, self.h - 24, "Prototipo · Núñez Marcos Agustín", 10, "#7D8799")

    def breadcrumb(self, y, current="Servicios", parent="Inicio"):
        self.text(152, y, parent, 11, MUTED)
        self.text(198, y, "›", 11, "#98A2B3")
        self.text(218, y, current, 11, DARK, 700)

    def page_title(self, y, eyebrow, title, subtitle=None, width=650):
        self.pill(152, y - 36, min(360, len(eyebrow) * 7.5 + 54), 28, eyebrow, CYAN_SOFT, CYAN_DARK, size=10, weight=700)
        self.text(152, y + 36, title, 48, DARK, 700, ls=-1.5)
        if subtitle:
            self.para(152, y + 66, subtitle, width, 14, 22, MUTED)

    def checkbox(self, x, y, label, size=10, color=MUTED):
        """Casilla dibujada (evita glifos que no existen en todas las fuentes)."""
        self.rect(x, y - size + 1, size, size, WHITE, 2, "#B8C4D4", 1)
        self.text(x + size + 8, y, label, 10, color)

    def chevron(self, x, y, color=MUTED, size=10, direction="down"):
        if direction == "down":
            self.path(f"M {x} {y} L {x + size / 2} {y + size / 2} L {x + size} {y}", "none", color, 1.6)
        else:
            self.path(f"M {x} {y} L {x + size / 2} {y - size / 2} L {x + size} {y}", "none", color, 1.6)

    def input(self, x, y, w, label, placeholder, height=44, value=None):
        self.text(x, y, label, 11, DARK, 700)
        self.rect(x, y + 10, w, height, WHITE, 10, BORDER, 1)
        self.text(x + 14, y + 10 + height / 2 + 5, value or placeholder, 12, TEXT if value else "#98A2B3")

    def stat_card(self, x, y, w, h, label, value, note="", icon="box"):
        self.card(x, y, w, h, 14)
        self.icon(icon, x + 18, y + 18, 18, CYAN_DARK)
        self.text(x + 46, y + 32, label, 10, MUTED, 700, ls=0.8)
        self.text(x + 18, y + 72, str(value), 26, DARK, 700)
        if note:
            self.text(x + 18, y + 92, note, 10, MUTED)

    def save(self):
        body = "".join(self.parts)
        defs = "".join(self.defs)
        svg = (
            f'<svg xmlns="http://www.w3.org/2000/svg" width="{self.w}" height="{self.h}" '
            f'viewBox="0 0 {self.w} {self.h}" role="img" aria-label="{esc(self.title)}">'
            f"<title>{esc(self.title)}</title>"
            f"<defs>{defs}</defs>"
            f'<rect width="{self.w}" height="{self.h}" fill="{self.bg}"/>'
            f"{body}</svg>"
        )
        path = Path(__file__).resolve().parent / "svg" / f"{self.slug}.svg"
        path.write_text(svg, encoding="utf-8")
        return path
