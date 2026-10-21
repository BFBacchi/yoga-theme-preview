<?php
/**
 * Seeds the yoga WordPress theme with Spanish demo content.
 * Run with: wp eval-file setup-demo.php
 *
 * Idempotent: re-running updates existing pages, posts, widgets and menus.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'run via wp eval-file' );
}

$img_dir = getenv( 'YOGO_DEMO_IMAGES' );
if ( ! $img_dir ) {
	$img_candidates = array(
		dirname( __DIR__ ) . '/demo-images/',   // dentro del repo: scripts/ → ../demo-images/
		'/tmp/opencode/wpyoga/demo-images/',
	);
	foreach ( $img_candidates as $candidate ) {
		if ( is_dir( $candidate ) ) {
			$img_dir = $candidate;
			break;
		}
	}
}
if ( ! $img_dir ) {
	$img_dir = '/tmp/opencode/wpyoga/demo-images/';
}
$img_dir = rtrim( $img_dir, '/' ) . '/';

/* ------------------------------------------------------------------ *
 * Helper: import a local image into the media library
 * ------------------------------------------------------------------ */
function yoga_demo_sideload( $path ) {
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( basename( $path ) );
	copy( $path, $tmp );

	$id = media_handle_sideload(
		array(
			'name'     => basename( $path ),
			'tmp_name' => $tmp,
		),
		0
	);

	if ( is_wp_error( $id ) ) {
		if ( file_exists( $tmp ) ) {
			unlink( $tmp );
		}
		return 0;
	}
	return (int) $id;
}

/* ------------------------------------------------------------------ *
 * 1. Import images
 * ------------------------------------------------------------------ */
$old_attachments = get_posts( array(
	'post_type'   => 'attachment',
	'numberposts' => -1,
	'post_status' => 'any',
	'fields'      => 'ids',
) );
foreach ( $old_attachments as $att_id ) {
	wp_delete_attachment( $att_id, true );
}

$image_map = array();
$import_list = array(
	'slider-1', 'slider-2', 'slider-3',
	'service-1', 'service-2', 'service-3',
	'post-1', 'post-2', 'post-3', 'post-4', 'post-5', 'post-6',
	'callout',
);

foreach ( $import_list as $slug ) {
	$path = $img_dir . $slug . '.jpg';
	if ( ! file_exists( $path ) ) {
		echo "  ! falta la imagen: {$slug}\n";
		continue;
	}
	$id = yoga_demo_sideload( $path );
	if ( $id ) {
		$url      = wp_get_attachment_url( $id );
		$relative = preg_replace( '#^' . preg_quote( home_url(), '#' ) . '#', '', $url );
		$image_map[ $slug ] = array(
			'id'  => $id,
			'url' => $relative,
		);
		echo "  + {$slug} -> adjunto {$id}\n";
	}
}
update_option( 'yoga_demo_images', $image_map );

/* ------------------------------------------------------------------ *
 * 2. Widgets: deslizador + servicios
 * ------------------------------------------------------------------ */
$slide_widgets = array(
	2 => array(
		'slider_title' => 'Encuentra tu equilibrio interior',
		'slider_desc'  => 'Practica movimiento consciente en nuestro estudio al aire libre y vuelve a descubrir una versión más tranquila y más fuerte de ti.',
		'btnone'       => 'Reserva tu clase',
		'btnonelink'   => '/clases/',
		'image_uri'    => isset( $image_map['slider-1'] ) ? $image_map['slider-1']['url'] : '',
	),
	3 => array(
		'slider_title' => 'Respira. Muévete. Medita.',
		'slider_desc'  => 'Sesiones guiadas para todos los niveles: desde tu primera salutation al sol hasta el pranayama más avanzado.',
		'btnone'       => 'Nuestras clases',
		'btnonelink'   => '/clases/',
		'image_uri'    => isset( $image_map['slider-2'] ) ? $image_map['slider-2']['url'] : '',
	),
	4 => array(
		'slider_title' => 'Cuerpo más fuerte, mente más serena',
		'slider_desc'  => 'Únete a una comunidad acogedora y construye una práctica que te acompañe toda la vida.',
		'btnone'       => 'Empieza ahora',
		'btnonelink'   => '/contacto/',
		'image_uri'    => isset( $image_map['slider-3'] ) ? $image_map['slider-3']['url'] : '',
	),
);

