#!/usr/bin/env python3
"""
TechFix · Generador del prototipo.

Produce:
  prototipo/svg/*.svg        pantallas vectoriales
  prototipo/screens.json     manifiesto
  prototipo/index.html       visor navegable
  figma-plugin/code.js       plugin que inserta todo en Figma
  figma-plugin/manifest.json

Solo usa la biblioteca estándar de Python.
"""

import json
from datetime import date
from pathlib import Path

from screens_admin import all_screens

ROOT = Path(__file__).resolve().parent
SVG_DIR = ROOT / "svg"
SVG_DIR.mkdir(exist_ok=True)
PLUGIN_DIR = ROOT.parent / "figma-plugin"
PLUGIN_DIR.mkdir(exist_ok=True)

FLOW = [
    ("Inicio", "01-inicio"),
    ("Servicios", "02-servicios"),
    ("Solicitud", "04-solicitud"),
    ("Seguimiento", "06-seguimiento"),
    ("Tienda", "07-tienda"),
    ("Mi panel", "13-dashboard-cliente"),
    ("Admin", "14-admin-panel"),
]


def build_manifest(screens):
    items = []
    for s in screens:
        path = s.save()
        items.append({
            "slug": s.slug,
            "title": s.title,
            "group": s.group,
            "w": s.w,
            "h": s.h,
            "file": path.name,
        })
    return items


def build_nav(items):
    groups = []
    for item in items:
        if item["group"] not in groups:
            groups.append(item["group"])
    out = []
    for group in groups:
        out.append(f'<div class="group"><h3>{group}</h3>')
        for i, item in enumerate(items):
            if item["group"] != group:
                continue
            out.append(
                f'<button class="item" data-slug="{item["slug"]}" '
                f'data-title="{item["title"]}">'
                f'<span class="num">{i + 1:02d}</span>'
                f'<span class="label">{item["title"]}</span>'
                f'<span class="dims">{item["w"]}px</span></button>'
            )
        out.append("</div>")
    return "\n".join(out)


def build_flow():
    return "".join(
        f'<button class="chip" data-slug="{slug}">{label}</button>'
        for label, slug in FLOW
    )


def write_viewer(items):
    template = (ROOT / "plantilla_visor.html").read_text(encoding="utf-8")
    html = (
        template
        .replace("__TOTAL__", str(len(items)))
        .replace("__DATE__", date.today().strftime("%d/%m/%Y"))
        .replace("__NAV__", build_nav(items))
        .replace("__FLOW__", build_flow())
        .replace("__SCREENS_JSON__", json.dumps(items, ensure_ascii=False))
        .replace("__FLOW_JSON__", json.dumps(
            [{"label": label, "slug": slug} for label, slug in FLOW],
            ensure_ascii=False,
        ))
    )
    (ROOT / "index.html").write_text(html, encoding="utf-8")


