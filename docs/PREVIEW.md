# Yoga — WordPress Theme Preview (ES)

This is a **WordPress theme** (Themeansar "Yoga", v1.3.7), not a standalone app.
It runs as a live WordPress site with Spanish demo content and a visual refresh.

## URL

    http://localhost:8080/          (from this machine)
    http://192.168.100.19:8080/     (from another device on the same network)

Admin: `http://localhost:8080/wp-admin/` — user `admin`, password `admin`

> Use the LAN address from other devices. The site URL is derived from the
> request host, so both work; the `localhost` form redirects if you use it
> from a different machine.

## Cómo se ejecuta

| Pieza | Detalle |
|---|---|
| PHP | Binario estático, sin root: `/tmp/opencode/wpyoga/php` (PHP 8.4.23) |
| Base de datos | SQLite mediante el plugin `sqlite-database-integration` (sin MySQL) |
| Servidor | Servidor embutido de PHP + `wordpress/router.php` (permalink amigable) |
| WordPress | `/tmp/opencode/wpyoga/wordpress` (última versión) |
| Tema | Enlazado por símbolo desde la carpeta original, así que se ven los cambios en vivo |

```
/tmp/opencode/wpyoga
├── php                 binario estático de PHP 8.4
├── serve.sh            iniciar / parar / reiniciar el servidor
├── setup-demo.php      siembra el contenido de demostración (wp eval-file)
├── demo-images/        imágenes importadas en la biblioteca multimedia
└── wordpress/          instalación de WordPress
```

## Comandos

```sh
/tmp/opencode/wpyoga/serve.sh restart   # reiniciar el servidor
/tmp/opencode/wpyoga/serve.sh stop      # parar

cd /tmp/opencode/wpyoga/wordpress
../php ../wp-cli.phar theme list                      # inspeccionar
../php ../wp-cli.phar eval-file ../setup-demo.php    # re-sembrar el contenido
../php ../wp-cli.phar language core list              # idiomas
```

## Traducción al español

- **Núcleo de WordPress**: paquete `es_ES` instalado y activo (`WPLANG=es_ES`).
  El panel de administración y los textos del núcleo salen traducidos.
- **Tema**: `languages/es_ES.po` + `languages/es_ES.mo` en la carpeta del tema
  (73 cadenas traducidas, compiladas con `msgfmt`). Es el único archivo añadido
  a la carpeta original del tema; su código no se ha modificado.
- **Contenido**: páginas, entradas, widgets, menús y textos del customizer
  están en español (`setup-demo.php`).
- **Fechas**: el tema imprimía `Oct 16, 2026`; ahora `16 de oct 2026` mediante
  el filtro `get_the_date` en el mu-plugin.

> La extensión `gettext` no viene en el build estático de PHP, pero WordPress
> lee los `.mo` con PHP puro (`wp-includes/pomo/mo.php`), así que las
> traducciones funcionan sin ella.

## Mejoras visuales

Capa propia en `wp-content/mu-plugins/yoga-assets/` (`yoga-visual.css` +
`yoga-visual.js`), cargada **después** de todas las hojas del tema:

- Cabecera pegajosa con sombra al hacer scroll y subrayado animado en el menú.
- Tarjetas de servicio con imagen a sangre, icono circular superpuesto y zoom al pasar el ratón.
- Tarjetas de blog con imagen ampliable, insignia de fecha y elevación.
- Tipografía, espaciado, botones redondeados con degradado y foco visible.
- Formularios, buscador, paginación, widgets de la barra lateral y pie de página.
- Animación de entrada al hacer scroll, botón "volver arriba" y preloader
  (que se oculta siempre por CSS, aunque el JavaScript falle).
- Efecto Ken Burns en el deslizador, degradado en el hero.
- Adaptación a móvil, `prefers-reduced-motion` y estilos de impresión.

### Correcciones del tema que se corrigen por CSS

| Problema | Causa |
|---|---|
| Huecos laterales en el hero | El tema no reinicia `<figure>` (margen por defecto del navegador). |
| Recuadro gris en el menú activo | Bootstrap: `.navbar-default .navbar-nav > .active > a { background-color:#e7e7e7 }`. |
| Icono de servicio separado de la imagen | El widget imprime el icono antes que la imagen; se resuelve con flex `order`. |
| Pie de página ilegible | `.yoga-widget` es compartido entre barra lateral y pie; ahora se limita a `#secondary`. |
| Recorte raro del bloque destacado | El tema usa `background: ... fixed 0 0 /cover`; se fuerza `background-attachment: scroll`. |
| Reglas de navegación sin efecto | La clase `.yoga-trhead` que usa el tema en sus CSS **no existe en el HTML** (la real es `.yoga-main-nav`): sus reglas están muertas. |