$service_widgets = array(
	2 => array(
		'fa_icon'       => 'fa-heart',
		'hide_image'    => 0,
		'service_title' => 'Clases de yoga',
		'service_desc'  => 'Sesiones de Hatha, Vinyasa y Yin en grupos reducidos, para que cada postura se pracique con una guía adecuada.',
		'btnmore'       => 'Ver horarios',
		'btnlink'       => '/clases/',
		'image_uri'     => isset( $image_map['service-1'] ) ? $image_map['service-1']['url'] : '',
	),
	3 => array(
		'fa_icon'       => 'fa-leaf',
		'hide_image'    => 0,
		'service_title' => 'Meditación y respiración',
		'service_desc'  => 'Trabajo guiado de respiración y meditación sentada para calmar el sistema nervioso y afinar la concentración.',
		'btnmore'       => 'Saber más',
		'btnlink'       => '/clases/',
		'image_uri'     => isset( $image_map['service-2'] ) ? $image_map['service-2']['url'] : '',
	),
	4 => array(
		'fa_icon'       => 'fa-spa',
		'hide_image'    => 0,
		'service_title' => 'Retiros de bienestar',
		'service_desc'  => 'Retiros de fin de semana que combinan yoga, talleres de nutrición, masajes y caminatas en la naturaleza.',
		'btnmore'       => 'Próximos retiros',
		'btnlink'       => '/contacto/',
		'image_uri'     => isset( $image_map['service-3'] ) ? $image_map['service-3']['url'] : '',
	),
);

update_option( 'widget_yoga_slider-widget', $slide_widgets );
update_option( 'widget_yoga_service_widget', $service_widgets );

$sidebars = get_option( 'sidebars_widgets', array() );

// The sidebars_widgets option stores widget id strings (legacy format).
$sidebars['sidebar-slider']          = array();
$sidebars['servicehome_widget_area'] = array();
$sidebars['sidebar-1']              = array();

foreach ( array_keys( $slide_widgets ) as $num ) {
	$sidebars['sidebar-slider'][] = 'yoga_slider-widget-' . $num;
}
foreach ( array_keys( $service_widgets ) as $num ) {
	$sidebars['servicehome_widget_area'][] = 'yoga_service_widget-' . $num;
}
update_option( 'sidebars_widgets', $sidebars );

/* ------------------------------------------------------------------ *
 * 3. Páginas
 * ------------------------------------------------------------------ */
