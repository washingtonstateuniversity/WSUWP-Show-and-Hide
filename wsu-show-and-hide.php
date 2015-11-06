<?php
/*
Plugin Name: WSU Show and Hide
Version: 0.0.1
Description: Attach show/hide or accordion behavior to a specified area of a page.
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
	public function setup_hooks() {
		add_action( 'init', array( $this, 'add_post_type_support' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * Add support for WSUWP Show and Hide to pages by default.
	 *
	 * @since 0.1.0
	 */
	public function add_post_type_support() {
		add_post_type_support( 'page', 'wsuwp-show-and-hide' );
	}

	/**
	 * Add the meta boxes used by the plugin.
	 *
	 * @param string $post_type The current post type.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( post_type_supports( $post_type, 'wsuwp-show-and-hide' ) ) {
			add_meta_box( 'wsuwp-show-and-hide', 'Show and Hide Behavior', array( $this, 'display_show_and_hide_meta_box' ), $post_type , 'side', 'default' );
		}
	}

	/**
	 * Display a meta box that allows element classes and show/hide behavior to be set for a page.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Post $post The current post object.
	 */
	public function display_show_and_hide_meta_box( $post ) {
		$behavior_defaults = array(
			'show_hide_click' => '',
			'show_or_hide' => 'show',
			'show_hide_change' => '',
		);
		$behavior = get_post_meta( $post->ID, '_wsuwp_show_hide_options', true );
		$behavior = wp_parse_args( $behavior, $behavior_defaults );

		if ( ! in_array( $behavior['show_or_hide'], array( 'show', 'hide' ) ) ) {
			$behavior['show_or_hide'] = 'show';
		}

		if ( empty( esc_attr( $behavior['show_hide_click'] ) ) || empty( esc_attr( $behavior['show_hide_change'] ) ) ) {
			?><p><strong>Current:</strong> No configuration has been saved. Once values are entered and the post has been updated, this will explain the expected behavior.</p><?php
		} else {
			$behavior['show_hide_click'] = esc_attr( $behavior['show_hide_click'] );
			$behavior['show_hide_change'] = esc_attr( $behavior['show_hide_change'] );
			?><p><strong>Current:</strong> When a visitor clicks an element on this page with a class of <code><?php echo $behavior['show_hide_click']; ?></code>
			assigned to it, <strong><?php echo $behavior['show_or_hide']; ?></strong> the element with a class of <code><?php echo $behavior['show_hide_change']; ?></code>.</p><?php
		}
		?>

		<label for="show-hide-click">Element to click:</label>
		<br />
		<input id="show-hide-click" name="show_hide_click" type="text" value="<?php echo esc_attr( $behavior['show_hide_click'] ); ?>">
		<br />
		<label for="show-or-hide">Show or Hide</label><br />
		<select id="show-or-hide" name="show_or_hide">
			<option value="show" <?php selected( $behavior['show_or_hide'], 'show' ); ?>>Show</option>
			<option value="hide" <?php selected( $behavior['show_or_hide'], 'hide' ); ?>>Hide</option>
		</select>
		<br />
		<label for="show-hide-change">Element to change:</label>
		<br />
		<input id="show-hide-change" name="show_hide_change" type="text" value="<?php echo esc_attr( $behavior['show_hide_change'] ); ?>">
		<?php
	}

	/**
	 * Save the show/hide options associated with a post.
	 *
	 * @param int     $post_id ID of the current post being saved.
	 * @param WP_Post $post    Current post object.
	 */
	public function save_post( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! post_type_supports( $post->post_type, 'wsuwp-show-and-hide' ) ) {
			return;
		}

		if ( 'auto-draft' === $post->post_status ) {
			return;
		}

		if ( ! isset( $_POST['show_hide_click'] ) || ! isset( $_POST['show_or_hide'] ) || ! isset( $_POST['show_hide_change'] ) ) {
			return;
		}

		$behavior = array();
		$behavior['show_hide_click'] = sanitize_html_class( $_POST['show_hide_click'] );
		$behavior['show_or_hide'] = in_array( $_POST['show_or_hide'], array( 'show', 'hide' ) ) ? $_POST['show_or_hide'] : 'show';
		$behavior['show_hide_change'] = sanitize_html_class( $_POST['show_hide_change'] );

		update_post_meta( $post_id, '_wsuwp_show_hide_options', $behavior );
	}

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