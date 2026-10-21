<?php
function yoga_scripts() {

	wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.css');

	wp_enqueue_style( 'yoga-style', get_stylesheet_uri() );

	wp_enqueue_style('yoga-default', get_template_directory_uri() . '/css/colors/default.css');
	
	wp_enqueue_style('smartmenus',get_template_directory_uri().'/css/jquery.smartmenus.bootstrap.css');	

	wp_enqueue_style('font-awesome',get_template_directory_uri().'/css/font-awesome.css');
	
	wp_enqueue_style('carousel',get_template_directory_uri().'/css/owl.carousel.css');

	wp_enqueue_style('owl_transitions',get_template_directory_uri().'/css/owl.transitions.css');

	/* Js script */

	wp_enqueue_script( 'yoga-navigation', get_template_directory_uri() . '/js/navigation.js', array('jquery'));

	wp_enqueue_script('bootstrap-script', get_template_directory_uri() . '/js/bootstrap.js', array('jquery'));

	wp_enqueue_script('smartmenus', get_template_directory_uri() . '/js/jquery.smartmenus.js' , array('jquery'));
	
	wp_enqueue_script('yoga_slider', get_template_directory_uri() . '/js/slider.js' , array('jquery'));

	wp_enqueue_script('smartmenus-bootstrap', get_template_directory_uri() . '/js/jquery.smartmenus.bootstrap.js' , array('jquery'));
	
	wp_enqueue_script('owl-carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'));
	
	wp_register_script('yoga_custom', get_template_directory_uri() . '/js/custom.js','','1.1', true);
	wp_enqueue_script('yoga_custom');

  	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action('wp_enqueue_scripts', 'yoga_scripts');

/**
 	* Added skip link focus
 	*/
	function yoga_skip_link_focus_fix() {
	?>
	<script>
	/(trident|msie)/i.test(navigator.userAgent)&&document.getElementById&&window.addEventListener&&window.addEventListener("hashchange",function(){var t,e=location.hash.substring(1);/^[A-z0-9_-]+$/.test(e)&&(t=document.getElementById(e))&&(/^(?:a|select|input|button|textarea)$/i.test(t.tagName)||(t.tabIndex=-1),t.focus())},!1);
	</script>
	<?php
	}
	add_action( 'wp_print_footer_scripts', 'yoga_skip_link_focus_fix' );


	function yoga_customizer_selective_preview() {
	wp_enqueue_script(
		'yoga-customizer-preview', get_template_directory_uri() . '/js/customizer.js', array(
			'jquery',
			'customize-preview',
		), 999, true
	);
}

add_action( 'customize_preview_init', 'yoga_customizer_selective_preview' );
?>