$pages = array(
	'home' => array(
		'title'    => 'Inicio',
		'slug'     => 'inicio',
		'template' => 'templates/template-frontpage.php',
	),
	'classes' => array(
		'title'   => 'Clases',
		'slug'    => 'clases',
		'content' => "<!-- wp:paragraph --><p>El estudio ofrece una programación completa de yoga, movilidad y trabajo de respiración, los siete días de la semana. Todas las sesiones las dirige un instructor certificado y se ajustan al grupo, de modo que principiantes y practicantes de larga trayectoria pueden compartir la misma esterilla.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Horarios de clase</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p><strong>Lunes y miércoles — 07:00 Hatha Flow</strong><br/>Para principiantes, lento y constante.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p><strong>Martes y jueves — 18:30 Vinyasa</strong><br/>Una secuencia dinámica al ritmo de la respiración.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p><strong>Sábado — 10:00 Yin y sonido</strong><br/>Posturas largas con baño de sonido para cerrar.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p><strong>Domingo — 09:00 Clase comunitaria</strong><br/>Gratuita para estudiantes. Se aceptan donativos.</p><!-- /wp:paragraph -->",
	),
	'about' => array(
		'title'   => 'Nosotros',
		'slug'    => 'nosotros',
		'content' => "<!-- wp:paragraph --><p>Serenity Studio nació de una idea sencilla: moverse debería hacerte sentir más a gusto en tu cuerpo, no menos. Nuestros maestros se formaron en India, Indonesia y Estados Unidos, y todos siguen estudiando su propia práctica cada año.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>En qué creemos</h2><!-- /wp:heading -->\n\n<!-- wp:list --><ul><li>La práctica debe ser accesible: ofrecemos clases en esterilla, en silla y recorridos para principiantes.</li><li>Los maestros deben cobrar bien para poder seguir aprendiendo.</li><li>El estudio es una comunidad, no un mercado.</li></ul><!-- /wp:list -->\n\n<!-- wp:paragraph --><p>Ven a una sola clase y quédate después para el té. La mayoría de nuestros socios llegaron solos y se fueron con una práctica y un grupo de amigos.</p><!-- /wp:paragraph -->",
	),
	'trainers' => array(
		'title'   => 'Nuestros instructores',
		'slug'    => 'instructores',
		'content' => "<!-- wp:paragraph --><p>Nuestro equipo docente reúne más de sesenta años de práctica combinada. Todas las clases están cubiertas, así que nunca tienes que preguntarte quién dirige la sala.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>El equipo</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p><strong>Maya Rao — Hatha y Yin</strong><br/>Maya lleva más de doce años enseñando y se ha especializado en trabajar con alumnos que se recuperan de lesiones. Sus clases son tranquilas, precisas y muy prácticas.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p><strong>Daniel Okoye — Vinyasa y movilidad</strong><br/>Daniel llegó al yoga desde la danza y ahora se centra en el movimiento funcional. Esperas secuencias creativas y bastante trabajo de core.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p><strong>Lucía Ferrante — Meditación y pranayama</strong><br/>Lucía se formó en Vipassana y ofrece un enfoque amable y práctico del trabajo de respiración, ideal para empezar de cero.</p><!-- /wp:paragraph -->",
	),
	'contact' => array(
		'title'   => 'Contacto',
		'slug'    => 'contacto',
		'content' => "<!-- wp:paragraph --><p>Nos encantaría tenerte en la esterilla. Ven a ver el estudio, habla con alguno de nuestros maestros o simplemente reserva una clase y llega diez minutos antes.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Visítanos</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p><strong>Serenity Yoga Studio</strong><br/>Calle Sauce 14<br/>Barrio del Río<br/>Abierto todos los días, de 6:00 a 21:00</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p><strong>Correo</strong>: hola@serenityyoga.studio<br/><strong>Teléfono</strong>: +34 900 123 456</p><!-- /wp:paragraph -->",
	),
);

$page_ids = array();
foreach ( $pages as $key => $data ) {
	$existing = get_page_by_path( $data['slug'] );
	if ( $existing ) {
		$page_ids[ $key ] = $existing->ID;
		wp_update_post( array(
			'ID'           => $existing->ID,
			'post_title'   => $data['title'],
			'post_content' => isset( $data['content'] ) ? $data['content'] : $existing->post_content,
		) );
		continue;
	}
	$id = wp_insert_post( array(
		'post_title'   => $data['title'],
		'post_name'    => $data['slug'],
		'post_content' => isset( $data['content'] ) ? $data['content'] : '',
		'post_status'  => 'publish',
		'post_type'    => 'page',
	) );
	$page_ids[ $key ] = $id;
	if ( ! empty( $data['template'] ) ) {
		update_post_meta( $id, '_wp_page_template', $data['template'] );
	}
}

/* Página del blog (índice de entradas) */
$blog_id = get_page_by_path( 'blog' );
if ( $blog_id instanceof WP_Post ) {
	$blog_id = $blog_id->ID;
}
if ( ! $blog_id ) {
	$blog_id = wp_insert_post( array(
		'post_title'   => 'Blog',
		'post_name'    => 'blog',
		'post_content' => '<!-- wp:paragraph --><p>Notas del estudio: consejos de práctica, entrevistas con maestros y el ensayo ocasional sobre el descanso.</p><!-- /wp:paragraph -->',
		'post_status'  => 'publish',
		'post_type'    => 'page',
	) );
} else {
	wp_update_post( array(
		'ID'           => $blog_id,
		'post_title'   => 'Blog',
		'post_content' => '<!-- wp:paragraph --><p>Notas del estudio: consejos de práctica, entrevistas con maestros y el ensayo ocasional sobre el descanso.</p><!-- /wp:paragraph -->',
	) );
}
$page_ids['blog'] = (int) $blog_id;

