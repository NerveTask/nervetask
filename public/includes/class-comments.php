<?php
/**
 * NerveTask.
 *
 * @package   NerveTask
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://nervetask.com
 * @copyright 2014 NerveTask
 */

/**
 * @package NerveTask
 * @author  Patrick Daly <patrick@developdaly.com>
 */
class NerveTask_Comments {

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {

		add_action( 'set_object_terms', array( $this, 'updated_terms_comment' ), 10, 6 );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Adds a comment when a task's terms are updated.
	 *
	 * @since    0.1.0
	 */
	public function updated_terms_comment( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		
		$current_user = wp_get_current_user();
		$post = get_post( $object_id );

		if( empty( $post ) ) {
			return;
		}
		
		if ( 'nervetask' != get_post_type( $post ) ) {
			return;
		}
		
		// No changes to terms, so stop
		$diff = array_diff( $tt_ids, $old_tt_ids );
		if( empty( $diff ) ) {
			return;
		}

		$terms = get_the_terms( $object_id, $taxonomy );

		if ( $terms && ! is_wp_error( $terms ) ) {

			$terms_array = array();

			foreach ( $terms as $term ) {
				$terms_array[] = '<a href="'. get_term_link( $term->term_id, $taxonomy ) .'">'. $term->name .'</a>';
			}

			$terms_list = join( ', ', $terms_array );
		
		}

		$taxonomy_object = get_taxonomy( $taxonomy );
		
		// If the current user can't edit posts stop
		if ( !current_user_can('edit_posts') ) {
			$output = __( 'You don\'t have proper permissions to update the due date of this task. :(', 'nervetask' );
			return $output;
		}

		$data = array(
			'comment_author' => $current_user -> display_name,
			'comment_author_email' => $current_user -> user_email,
			'comment_content' => 'updated the '. $taxonomy_object->singular_label .': '. $terms_list,
			'comment_post_ID' => $object_id,
			'comment_type' =>'status'
		);

		$comment_id = wp_insert_comment($data);
		
		return $comment_id;

	}

}