<?php
function yoga_header_setting( $wp_customize ) {
$wp_customize->remove_control('header_textcolor');

	/* Header Section */
	$wp_customize->add_panel( 'header_options', array(
		'priority' => 300,
		'capability' => 'edit_theme_options',
		'title' => __('Header Settings', 'yoga'),
	) );

	$wp_customize->add_section( 'header_contact' , array(
		'title' => __('Header Top Bar Setting', 'yoga'),
		'panel' => 'header_options',
   	) );
	
	$wp_customize->add_setting(
		'yoga_topbar_enable', array(
        'default'        => 'true',
        'capability'     => 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'yoga_topbar_enable', array(
        'label'   => __('Hide / Show Section', 'yoga'),
        'section' => 'header_contact',
        'type'    => 'radio',
        'choices'=>array('true'=>'On','false'=>'Off'),
    ) );

    $wp_customize->add_setting(
		'yoga_head_info_one', array(
        'capability' => 'edit_theme_options',
		'default'=> '<a><i class="fa fa-clock-o"></i>Open-Hours:10 am to 7pm</a>',
		'sanitize_callback' => 'yoga_sanitize_textarea_content',
    ) );
    $wp_customize->add_control( 'yoga_head_info_one', array(
        'label' => __('Info one', 'yoga'),
        'section' => 'header_contact',
        'type' => 'textarea',
    ) );
	
	
	$wp_customize->add_setting(
		'yoga_head_info_two', array(
        'capability' => 'edit_theme_options',
		'default'=> '<a href="mailto:info@themeansar.com" title="Mail Me"><i class="fa fa-envelope"></i> info@themeansar.com</a>',
		'sanitize_callback' => 'yoga_sanitize_textarea_content',
    ) );
    $wp_customize->add_control( 'yoga_head_info_two', array(
        'label' => __('Info two', 'yoga'),
        'section' => 'header_contact',
        'type' => 'textarea',
    ) );

	
	function yoga_header_info_sanitize_text( $input ) {

    return wp_kses_post( force_balance_tags( $input ) );

    }
	
	if ( ! function_exists( 'yoga_sanitize_textarea_content' ) ) :

	/**
	 * Sanitize textarea content.
	 *
	 * @since 1.0.0
	 *
	 * @param string               $input Content to be sanitized.
	 * @param WP_Customize_Setting $setting WP_Customize_Setting instance.
	 * @return string Sanitized content.
	 */
	function yoga_sanitize_textarea_content( $input, $setting ) {

		return ( stripslashes( wp_filter_post_kses( addslashes( $input ) ) ) );

	}
endif;
	
	function yoga_header_sanitize_checkbox( $input ) {
			// Boolean check 
	return ( ( isset( $input ) && true == $input ) ? true : false );
	
	}
	}
	add_action( 'customize_register', 'yoga_header_setting' );
	?>