/* ------------------------------------------------------------------ *
 * 4. Entradas
 * ------------------------------------------------------------------ */
$posts = array(
	array(
		'title'   => 'Cómo crear un hábito de yoga que de verdad se mantenga',
		'slug'    => 'como-crear-un-habito-de-yoga',
		'img'     => 'post-1',
		'cat'     => 'Práctica de yoga',
		'excerpt' => 'La clave no es la intensidad, sino la repetición. Esta es la rutina pequeña y poco vistosa que mantiene a la gente volviendo.',
		'content' => "<!-- wp:paragraph --><p>La mayoría de quienes abandonan el yoga no lo hacen porque fuera difícil. Lo abandonan porque reservaron una clase intensa de dos horas un sábado, se destrozaron durante dos días y nunca volvieron.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Empieza con menos de lo que parece útil</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Veinte minutos, tres veces por semana, bastan para asentar una base. La constancia multiplica más rápido que la intensidad, y una sesión corta es mucho más fácil de proteger en una semana ocupada que una ambiciosa.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Ancla la práctica</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Engancha tu práctica a algo que ya hagas sin falta: después del primer café, antes de mirar el correo, al salir del trabajo. Un hábito anclado no necesita motivación.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Ven a clase al menos una vez cada quince días. Un maestro en la sala te corregirá de formas que ningún vídeo puede, y entrar en la sala por segunda vez es mucho más fácil.</p><!-- /wp:paragraph -->",
	),
	array(
		'title'   => 'Qué comer antes del yoga (y qué conviene evitar)',
		'slug'    => 'que-comer-antes-del-yoga',
		'img'     => 'post-2',
		'cat'     => 'Práctica de yoga',
		'excerpt' => 'Comer demasiado y ayunar antes de clase son los dos errores más habituales. La regla es más simple de lo que parece.',
		'content' => "<!-- wp:paragraph --><p>El yoga con el estómago lleno resulta incómodo. El yoga en ayunas es peor: te deja mareado y temblando en mitad de una postura de equilibrio.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Come de dos a tres horas antes</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Algo ligero y fácil de digerir: fruta con yogur, un sándwich pequeño, avena con miel o unas dátiles con un plátano. Eso le da al cuerpo tiempo a asentarse sin dejarte con hambre.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Hidrátate, pero no justo antes</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Bebe agua durante la tarde y para una hora antes de clase. Quieres estar hidratado, no lleno.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Después, come con normalidad. Una comida completa y equilibrada en la hora o las dos siguientes es exactamente lo que tu cuerpo pide.</p><!-- /wp:paragraph -->",
	),
	array(
		'title'   => 'Yoga y running: construir una base más fuerte y estable',
		'slug'    => 'yoga-y-running',
		'img'     => 'post-3',
		'cat'     => 'Práctica de yoga',
		'excerpt' => 'El running exige equilibrio sobre un pie y una pelvis estable. El yoga te da ambas cosas, y algo más.',
		'content' => "<!-- wp:paragraph --><p>Los corredores suelen llegar al yoga con isquiotibiales tensos, glúteos débiles y caderas que rotan unos grados de más. Las tres cosas te cuestan metros.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>De dónde viene la estabilidad</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Las posturas de equilibrio como la del árbol no son trucos de equilibrio: son pruebas de estabilidad de cadera y de conciencia del pie. Cuando puedes mantener el equilibrio sobre una pierna durante un minuto completo sin oscilar, tu zancada de running será Visiblemente más controlada.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Dos sesiones por semana, centradas en trabajo de una pierna, aperturas de cadera y fuerza de gemelos, le hacen a un corredor más de lo que la mayoría espera de estirar por su cuenta.</p><!-- /wp:paragraph -->",
	),
	array(
		'title'   => 'El descanso es parte de la práctica',
		'slug'    => 'el-descanso-es-parte-de-la-practica',
		'img'     => 'post-4',
		'cat'     => 'Mindfulness',
		'excerpt' => 'El yoga restaurativo, el sauna y el masaje no son lujos añadidos a la agenda. Son lo que hace posible la práctica.',
		'content' => "<!-- wp:paragraph --><p>Persiste la idea de que el descanso hay que ganárselo con esfuerzo. En una práctica física, lo más cercano a la verdad es lo contrario: la recuperación es donde ocurre la adaptación.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Los músculos no se reconstruyen durante la sesión. Se reconstruyen a lo largo de las horas siguientes, mientras duermes, comes y te mantienes de pie. Un día de descanso no es un día perdido: es parte del entrenamiento.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Prueba a programar una sesión restaurativa cada semana, junto a la clase que más te haga sentir vivo. Nota lo diferente que se siente la semana siguiente.</p><!-- /wp:paragraph -->",
	),
	array(
		'title'   => 'Una rutina matutina que solo lleva diez minutos',
		'slug'    => 'rutina-matutina-de-diez-minutos',
		'img'     => 'post-5',
		'cat'     => 'Mindfulness',
		'excerpt' => 'Antes de las notificaciones y antes del correo. Una secuencia corta que cambia el textura del día entero.',
		'content' => "<!-- wp:paragraph --><p>El mejor momento para practicar es el momento en el que de verdad vas a hacerlo. Para la mayoría de la gente es temprano por la mañana, antes de que el día ponga sus exigencias.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Una secuencia que conviene conservar</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Gato-vaca, tres rondas de perro boca abajo, una flexión de pie, una torsión suave y dos minutos de respiración suave. Diez minutos, sin música y sin necesidad de flexibilidad.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>Lo que hace que esto funcione no es la secuencia. Es que ocurre antes que el móvil.</p><!-- /wp:paragraph -->",
	),
	array(
		'title'   => 'Aprender a respirar correctamente',
		'slug'    => 'aprender-a-respirar',
		'img'     => 'post-6',
		'cat'     => 'Mindfulness',
		'excerpt' => 'La respiración es la única parte del yoga que puedes llevarte a cualquier sitio. Así se empieza a reentrenar.',
		'content' => "<!-- wp:paragraph --><p>La mayoría de respiramos de forma superficial y alta en el pecho, sobre todo cuando estamos ocupados o ansiosos. El trabajo de respiración es la vía más rápida para cambiar eso.</p><!-- /wp:paragraph -->\n\n<!-- wp:heading --><h2>Tres minutos al día</h2><!-- /wp:heading -->\n\n<!-- wp:paragraph --><p>Siéntate cómodamente. Cierra una fosa nasal e inspira por la otra contando hasta cuatro. Cambia. Espira por la primera fosa nasal contando hasta seis. Repite durante tres minutos.</p><!-- /wp:paragraph -->\n\n<!-- wp:paragraph --><p>La exhalación larga es el ingrediente activo: es lo que te saca de un estado de estrés. Practica antes de dormir y notarás que el sueño llega con facilidad en menos de una semana.</p><!-- /wp:paragraph -->",
	),
);

