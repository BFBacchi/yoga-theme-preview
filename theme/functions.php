<?php
/**
 * yoga functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package yoga
 */

	$yoga_theme_path = get_template_directory() . '/inc/ansar/';

	require( $yoga_theme_path . '/yoga-custom-navwalker.php' );
	require( $yoga_theme_path . '/icon-functions.php');
	require( $yoga_theme_path . '/font/font.php');
	require( $yoga_theme_path . '/widget/yoga-slider.php');
	require( $yoga_theme_path . '/widget/yoga-service.php');

	/*-----------------------------------------------------------------------------------*/
	/*	Enqueue scripts and styles.
	/*-----------------------------------------------------------------------------------*/
	require( $yoga_theme_path .'/enqueue.php');
	/* ----------------------------------------------------------------------------------- */
	/* Customizer */
	/* ----------------------------------------------------------------------------------- */
	
	require( $yoga_theme_path . '/customize/yoga_customize_copyright.php');
	require( $yoga_theme_path  . '/customize/yoga_customize_header.php');
	require( $yoga_theme_path . '/customize/yoga_customize_homepage.php');
	require( $yoga_theme_path . '/customize/class-alpha-color-control/class-alpha-color-control.php');
	
	/*
	 * Load customize pro
	*/
	require_once( trailingslashit( get_template_directory() ) . 'inc/ansar/customize/customize-pro/class-customize.php' );
	require_once( trailingslashit( get_template_directory() ) . 'inc/ansar/customize/yoga_customize_partials.php' );

	

	

if ( ! function_exists( 'yoga_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function yoga_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on yoga, use a find and replace
	 * to change 'yoga' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'yoga', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	//Added selecctive refresh for widget
	add_theme_support( 'customize-selective-refresh-widgets' );


	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary menu', 'yoga' ),
        'social' => __( 'Social Links Menu', 'yoga' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'yoga_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

    // Set up the woocommerce feature.
    add_theme_support( 'woocommerce');
	
	//Custom logo
	
	add_theme_support( 'custom-logo', array(
			'header-text' => array( 'site-title', 'site-description' ),
			
		) );
	
	// custom header Support
			$args = array(
			'default-image'		=>  get_template_directory_uri() .'/images/sub-header.jpg',
			'width'			=> '1600',
			'height'		=> '600',
			'flex-height'		=> false,
			'flex-width'		=> false,
			'header-text'		=> true,
			'default-text-color'	=> '#143745'
		);
		add_theme_support( 'custom-header', $args );
	

}
endif;
add_action( 'after_setup_theme', 'yoga_setup' );


	function yoga_the_custom_logo() {
	
		if ( function_exists( 'the_custom_logo' ) ) {
			the_custom_logo();
		}

	}

	add_filter('get_custom_logo','yoga_logo_class');


	function yoga_logo_class($html)
	{
	$html = str_replace('custom-logo-link', 'navbar-brand', $html);
	return $html;
	}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function yoga_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'yoga_content_width', 640 );
}
add_action( 'after_setup_theme', 'yoga_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function yoga_widgets_init() {
	
	$yoga_footer_column_layout = get_theme_mod('yoga_footer_column_layout',3);
	
	$yoga_footer_column_layout = 12 / $yoga_footer_column_layout;
	
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Widget Area', 'yoga' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="widget %2$s yoga-widget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h6>',
		'after_title'   => '</h6>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget Area', 'yoga' ),
		'id'            => 'footer_widget_area',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="col-md-'.$yoga_footer_column_layout.' col-sm-6 yoga-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h6>',
		'after_title'   => '</h6>',
	) );
	
	 register_sidebar(
            array (
                'name'          =>  esc_html__( 'Slider Widgets Area', 'yoga' ),
                'id'            => 'sidebar-slider',
                'before_widget' => '<div id="%1$s" class="yoga-widget %2$s">',
                'after_widget'  => '</div>'
            )
        );
	
	register_sidebar( array(
		'name'          => esc_html__( 'Service Widget Area', 'yoga' ),
		'id'            => 'servicehome_widget_area',
		'description'   => '',
		'before_widget' => '<div class="item">',
		'after_widget'  => '</div>',
	) );

}
add_action( 'widgets_init', 'yoga_widgets_init' );

/*-----------------------------------------------------------------------------------*/
/*  customizer-controls
/*-----------------------------------------------------------------------------------*/
/* Custom template tags for this theme. */
require get_template_directory() . '/inc/template-tags.php';


function yoga_register_widgets() {    

    register_widget('yoga_slider_widget');
    register_widget('yoga_service_widget');
	
}
add_action('widgets_init', 'yoga_register_widgets');


//Read more Button on slider & Post
function yoga_read_more() {
	
	global $post;
	
	$readbtnurl = '<a class="btn btn-tislider blog-readmore" href="' . get_permalink() . '">'.__('Read More' , 'yoga' ).'</a>';
	
    return $readbtnurl;
}
add_filter( 'the_content_more_link', 'yoga_read_more' );


function yoga_excerpt_more_button( $more ) {
	return '<br><a class="btn btn-tislider blog-readmore" href="' . get_permalink() . '">'.__('Read More','yoga').'</a>';
}
add_filter( 'excerpt_more', 'yoga_excerpt_more_button', 20 );

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Fire the wp_body_open action.
	 *
	 * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
	 *
	 * @since Twenty Nineteen 1.4
	 */
	function wp_body_open() {
		/**
		 * Triggered after the opening <body> tag.
		 *
		 * @since Twenty Nineteen 1.4
		 */
		do_action( 'wp_body_open' );
	}
endif;
?>