## Diseño visual: tema oscuro

El tema original es un WordPress de 2018 con superficies blancas planas. La
capa `wp-content/mu-plugins/yoga-assets/` lo lleva a un tema oscuro con
acentos saturados.

### Sistema de color

| Token | Valor | Uso |
|---|---|---|
| `--yoga-bg` | `#08060f` | Base casi negra |
| `--yoga-primary` | `#a855f7` | Morado de marca, más brillante sobre oscuro |
| `--yoga-accent` | `#fbbf24` | Ámbar |
| `--yoga-cyan` | `#22d3ee` | Cian, para acentos secundarios |
| `--yoga-pink` | `#f472b6` | Rosa, en degradados |
| `--yoga-ink` | `#f6f3ff` | Titulares |
| `--yoga-body` | `#b3abc9` | Texto corrente |
| `--yoga-glass` | `rgba(255,255,255,.045)` | Fondo de tarjetas |

### Capas de fondo

1. **Auroras** (`body::before`, fijo): cinco degradados radiales saturados
   (morado, rosa, cian, índigo, ámbar) sobre negro profundo.
2. **Grano** (`body::after`, fijo): textura `feTurbulence` al 7 %.
3. **Tintes por sección**: halo morado en servicios, halo cian en el blog.

### Elementos

- Tarjetas de cristal (`backdrop-filter: blur + saturate`) con borde translúcido.
- Borde degradado animado en las tarjetas de servicio al pasar el ratón.
- Botones con degradado morado→rosa y resplandor (`box-shadow` teñido).
- Títulos de sección con texto en degradado (`background-clip: text`).
- Insignias de icono con degradado y resplandor neón.
- Navegación de cristal con desenfoque; al hacer scroll se queda fija.
- Barra de desplazamiento con degradado y píldoras redondeadas.
- Formularios, buscador, paginación y área de comentarios en cristal.

### Errores del tema corregidos por CSS

| Problema | Causa |
|---|---|
| Huecos laterales en el hero | El tema no reinicia `<figure>` (margen por defecto del navegador). |
| Recuadro gris en el menú activo | Bootstrap: `.navbar-default .navbar-nav > .active > a { background-color:#e7e7e7 }`. |
| Icono de servicio separado de la imagen | El widget imprime el icono antes que la imagen; se resuelve con flex `order`. |
| Pie de página ilegible | `.yoga-widget` es compartido entre barra lateral y pie; ahora se limita a `#secondary`. |
| Recorte raro del bloque destacado | El tema usa `background: ... fixed 0 0 /cover`; se fuerza `background-attachment: scroll`. |
| Reglas de navegación sin efecto | La clase `.yoga-trhead` que usa el tema en sus CSS **no existe en el HTML** (la real es `.yoga-main-nav`): sus reglas están muertas. |
| Paginación con fondo blanco | La paginación real es `.navigation.pagination .nav-links .page-numbers`; los selectores `.pagination > li > a` del tema no coinciden. |
| Cabecera interna con velo gris | El tema pone `rgba(225,225,225,.5)` sobre la imagen del título. |
| Fondo blanco en la navegación | Bootstrap aplica `.navbar-default { background-color:#f8f8f8 }` al `<nav>` interior. |

### Cabecera fija

La navegación se fija al hacer scroll con `position: fixed` + un espaciador
insertado por JavaScript, de modo que el contenido no salta. La barra
superior se oculta y solo queda el menú.

> Se usa `fixed` en lugar de `sticky` porque el contenedor de la cabecera es
> corto y `sticky` no podría recorrer la página. Además `body` usa
> `overflow-x: clip` (no `hidden`), ya que `hidden` convierte el body en
> contenedor de scroll y rompería el `sticky`.

## Notas

- `?yoga_clean=1` desactiva el preloader y las animaciones (útil para capturas).
- `?yoga_stuck=1` fuerza el estado de cabecera desplazada (depuración).
- `WP_DEBUG` está activo con `WP_DEBUG_DISPLAY` desactivado; los errores van a
  `wordpress/wp-content/debug.log`.
- Las fotos de demostración vienen de Pexels y Wikimedia Commons (uso libre).