foreach ( $posts as $i => $p ) {
	$date = gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( ( count( $posts ) - $i ) * 4 ) . ' days' ) );
	$existing = get_page_by_path( $p['slug'], OBJECT, 'post' );
	if ( $existing ) {
		$id = $existing->ID;
		wp_update_post( array(
			'ID'           => $id,
			'post_title'   => $p['title'],
			'post_excerpt' => $p['excerpt'],
			'post_content' => $p['content'],
			'post_date'    => $date,
		) );
	} else {
		$id = wp_insert_post( array(
			'post_title'   => $p['title'],
			'post_name'    => $p['slug'],
			'post_excerpt' => $p['excerpt'],
			'post_content' => $p['content'],
			'post_status'  => 'publish',
			'post_type'    => 'post',
			'post_date'    => $date,
		) );
	}
	if ( $id && isset( $image_map[ $p['img'] ] ) ) {
		set_post_thumbnail( $id, $image_map[ $p['img'] ]['id'] );
	}
}

/* ------------------------------------------------------------------ *
 * 4b. Categorías
 * ------------------------------------------------------------------ */
foreach ( array( 'Práctica de yoga', 'Mindfulness' ) as $cat_name ) {
	if ( ! term_exists( $cat_name, 'category' ) ) {
		wp_insert_term( $cat_name, 'category' );
	}
}
foreach ( $posts as $p ) {
	$post = get_page_by_path( $p['slug'], OBJECT, 'post' );
	$term = term_exists( $p['cat'], 'category' );
	if ( $post && $term ) {
		wp_set_post_categories( $post->ID, array( (int) $term['term_id'] ) );
	}
}

