<?php
/*
Plugin Name: WSU Show and Hide
Version: 0.0.1
Description: Attach show/hide or accordion behavior to a specific section of a page or post.
Author: washingtonstateuniversity, jeremyfelt
Author URI: https://web.wsu.edu/
Plugin URI: https://web.wsu.edu/wordpress/plugins/wsu-show-and-hide/
*/

class WSU_Show_And_Hide {
	/**
	 * @var WSU_Show_And_Hide
	 */
	private static $instance;

	/**
	 * Maintain and return the one instance. Initiate hooks when
	 * called the first time.
	 *
	 * @since 0.0.1
	 *
	 * @return \WSU_Show_And_Hide
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WSU_Show_And_Hide();
			self::$instance->setup_hooks();
		}
		return self::$instance;
	}

	/**
	 * Setup hooks to include.
	 *
	 * @since 0.0.1
	 */
	public function setup_hooks() {}

}

add_action( 'after_setup_theme', 'WSU_Show_And_Hide' );
/**
 * Start things up.
 *
 * @return \WSU_Show_And_Hide
 */
function WSU_Show_And_Hide() {
	return WSU_Show_And_Hide::get_instance();
}