<?php
/**
 * Plugin Name: Yoga Preview Tweaks
 * Description: Capa de mejoras visuales y ajustes de localización (español) para el tema Yoga. No modifica el tema original.
 * Version: 2.0.0
 * Author: local preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Carga la hoja de estilos y el script de mejoras visuales.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		$base = plugins_url( 'yoga-assets/', __FILE__ );

		wp_enqueue_style(
			'yoga-visual',
			$base . 'yoga-visual.css',
			// Depende de todas las hojas del tema para cargarse la última
			// y poder sobrescribir sus reglas (bootstrap, colores, etc.).
			array(
				'bootstrap',
				'yoga-style',
				'yoga-default',
				'smartmenus',
				'font-awesome',
				'carousel',
				'owl_transitions',
			),
			'3.0.0'
		);

		wp_enqueue_script(
			'yoga-visual',
			$base . 'yoga-visual.js',
			array(),
			'3.0.0',
			true
		);
	}
);

/**
 * Modo "limpio": desactiva preloader y animaciones de entrada.
 * Útil para capturas de pantalla y depuración:  ?yoga_clean=1
 */
function yoga_clean_mode() {
	return isset( $_GET['yoga_clean'] ) && '1' === (string) $_GET['yoga_clean'];
}

add_filter(
	'body_class',
	function ( $classes ) {
		if ( yoga_clean_mode() ) {
			$classes[] = 'yoga-clean';
		}
		return $classes;
	}
);

/**
 * Preloader: se imprime en el HTML inicial para evitar el parpadeo.
 */
add_action(
	'wp_body_open',
	function () {
		if ( is_admin() || yoga_clean_mode() ) {
			return;
		}
		echo '<div class="yoga-preloader" id="yoga-preloader"><div class="yoga-preloader__inner">'
			. '<div class="yoga-preloader__mark"></div>'
			. '<div class="yoga-preloader__text">Yoga</div>'
			. '</div></div>';
	}
);

/**
 * Oculta el preloader cuando la página termina de cargar.
 */
add_action(
	'wp_footer',
	function () {
		if ( is_admin() || yoga_clean_mode() ) {
			return;
		}
		?>
		<script>
		(function () {
			var el = document.getElementById('yoga-preloader');
			if (!el) { return; }
			var hide = function () {
				el.classList.add('is-done');
				window.setTimeout(function () {
					if (el.parentNode) { el.parentNode.removeChild(el); }
				}, 600);
			};
			if (document.readyState === 'complete') {
				window.setTimeout(hide, 150);
			} else {
				window.addEventListener('load', function () { window.setTimeout(hide, 150); });
			}
			// Red de seguridad: nunca dejar el preloader bloqueando la página.
			window.setTimeout(hide, 1300);
		})();
		</script>
		<?php
	}
);

/**
 * Fechas en convención española.
 *
 * El tema imprime get_the_date('M') . ' ' . get_the_date('j,') . ' ' . get_the_date('Y'),
 * es decir "Oct 16, 2026". Se reordena a "16 de oct 2026".
 */
add_filter( 'get_the_date', 'yoga_es_date_format', 10, 3 );
function yoga_es_date_format( $the_date, $format, $post ) {
	if ( ! $post instanceof WP_Post ) {
		return $the_date;
	}

	$ts = get_post_timestamp( $post );

	switch ( $format ) {
		case 'M':
			$abbrev = wp_date( 'M', $ts );
			return wp_date( 'j', $ts ) . ' de ' . mb_strtolower( $abbrev );
		case 'j,':
			return '';
		case 'Y':
			return wp_date( 'Y', $ts );
	}

	return $the_date;
}

/**
 * Etiquetas más claras en el formulario de comentarios y en el buscador.
 */
add_filter(
	'comment_form_defaults',
	function ( $defaults ) {
		$defaults['title_reply']       = 'Deja un comentario';
		$defaults['title_reply_to']    = 'Responder a %s';
		$defaults['label_submit']      = 'Publicar comentario';
		$defaults['comment_notes_before'] = 'Tu dirección de correo no se publicará. Los campos obligatorios están marcados con *.';

		return $defaults;
	}
);

add_filter(
	'nav_menu_css_class',
	function ( $classes, $item ) {
		if ( in_array( 'current-menu-item', $classes, true ) ) {
			$classes[] = 'yoga-current';
		}
		return $classes;
	},
	10,
	2
);