PLUGIN_JS = r"""
const PAGE_NAME = "TechFix · Prototipo";
const GAP = 90;
const COLS = 3;
const LABEL_H = 34;
const GROUP_GAP = 150;

const SCREENS = __SCREENS__;

function svgWithoutLinks(svg) {
  return svg.replace(/<a\b[^>]*>/g, "").replace(/<\/a>/g, "");
}

function makeScreenNode(svg) {
  try {
    return figma.createNodeFromSvg(svg);
  } catch (err) {
    console.warn("Reintentando sin enlaces SVG", err);
    return figma.createNodeFromSvg(svgWithoutLinks(svg));
  }
}

function loadInter() {
  const fonts = [
    { family: "Inter", style: "Regular" },
    { family: "Inter", style: "Bold" },
    { family: "Inter", style: "Semi Bold" },
  ];
  return Promise.all(fonts.map(f => figma.loadFontAsync(f).catch(() => null)));}

async function main() {
  await loadInter();

  let page = figma.root.children.find(p => p.name === PAGE_NAME);
  if (!page) {
    page = figma.createPage();
    page.name = PAGE_NAME;
  }
  figma.currentPage = page;
  for (const child of [...page.children]) child.remove();

  try {
    page.backgrounds = [{
      type: "SOLID",
      color: { r: 0.957, g: 0.969, b: 0.976 }
    }];
  } catch (e) {
    console.warn("No se pudo cambiar el fondo de la página", e);
  }

  const groups = [];
  for (const screen of SCREENS) {
    if (!groups.includes(screen.group)) groups.push(screen.group);
  }

  let cursorY = 0;
  let placed = 0;

  for (const group of groups) {
    const heading = figma.createText();
    heading.name = "Sección · " + group;
    heading.fontName = { family: "Inter", style: "Bold" };
    heading.fontSize = 44;
    heading.characters = group.toUpperCase();
    heading.fills = [{ type: "SOLID", color: { r: 0.1, g: 0.1, b: 0.18 } }];
    heading.x = 0;
    heading.y = cursorY;
    cursorY += 72;

    const list = SCREENS.filter(s => s.group === group);
    let col = 0;
    let rowY = cursorY;
    let rowH = 0;
    let count = 0;

    for (const screen of list) {
      const node = makeScreenNode(screen.svg);
      node.name = String(placed + 1).padStart(2, "0") + " · " + screen.title;

      const cellX = col * (node.width + GAP);

      const label = figma.createText();
      label.name = "Etiqueta · " + screen.title;
      label.fontName = { family: "Inter", style: "Bold" };
      label.fontSize = 22;
      label.characters = String(placed + 1).padStart(2, "0") + " · " + screen.title;
      label.fills = [{ type: "SOLID", color: { r: 0.4, g: 0.44, b: 0.52 } }];
      label.x = cellX;
      label.y = rowY;
      label.textAutoResize = "WIDTH_AND_HEIGHT";

      node.x = cellX;
      node.y = rowY + LABEL_H;

      rowH = Math.max(rowH, node.height + LABEL_H);
      placed += 1;
      count += 1;
      col += 1;

      if (col === COLS || count === list.length) {
        rowY += rowH + GAP;
        rowH = 0;
        col = 0;
      }
    }
    cursorY = rowY + GROUP_GAP;
  }

  const first = page.children[0];
  if (first) {
    figma.viewport.scrollAndZoomIntoView(page.children);
  }
  figma.closePlugin("TechFix: " + placed + " pantallas insertadas en la página " + PAGE_NAME);
}

main().catch(err => {
  console.error(err);
  figma.closePlugin("Error al generar el prototipo: " + err.message);
});
"""


def write_plugin(items):
    payload = []
    for item in items:
        payload.append({
            "slug": item["slug"],
            "title": item["title"],
            "group": item["group"],
            "w": item["w"],
            "h": item["h"],
            "svg": (SVG_DIR / item["file"]).read_text(encoding="utf-8"),
        })

    js = PLUGIN_JS.replace("__SCREENS__", json.dumps(payload, ensure_ascii=False))
    (PLUGIN_DIR / "code.js").write_text(js, encoding="utf-8")

    manifest = {
        "name": "TechFix · Prototipo",
        "id": "techfix-prototype-2026",
        "api": "1.0.0",
        "main": "code.js",
        "editorType": ["figma"],
    }
    (PLUGIN_DIR / "manifest.json").write_text(
        json.dumps(manifest, indent=2, ensure_ascii=False) + "\n",
        encoding="utf-8",
    )


def main():
    screens = all_screens()
    items = build_manifest(screens)
    (ROOT / "screens.json").write_text(
        json.dumps(items, indent=2, ensure_ascii=False) + "\n", encoding="utf-8"
    )
    write_viewer(items)
    write_plugin(items)
    print(f"{len(items)} pantallas generadas")
    print(f"SVG:      {SVG_DIR}")
    print(f"Visor:    {ROOT / 'index.html'}")
    print(f"Plugin:   {PLUGIN_DIR}")


if __name__ == "__main__":
    main()
