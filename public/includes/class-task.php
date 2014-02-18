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
			add_action( 'wp_ajax_nopriv_nervetask_new_task',		array( $this, 'insert' ) );
			add_action( 'wp_ajax_nervetask_new_task',				array( $this, 'insert' ) );

			add_action( 'wp_ajax_nopriv_nervetask_update_content',	array( $this, 'update_content' ) );
			add_action( 'wp_ajax_nervetask_update_content',			array( $this, 'update_content' ) );

			add_action( 'wp_ajax_nopriv_nervetask_insert_comment',	array( $this, 'insert_comment' ) );
			add_action( 'wp_ajax_nervetask_insert_comment',			array( $this, 'insert_comment' ) );

			add_action( 'wp_ajax_nopriv_nervetask_update_assignees',array( $this, 'update_assignees' ) );
			add_action( 'wp_ajax_nervetask_update_assignees',		array( $this, 'update_assignees' ) );

			add_action( 'wp_ajax_nopriv_nervetask_update_status',	array( $this, 'update_status' ) );
			add_action( 'wp_ajax_nervetask_update_status',			array( $this, 'update_status' ) );

			add_action( 'wp_ajax_nopriv_nervetask_update_priority',	array( $this, 'update_priority' ) );
			add_action( 'wp_ajax_nervetask_update_priority',		array( $this, 'update_priority' ) );

			add_action( 'wp_ajax_nopriv_nervetask_update_category',	array( $this, 'update_category' ) );
			add_action( 'wp_ajax_nervetask_update_category',		array( $this, 'update_category' ) );
		} else {
			add_action( 'init',	array( $this, 'insert' ) );
			add_action( 'init',	array( $this, 'update_content' ) );
			add_action( 'init',	array( $this, 'insert_comment' ) );
			add_action( 'init',	array( $this, 'update_assignees' ) );
			add_action( 'init',	array( $this, 'update_status' ) );
			add_action( 'init',	array( $this, 'update_priority' ) );
			add_action( 'init',	array( $this, 'update_category' ) );
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
			if( isset( $_POST['nervetask-new-task-content'] ) ) {
				$post_content = $_POST['nervetask-new-task-content'];
			} else {
				$post_content = '';
			}
			$post_status	= 'publish';
			if( isset( $_POST['nervetask-new-task-title'] ) ) {
				$post_title = $_POST['nervetask-new-task-title'];
			} else {
				$post_title = '';
			}
			$post_type		= 'nervetask';

			$args = array(
				'post_content'  => $post_content,
				'post_status'	=> $post_status,
				'post_title'    => $post_title,
				'post_type'		=> $post_type,
			);

			$post_id = wp_insert_post( $args );

			// TODO: Retrieve default status and priority from options
			wp_set_post_terms( $post_id, array( 'new' ), 'nervetask_status' );
			wp_set_post_terms( $post_id, array( 'normal' ), 'nervetask_priority' );

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

	/**
	 * Updates a task's content.
	 *
	 * First checks if the task is being added through a regular $_POST and
	 * performs a standard page refresh.
	 *
	 * If an ajax referrer is set and valid then proceed instead by dying
	 * and returning a response for the client.
	 *
	 * @since    0.1.0
	 */
	public function update_content() {

		// If the current user can't publish posts stop
		if ( !current_user_can('edit_posts') ) {
			return;
		}

		if ( ( defined('DOING_AJAX') && DOING_AJAX ) || !empty( $_POST['nervetask_update_content'] ) ) {

			$_POST = maybe_unserialize( $_POST );
			$post_id	= $_POST['post_id'];
			if( isset( $_POST['nervetask-new-task-content'] ) ) {
				$post_content = $_POST['nervetask-new-task-content'];
			} else {
				$post_content = '';
			}

			$args = array(
				'ID'			=> $post_id,
				'post_content'  => $post_content
			);

			$post_id = wp_update_post( $args );

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

	/**
	 * Inserts a new comment.
	 *
	 * First checks if the task is being added through a regular $_POST and
	 * performs a standard page refresh.
	 *
	 * If an ajax referrer is set and valid then proceed instead by dying
	 * and returning a response for the client.
	 *
	 * @since    0.1.0
	 */
	public function insert_comment() {

		// If the current user can't publish posts stop
		if ( !current_user_can('read') ) {
			return;
		}

		if ( ( defined('DOING_AJAX') && DOING_AJAX ) || !empty( $_POST['nervetask_insert_comment'] ) ) {

			$_POST = maybe_unserialize( $_POST );
			if( isset( $_POST['post_id'] ) ) {
				$comment_post_id = $_POST['post_id'];
			} else {
				return;
			}
			if( isset( $_POST['nervetask-new-comment-content'] ) ) {
				$comment_content = $_POST['nervetask-new-comment-content'];
			} else {
				$comment_content = '';
			}

			$args = array(
				'comment_post_ID'	=> $comment_post_id,
				'comment_content'	=> $comment_content
			);

			$comment_id = wp_insert_comment ( $args );

			// If this is an ajax request
			if ( defined('DOING_AJAX') && DOING_AJAX ) {

				// If the post inserted succesffully
				if ( $comment_id != 0 ) {

					$comment = get_comment( $comment_id );

					die(
						json_encode(
							array(
								'success' => true,
								'message' => __('Success!'),
								'comment' => $comment
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

	/**
	 * Updates the users assigned to a task.
	 *
	 * First checks if the user is being added through a regular $_POST and
	 * performs a standard page refresh.
	 *
	 * If an ajax referrer is set and valid then proceed instead by dying
	 * and returning a response for the client.
	 *
	 * @since    0.1.0
	 */
	public function update_assignees() {

		// If the current user can't edit posts stop
		if ( !current_user_can('edit_posts') ) {
			return;
		}

		if ( ( defined('DOING_AJAX') && DOING_AJAX ) || !empty( $_POST['nervetask_update_assignees'] ) ) {

			$_POST		= maybe_unserialize( $_POST );
			$users		= $_POST['users'];
			$post_id	= $_POST['post_id'];
			$all_users	= get_users();

			foreach ($all_users as $all_user) {
				p2p_type('nervetask_to_user')->disconnect( $post_id, $all_user->ID );
			}

			foreach ($users as $user) {
				p2p_type('nervetask_to_user')->connect( $post_id, $user, array( 'date' => current_time( 'mysql' ) ) );
			}

			$users = get_users(
				array(
					'connected_type' => 'nervetask_to_user',
					'connected_items' => $post_id
				)
			);

			// If this is an ajax request
			if ( defined('DOING_AJAX') && DOING_AJAX ) {

				// If the post inserted succesffully
				if ( !empty( $users ) ) {

					$post = get_post( $post_id );

					die(
						json_encode(
							array(
								'success' => true,
								'message' => __('Success!'),
								'users' => $users
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

	/**
	 * Updates the status of a task.
	 *
	 * First checks if the user is being added through a regular $_POST and
	 * performs a standard page refresh.
	 *
	 * If an ajax referrer is set and valid then proceed instead by dying
	 * and returning a response for the client.
	 *
	 * @since    0.1.0
	 */
	public function update_status() {

		// If the current user can't edit posts stop
		if ( !current_user_can('edit_posts') ) {
			return;
		}

		if ( ( defined('DOING_AJAX') && DOING_AJAX ) || !empty( $_POST['nervetask_update_status'] ) ) {

			$_POST		= maybe_unserialize( $_POST );
			$status		= $_POST['status'];
			$post_id	= $_POST['post_id'];

			// Convert array values from strings to integers
			$status = array_map(
				create_function('$value', 'return (int)$value;'),
				$status
			);

			// Update the terms
			$result = wp_set_post_terms( $post_id, $status, 'nervetask_status' );

			// If this is an ajax request
			if ( defined('DOING_AJAX') && DOING_AJAX ) {

				// If the post inserted succesffully
				if ( $result ) {

					$terms = get_the_terms( $post_id, 'nervetask_status' );

					die(
						json_encode(
							array(
								'success' => true,
								'message' => __('Success!'),
								'terms' => $terms
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

	/**
	 * Updates the priority of a task.
	 *
	 * First checks if the user is being added through a regular $_POST and
	 * performs a standard page refresh.
	 *
	 * If an ajax referrer is set and valid then proceed instead by dying
	 * and returning a response for the client.
	 *
	 * @since    0.1.0
	 */
	public function update_priority() {

		// If the current user can't edit posts stop
		if ( !current_user_can('edit_posts') ) {
			return;
		}

		if ( ( defined('DOING_AJAX') && DOING_AJAX ) || !empty( $_POST['nervetask_update_priority'] ) ) {

			$_POST		= maybe_unserialize( $_POST );
			$priority		= $_POST['priority'];
			$post_id	= $_POST['post_id'];

			// Convert array values from strings to integers
			$priority = array_map(
				create_function('$value', 'return (int)$value;'),
				$priority
			);

			// Update the terms
			$result = wp_set_post_terms( $post_id, $priority, 'nervetask_priority' );

			// If this is an ajax request
			if ( defined('DOING_AJAX') && DOING_AJAX ) {

				// If the post inserted succesffully
				if ( $result ) {

					$terms = get_the_terms( $post_id, 'nervetask_priority' );

					die(
						json_encode(
							array(
								'success' => true,
								'message' => __('Success!'),
								'terms' => $terms
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
	/**
	 * Updates the category of a task.
	 *
	 * First checks if the user is being added through a regular $_POST and
	 * performs a standard page refresh.
	 *
	 * If an ajax referrer is set and valid then proceed instead by dying
	 * and returning a response for the client.
	 *
	 * @since    0.1.0
	 */
	public function update_category() {

		// If the current user can't edit posts stop
		if ( !current_user_can('edit_posts') ) {
			return;
		}

		if ( ( defined('DOING_AJAX') && DOING_AJAX ) || !empty( $_POST['nervetask_update_category'] ) ) {

			$_POST		= maybe_unserialize( $_POST );
			$category		= $_POST['category'];
			$post_id	= $_POST['post_id'];

			// Convert array values from strings to integers
			$category = array_map(
				create_function('$value', 'return (int)$value;'),
				$category
			);

			// Update the terms
			$result = wp_set_post_terms( $post_id, $category, 'nervetask_category' );

			// If this is an ajax request
			if ( defined('DOING_AJAX') && DOING_AJAX ) {

				// If the post inserted succesffully
				if ( $result ) {

					$terms = get_the_terms( $post_id, 'nervetask_category' );

					die(
						json_encode(
							array(
								'success' => true,
								'message' => __('Success!'),
								'terms' => $terms
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