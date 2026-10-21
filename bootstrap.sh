#!/usr/bin/env bash
#
# bootstrap.sh — Monta el entorno completo de la previsualización.
#
#   1. Descarga un PHP estático (no hace falta root ni gestor de paquetes)
#   2. Descarga WordPress y el plugin de integración con SQLite
#   3. Genera wp-config.php con URL de sitio dinámica
#   4. Enlaza el tema y activa la capa visual (mu-plugins)
#   5. Instala WordPress y siembra el contenido de demostración
#
# Uso:  ./bootstrap.sh [directorio-destino]
#
set -euo pipefail

BASE="${1:-$HOME/yoga-preview}"
PORT="${PORT:-8080}"
PHP_VERSION="8.4.23"

# Las rutas absolutas del repo actual, para el tema y los mu-plugins
REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

WP="$BASE/wordpress"
PHP_BIN="$BASE/php"

say() { printf '\n\033[1;35m▸ %s\033[0m\n' "$*"; }
die() { printf '\n\033[1;31m✗ %s\033[0m\n' "$*" >&2; exit 1; }

mkdir -p "$BASE"
cd "$BASE"

# ---------------------------------------------------------------- PHP ----
if [ ! -x "$PHP_BIN" ]; then
	say "Descargando PHP estático $PHP_VERSION"
	curl -fsSL --max-time 300 -o php.tar.gz \
		"https://dl.static-php.dev/static-php-cli/common/php-${PHP_VERSION}-cli-linux-x86_64.tar.gz" \
		|| die "No se pudo descargar PHP"
	tar xzf php.tar.gz
	rm -f php.tar.gz
fi
chmod +x "$PHP_BIN"
"$PHP_BIN" -v | head -1

# El lector de traducciones de WordPress es PHP puro, así que no hace
# falta la extensión gettext (que el build estático no incluye).
"$PHP_BIN" -m | grep -qi gettext \
	|| echo "  (sin gettext: no pasa nada, WordPress lee los .mo en PHP puro)"

# --------------------------------------------------------- WordPress ----
if [ ! -f "$WP/wp-settings.php" ]; then
	say "Descargando WordPress"
	curl -fsSL --max-time 300 -o wordpress.tar.gz https://wordpress.org/latest.tar.gz \
		|| die "No se pudo descargar WordPress"
	tar xzf wordpress.tar.gz
	rm -f wordpress.tar.gz
fi

say "Instalando el plugin de SQLite"
if [ ! -d "$WP/wp-content/plugins/sqlite-database-integration" ]; then
	curl -fsSL --max-time 180 -o sqlite.zip \
		https://downloads.wordpress.org/plugin/sqlite-database-integration.zip \
		|| die "No se pudo descargar el plugin de SQLite"
	unzip -q sqlite.zip -d "$WP/wp-content/plugins/"
	rm -f sqlite.zip
fi
cp "$WP/wp-content/plugins/sqlite-database-integration/db.copy" "$WP/wp-content/db.php"

# ------------------------------------------------------- mu-plugins ----
say "Activando la capa visual"
mkdir -p "$WP/wp-content/mu-plugins"
cp -r "$REPO_ROOT/wordpress/mu-plugins/." "$WP/wp-content/mu-plugins/"
cp "$REPO_ROOT/wordpress/router.php" "$WP/router.php"

