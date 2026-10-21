<?php
// Footer copyright section 
function yoga_footer_copyright( $wp_customize ) {
	$wp_customize->add_panel('yoga_copyright', array(
		'priority' => 700,
		'capability' => 'edit_theme_options',
		'title' => __('Footer Settings', 'yoga'),
	) );

    $wp_customize->add_section('footer_widget_back', array(
        'title' => __('Background Setting','yoga'),
        'priority' => 30,
        'panel' => 'yoga_copyright',
    ) );
	
	
	 $wp_customize->add_setting(
                'yoga_footer_column_layout', array(
                'default' => 3,
                'sanitize_callback' => 'yoga_sanitize_select',
            ) );

            $wp_customize->add_control(
                'yoga_footer_column_layout', array(
                'type' => 'select',
                'label' => __('Select Column Layout','yoga'),
                'section' => 'footer_widget_column',
                'choices' => array(1=>1, 2=>2,3=>3,4=>4),
            ) );
			
	if ( ! function_exists( 'yoga_sanitize_select' ) ) :

	/**
	 * Sanitize select.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                $input The value to sanitize.
	 * @param WP_Customize_Setting $setting WP_Customize_Setting instance.
	 * @return mixed Sanitized value.
	 */
	function yoga_sanitize_select( $input, $setting ) {

		// Ensure input is a slug.
		$input = sanitize_key( $input );

		// Get list of choices from the control associated with the setting.
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// If the input is a valid key, return it; otherwise, return the default.
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );

	}

endif;		
}
add_action( 'customize_register', 'yoga_footer_copyright' );
?>