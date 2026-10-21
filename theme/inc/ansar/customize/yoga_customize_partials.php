<?php
/** 
 * Yoga Customizer partials.
 *
 * @package Yoga
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Yoga_Customizer_Partials' ) ) {

	/**
	 * Customizer Partials.
	 */
	class Yoga_Customizer_Partials {

		/**
		 * Instance.
		 *
		 * @access private
		 * @var object
		 */
		private static $instance;

		/**
		 * Initiator.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		// site title
		public static function customize_partial_blogname() {
			return get_bloginfo( 'name' );
		}

		// Site tagline
		public static function customize_partial_blogdescription() {
			return get_bloginfo( 'description' );
		}
		
	}
}

Yoga_Customizer_Partials::get_instance();