# ---------------------------------------------------------- wp-config ----
if [ ! -f "$WP/wp-config.php" ]; then
	say "Generando wp-config.php"
	SALTS="$( "$PHP_BIN" -r '
		$keys=["AUTH_KEY","SECURE_AUTH_KEY","LOGGED_IN_KEY","NONCE_KEY",
		       "AUTH_SALT","SECURE_AUTH_SALT","LOGGED_IN_SALT","NONCE_SALT"];
		$chars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_[]{}<>~`+=,.;:/?|";
		foreach($keys as $k){
			$s="";
			for($i=0;$i<64;$i++){ $s.=$chars[random_int(0,strlen($chars)-1)]; }
			# var_export() produce un literal PHP de una sola comilla: los
			# caracteres $ y \ quedan literales y no se interpolan.
			echo "define( \"$k\", " . var_export($s, true) . " );\n";
		}' )"

	cat > "$WP/wp-config.php" <<PHPEOF
<?php
/** Configuración de la previsualización (SQLite, URL de sitio dinámica). */

define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', 'wordpress' );
define( 'DB_PASSWORD', 'wordpress' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$SALTS

\$table_prefix = 'wp_';

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', false );

/*
 * La URL del sitio se deduce de la petición para que la previsualización
 * funcione tanto en localhost como en la IP de la red. El host se valida
 * contra loopback y rangos RFC1918 para que una cabecera Host falsificada
 * no pueda inyectar una URL arbitraria.
 */
\$yoga_site_host = 'localhost:$PORT';
if ( ! empty( \$_SERVER['HTTP_HOST'] ) ) {
	\$yoga_request_host = strtolower( trim( \$_SERVER['HTTP_HOST'] ) );
	\$yoga_host_only    = preg_replace( '/:\d+\$/', '', \$yoga_request_host );
	\$yoga_is_local     = in_array( \$yoga_host_only, array( 'localhost', '127.0.0.1', '::1' ), true )
		|| preg_match( '/^10\.\d{1,3}\.\d{1,3}\.\d{1,3}\$/', \$yoga_host_only )
		|| preg_match( '/^192\.168\.\d{1,3}\.\d{1,3}\$/', \$yoga_host_only )
		|| preg_match( '/^172\.(1[6-9]|2\d|3[01])\.\d{1,3}\.\d{1,3}\$/', \$yoga_host_only );
	if ( \$yoga_is_local ) {
		\$yoga_site_host = \$yoga_request_host;
	}
}
\$yoga_site_scheme = ( ! empty( \$_SERVER['HTTPS'] ) && 'off' !== \$_SERVER['HTTPS'] ) ? 'https' : 'http';

define( 'WP_HOME', \$yoga_site_scheme . '://' . \$yoga_site_host );
define( 'WP_SITEURL', \$yoga_site_scheme . '://' . \$yoga_site_host );

define( 'FS_METHOD', 'direct' );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
PHPEOF
fi

# ------------------------------------------------------------- Tema ----
say "Enlazando el tema"
ln -sfn "$REPO_ROOT/theme" "$WP/wp-content/themes/yoga"

# ------------------------------------------------------ WordPress CLI ----
if [ ! -f "$BASE/wp-cli.phar" ]; then
	say "Descargando WP-CLI"
	curl -fsSL --max-time 180 -o wp-cli.phar \
		https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
		|| die "No se pudo descargar WP-CLI"
fi

say "Instalando WordPress"
"$PHP_BIN" "$BASE/wp-cli.phar" --path="$WP" core install \
	--url="http://localhost:$PORT" \
	--title="Serenity Yoga Studio" \
	--admin_user=admin --admin_password=admin --admin_email=admin@example.com \
	--skip-email 2>/dev/null || echo "  (ya estaba instalado)"

say "Activando el tema y el idioma"
"$PHP_BIN" "$BASE/wp-cli.phar" --path="$WP" theme activate yoga || true
"$PHP_BIN" "$BASE/wp-cli.phar" --path="$WP" language core install es_ES --activate || true

say "Sembrando el contenido de demostración"
cp "$REPO_ROOT/scripts/setup-demo.php" "$BASE/setup-demo.php"
YOGO_DEMO_IMAGES="$REPO_ROOT/demo-images/" \
	"$PHP_BIN" "$BASE/wp-cli.phar" --path="$WP" eval-file "$BASE/setup-demo.php" | tail -4

cat > "$BASE/serve.sh" <<SHEOF
#!/usr/bin/env bash
set -euo pipefail
BASE="$BASE"
WP="\$BASE/wordpress"
PHP_BIN="\$BASE/php"
PIDFILE="\$BASE/server.pid"
PORT="$PORT"

stop() {
	if [ -f "\$PIDFILE" ]; then
		pid="\$(cat "\$PIDFILE")"
		kill "\$pid" 2>/dev/null || true
		sleep 1
		rm -f "\$PIDFILE"
	fi
}

case "\${1:-start}" in
	start|restart)
		stop
		cd "\$WP"
		nohup "\$PHP_BIN" -S "0.0.0.0:\$PORT" -t . router.php > "\$BASE/server.log" 2>&1 &
		echo \$! > "\$PIDFILE"
		sleep 2
		echo "→ http://localhost:\$PORT/  (admin / admin)"
		;;
	stop)
		stop
		echo "parado"
		;;
	*)
		echo "uso: \$0 {start|stop|restart}" >&2
		exit 1
		;;
esac
SHEOF
chmod +x "$BASE/serve.sh"

say "Listo"
cat <<EOF

  Sitio   : http://localhost:$PORT/
  Panel   : http://localhost:$PORT/wp-admin/  (admin / admin)
  Instalación: $BASE/wordpress
  Tema enlazado desde: $REPO_ROOT/theme

  Arrancar/parar:  $BASE/serve.sh {start|stop|restart}

EOF