/* ------------------------------------------------------------------ *
 * 5. Menús
 * ------------------------------------------------------------------ */
$menu_name = 'Menú principal';
$menu      = wp_get_nav_menu_object( $menu_name );
if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( $menu_name );
} else {
	$menu_id = $menu->term_id;
	foreach ( wp_get_nav_menu_items( $menu_id ) as $item ) {
		wp_delete_post( $item->ID, true );
	}
}

$menu_items = array(
	array( 'title' => 'Inicio',               'page' => 'home',     'order' => 1 ),
	array( 'title' => 'Clases',               'page' => 'classes',  'order' => 2 ),
	array( 'title' => 'Nuestros instructores', 'page' => 'trainers', 'order' => 3 ),
	array( 'title' => 'Nosotros',             'page' => 'about',    'order' => 4 ),
	array( 'title' => 'Blog',                 'page' => 'blog',     'order' => 5 ),
	array( 'title' => 'Contacto',             'page' => 'contact',  'order' => 6 ),
);
foreach ( $menu_items as $mi ) {
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => $mi['title'],
		'menu-item-object'    => 'page',
		'menu-item-object-id' => $page_ids[ $mi['page'] ],
		'menu-item-type'      => 'post_type',
		'menu-item-status'    => 'publish',
		'menu-item-position'  => $mi['order'],
	) );
}

/* Menú social */
$social_name = 'Enlaces sociales';
$social      = wp_get_nav_menu_object( $social_name );
if ( ! $social ) {
	$social_id = wp_create_nav_menu( $social_name );
} else {
	$social_id = $social->term_id;
	foreach ( wp_get_nav_menu_items( $social_id ) as $item ) {
		wp_delete_post( $item->ID, true );
	}
}
$social_links = array(
	array( 'title' => 'Facebook',  'url' => 'https://facebook.com' ),
	array( 'title' => 'Twitter',   'url' => 'https://twitter.com' ),
	array( 'title' => 'Instagram', 'url' => 'https://instagram.com' ),
	array( 'title' => 'YouTube',   'url' => 'https://youtube.com' ),
);
foreach ( $social_links as $i => $sl ) {
	wp_update_nav_menu_item( $social_id, 0, array(
		'menu-item-title'    => $sl['title'],
		'menu-item-url'      => $sl['url'],
		'menu-item-type'     => 'custom',
		'menu-item-status'   => 'publish',
		'menu-item-position' => $i + 1,
	) );
}

$locations = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = $menu_id;
$locations['social']  = $social_id;
set_theme_mod( 'nav_menu_locations', $locations );

/* ------------------------------------------------------------------ *
 * 6. Portada y ajustes de lectura
 * ------------------------------------------------------------------ */
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $page_ids['home'] );
update_option( 'page_for_posts', $page_ids['blog'] );
update_option( 'posts_per_page', 3 );
update_option( 'permalink_structure', '/%postname%/' );
update_option( 'blogname', 'Serenity Yoga' );
update_option( 'blogdescription', 'Yoga, meditación y bienestar en el corazón de la ciudad.' );

// Formatos de fecha en convención española.
update_option( 'date_format', 'j F Y' );
update_option( 'time_format', 'H:i' );
update_option( 'start_of_week', 1 );

/* ------------------------------------------------------------------ *
 * 7. Ajustes del tema
 * ------------------------------------------------------------------ */
