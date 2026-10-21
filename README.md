# Yoga — Tema de WordPress con previsualización en español

Previsualización funcional del tema **Yoga** (Themeansar, v1.3.7) con el sitio
traducido al español y un rediseño en tema oscuro, montada sobre un entorno
portable que **no necesita root, ni MySQL, ni gestores de paquetes**.

```
./bootstrap.sh
```

Y listo: <http://localhost:8080/> (panel: `admin` / `admin`).

---

## Qué hay aquí

| Ruta | Contenido |
|---|---|
| `theme/` | El tema Yoga, con la traducción `es_ES` añadida en `languages/` |
| `wordpress/mu-plugins/` | Capa de diseño oscuro y ajustes de localización |
| `wordpress/router.php` | Router para el servidor de PHP (permalinks amigables) |
| `scripts/` | Scripts de montaje, siembra de contenido y descarga de imágenes |
| `demo-images/` | Las 13 fotos de la demo (Pexels / Wikimedia Commons) |
| `docs/PREVIEW.md` | Documentación detallada del entorno de previsualización |

## El entorno, en dos frases

WordPress se monta sobre **SQLite** (el plugin oficial `sqlite-database-integration`)
en lugar de MySQL, y se sirve con el **servidor embutido de PHP** usando un
**binario estático de PHP** descargado a medida, de modo que no hace falta
instalar nada en el sistema.

> El build estático no incluye la extensión `gettext`, pero no hace falta:
> WordPress lee los `.mo` con PHP puro (`wp-includes/pomo/mo.php`).

## Requisitos

Sólo `bash`, `curl`, `tar`, `unzip` y `python3` (para los scripts de imágenes).

## Puesta en marcha

```sh
./bootstrap.sh                    # entorno en ~/yoga-preview
./bootstrap.sh /ruta/alternativa  # o donde quieras
```

`bootstrap.sh` descarga el PHP estático, WordPress y el plugin de SQLite,
genera un `wp-config.php` con la URL del sitio deducida de la petición,
enlaza el tema, activa el español y siembra el contenido de demostración.

Después:

```sh
~/yoga-preview/serve.sh start     # arrancar  (stop | restart)
```

### ¿Por qué la URL del sitio es dinámica?

Porque si `WP_HOME` fuera fijo (`http://localhost:8080`), al abrir la
previsualización desde otro equipo de la red WordPress redirigiría a
`localhost` —que es *tu* equipo— y todos los CSS, JS e imágenes quedarían
rotos. La URL se deduce de la petición y el host se valida contra loopback
y rangos RFC1918, de modo que una cabecera `Host` falsificada no puede
inyectar una URL arbitraria.

## El diseño

El tema original es un WordPress de 2018 con superficies blancas planas. La
capa `wordpress/mu-plugins/yoga-assets/` lo lleva a un tema oscuro:

- Base `#08060f` con auroras saturadas fijas (morado, rosa, cian, índigo, ámbar)
  y grano fino al 7 %.
- Tarjetas de cristal (`backdrop-filter: blur + saturate`) con borde translúcido.
- Botones con degradado morado→rosa y resplandor teñido.
- Títulos de sección con texto en degradado (`background-clip: text`).
- Borde degradado animado en las tarjetas de servicio al pasar el ratón.
- Navegación de cristal que queda fija al hacer scroll.

### Errores del tema que corrige la capa CSS

El tema arrastra varios selectores que **no coinciden con nada** en su HTML
y otras reglas de Bootstrap que pisan su propio estilo:

| Síntoma | Causa |
|---|---|
| Huecos laterales en el hero | El tema no reinicia `<figure>` (margen por defecto del navegador). |
| Recuadro gris en el menú activo | Bootstrap: `.navbar-default .navbar-nav > .active > a { background-color:#e7e7e7 }`. |
| Navegación con fondo blanco | Bootstrap aplica `.navbar-default { background-color:#f8f8f8 }` al `<nav>` interior. |
| Icono de servicio separado de la imagen | El widget imprime el icono antes que la imagen; se resuelve con flex `order`. |
| Pie de página ilegible | `.yoga-widget` se comparte entre barra lateral y pie; ahora se limita a `#secondary`. |
| Recorte raro del bloque destacado | El tema usa `background: ... fixed 0 0 /cover`; se fuerza `background-attachment: scroll`. |
| Reglas de navegación sin efecto | La clase `.yoga-trhead` **no existe en el HTML** (la real es `.yoga-main-nav`). |
| Paginación con cajas blancas | La paginación real es `.navigation.pagination .nav-links .page-numbers`. |
| Cabecera interna con velo gris | El tema pone `rgba(225,225,225,.5)` sobre la imagen del título. |

## La traducción

- **Núcleo de WordPress**: paquete `es_ES` instalado y activo.
- **Tema**: `theme/languages/es_ES.po` y `es_ES.mo` (73 cadenas, compiladas con
  `msgfmt`). El `.po` está listo para editar y recompilar:

  ```sh
  msgfmt --check --statistics -o theme/languages/es_ES.mo theme/languages/es_ES.po
  ```

- **Contenido**: páginas, entradas, widgets, menús y textos del customizer en
  español.
- **Fechas**: el tema imprimía `Oct 16, 2026`; ahora `16 de oct 2026`.

## Los scripts

| Script | Para qué sirve |
|---|---|
| `scripts/setup-demo.php` | Siembra contenido, widgets, menús y ajustes (`wp eval-file`). |
| `scripts/serve.sh` | Arranca / para el servidor. |
| `scripts/scrape_pexels.py` | Extrae URLs de candidatos de Pexels para las fotos. |
| `scripts/fetch_images.py` | Alternativa con Wikimedia Commons. |
| `scripts/build_sheets.py` | Monta hojas de contactos para elegir fotos. |

## Licencia

El tema Yoga es obra de [Themeansar](https://themeansar.com) y se distribuye
bajo GPLv3. Las fotos de `demo-images/` proceden de Pexels y Wikimedia
Commons y son de uso libre.
