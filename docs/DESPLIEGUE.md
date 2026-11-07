# Publicación del TP3 en GitHub Pages

El repositorio contiene dos partes:

- `tp3-maquetacion/`: sitio **estático** (HTML, CSS y JS). Es lo que se publica en GitHub Pages.
- El resto del repositorio: proyecto **PHP** de los TP4 a TP10. GitHub Pages no ejecuta PHP,
  por eso no se publica allí.

---

## 1. Repositorio

- Repositorio: <https://github.com/MarcosNpm/TechFix>
- Sitio publicado: <https://marcosnpm.github.io/TechFix/>

El repositorio debe ser **público** para que GitHub Pages funcione en el plan gratuito.

---

## 2. Activar GitHub Pages (una sola vez)

1. Entrar a <https://github.com/MarcosNpm/TechFix/settings/pages>.
2. En **Build and deployment → Source**, elegir **GitHub Actions**.
3. Ir a la pestaña **Actions** y ejecutar el flujo **Publicar TP3 en GitHub Pages**
   (o hacer un push a `main` que modifique `tp3-maquetacion/`).
4. Cuando termine, la URL aparece en el resumen del flujo y en **Settings → Pages**.

El flujo `.github/workflows/pages.yml` publica únicamente la carpeta `tp3-maquetacion/`.

---

## 3. Flujo de ramas y Pull Request

El trabajo se desarrolló en la rama `feature/maquetacion`:

```bash
git checkout -b feature/maquetacion
# ... commits de estructura, vistas, formato y auditoría ...
git push -u origin feature/maquetacion
```

Para abrir el Pull Request:

<https://github.com/MarcosNpm/TechFix/compare/main...feature/maquetacion?expand=1>

Luego:

1. Revisar los cambios y la vista previa.
2. **Create pull request**.
3. **Merge pull request → Confirm merge**.
4. Actualizar la rama local:

```bash
git checkout main
git pull origin main
```

Si se hace el merge desde la terminal:

```bash
git checkout main
git merge --no-ff feature/maquetacion
git push origin main
```

---

## 4. Verificación posterior

- Abrir <https://marcosnpm.github.io/TechFix/> y recorrer las diez vistas.
- Comprobar que los enlaces del menú y del pie funcionan.
- Revisar la consola del navegador: no debe haber errores 404.
- Ejecutar Lighthouse sobre la URL publicada:

```bash
npx lighthouse https://marcosnpm.github.io/TechFix/ --view
```

---

## 5. Actualizaciones

Cada push a `main` que modifique `tp3-maquetacion/` vuelve a publicar el sitio automáticamente.
También se puede ejecutar el flujo a mano desde **Actions → Publicar TP3 en GitHub Pages →
Run workflow**.