$mods = array(
	'yoga_slider_enable'           => 'true',
	'yoga_slider_overlay_color'    => 'rgba(0,0,0,0.35)',

	'yoga_service_enable'          => 'true',
	'yoga_service_title'           => 'Qué ofrecemos',
	'yoga_service_subtitle'        => 'Grupos reducidos, maestros con experiencia y un estudio al que querrás volver.',
	// Vacío: el tema lo imprimiría como style="background: ;" (inválido),
	// y el degradado de la sección lo define la hoja de mejoras.
	'yoga_service_overlay_color'   => '',
	'yoga_footer_widget_background' => '',

	'yoga_callout_enable'          => 'true',
	'yoga_callout_title'           => 'Tu primera clase es gratis',
	'yoga_callout_description'     => '¿Nuevo en el yoga? Ven a cualquier clase de Hatha para principiantes y comprueba cómo se siente. No hace falta experiencia ni material.',
	'yoga_callout_background'      => isset( $image_map['callout'] ) ? $image_map['callout']['url'] : '',
	'yoga_callout_overlay_color'   => 'rgba(20,20,20,0.45)',
	'yoga_callout_text_align'      => 'center',
	'yoga_callout_button_one_label' => 'Empieza gratis',
	'yoga_callout_button_one_link'  => '/contacto/',
	'yoga_callout_button_two_label' => 'Ver horarios',
	'yoga_callout_button_two_link'  => '/clases/',

	'yoga_news_enable'             => 'true',
	'yoga_news_title'              => 'Del blog',
	'yoga_news_subtitle'           => 'Consejos de práctica, entrevistas con maestros y noticias del estudio.',
	'news_select'                  => 3,
	'disable_news_meta'            => 'false',

	'yoga_topbar_enable'           => 'true',
	'yoga_head_info_one'           => '<a><i class="fa fa-clock-o"></i>Horario: de 6:00 a 21:00</a>',
	'yoga_head_info_two'           => '<a href="mailto:hola@serenityyoga.studio" title="Escríbenos"><i class="fa fa-envelope"></i> hola@serenityyoga.studio</a>',

	'yoga_footer_column_layout'    => 3,
	'blogname'                     => 'Serenity Yoga',
	'blogdescription'              => 'Yoga, meditación y bienestar en el corazón de la ciudad.',
);

foreach ( $mods as $key => $value ) {
	set_theme_mod( $key, $value );
}

/* ------------------------------------------------------------------ *
 * 8. Widgets del pie y de la barra lateral
 * ------------------------------------------------------------------ */
$sidebars = get_option( 'sidebars_widgets', array() );
$sidebars['footer_widget_area'] = array( 'text-2', 'text-3', 'text-4' );

update_option( 'widget_search', array(
	2 => array( 'title' => 'Buscar' ),
) );
update_option( 'widget_recent-posts', array(
	2 => array(
		'title'  => 'Entradas recientes',
		'number' => 5,
	),
) );
update_option( 'widget_categories', array(
	2 => array(
		'title'    => 'Categorías',
		'dropdown' => 0,
		'count'    => 1,
	),
) );
update_option( 'widget_archives', array(
	2 => array(
		'title'    => 'Archivo',
		'dropdown' => 0,
		'count'    => 0,
	),
) );
update_option( 'widget_text', array(
	2 => array(
		'title'  => 'Sobre Serenity',
		'text'   => 'Serenity Yoga Studio es un estudio de barrio que ofrece clases de yoga, meditación y bienestar los siete días de la semana. Todos los niveles son bienvenidos.',
		'filter' => true,
	),
	3 => array(
		'title'  => 'Enlaces rápidos',
		'text'   => "<ul><li><a href='/clases/'>Horarios de clase</a></li><li><a href='/instructores/'>Nuestros instructores</a></li><li><a href='/nosotros/'>Sobre nosotros</a></li><li><a href='/contacto/'>Contacto</a></li></ul>",
		'filter' => true,
	),
	4 => array(
		'title'  => 'Horario',
		'text'   => "<ul><li>Lunes a viernes: 6:00 – 21:00</li><li>Sábado: 8:00 – 18:00</li><li>Domingo: 8:00 – 16:00</li></ul>",
		'filter' => true,
	),
	5 => array(
		'title'  => 'Únete al estudio',
		'text'   => 'Los nuevos alumnos pueden probar cualquier clase para principiantes gratis. Solo llega diez minutos antes y menciona esta página en recepción.',
		'filter' => true,
	),
) );

$sidebars['sidebar-1'] = array( 'search-2', 'recent-posts-2', 'categories-2', 'archives-2', 'text-5' );
update_option( 'sidebars_widgets', $sidebars );

echo "\n=== Contenido de demostración en español ===\n";
echo "Portada ID: " . $page_ids['home'] . "\n";
echo "Páginas: " . implode( ', ', array_map( 'intval', $page_ids ) ) . "\n";
