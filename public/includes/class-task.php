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
class NerveTask_Task {

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

		if ( is_admin() && ( defined('DOING_AJAX') && DOING_AJAX ) ) {
			add_action( 'wp_ajax_nopriv_nervetask_new_task',	array( $this, 'insert' ) );
			add_action( 'wp_ajax_nervetask_new_task',			array( $this, 'insert' ) );
		} else {
			add_action( 'init',	array( $this, 'insert' ) );
		}
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
	 * Inserts a new task.
	 *
	 * First checks if the task is being added through a regular $_POST and
	 * performs a standard page refresh.
	 *
	 * If an ajax referrer is set and valid then proceed instead by dying
	 * and returning a response for the client.
	 *
	 * @since    0.1.0
	 */
	public function insert() {

		// If the current user can't publish posts stop
		if ( !current_user_can('publish_posts') ) {
			return;
		}

		if ( ( defined('DOING_AJAX') && DOING_AJAX ) || !empty( $_POST['nervetask_new_task'] ) ) {

			$_POST = maybe_unserialize( $_POST );
			$post_content	= $_POST['nervetask-new-task-content'];
			$post_status	= 'publish';
			$post_title 	= $_POST['nervetask-new-task-title'];
			$post_type		= 'nervetask';

			$args = array(
				'post_content'  => $post_content,
				'post_status'	=> $post_status,
				'post_title'    => $post_title,
				'post_type'		=> $post_type,
			);

			$post_id = wp_insert_post( $args );

			// If this is an ajax request
			if ( defined('DOING_AJAX') && DOING_AJAX ) {

				// If the post inserted succesffully
				if ( $post_id != 0 ) {

					$post = get_post( $post_id );

					die(
						json_encode(
							array(
								'success' => true,
								'message' => __('Success!'),
								'post' => $post
							)
						)
					);
				} else {
					die(
						json_encode(
							array(
								'success' => false,
								'message' => __('An error occured. Please refresh the page and try again.')
							)
						)
					);
				}
			}
		}
	}
}