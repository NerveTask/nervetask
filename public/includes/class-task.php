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
			add_action( 'wp_ajax_nopriv_nervetask',	array( $this, 'go' ) );
			add_action( 'wp_ajax_nervetask',		array( $this, 'go' ) );
		} else {
			add_action( 'init',	array( $this, 'go' ), 11 );
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
	 * This is the first function in the task process. It will route actions
	 * based on the controller value.
	 *
	 * First checks if the user is being added through a regular $_POST and
	 * performs a standard page refresh.
	 *
	 * If an ajax referrer is set and valid then proceed instead by dying
	 * and returning a response for the client.
	 *
	 * @since    0.1.0
	 */
	public function go() {

		if ( !empty( $_POST ) || ( defined('DOING_AJAX') && DOING_AJAX ) ) {

			if( isset( $_POST['action'] ) && $_POST['action'] == 'nervetask' ) {

				if( $_POST['controller'] == 'nervetask_new_task' ) {
					$result = self::new_task($_POST);
				}
				if( $_POST['controller'] == 'nervetask_update_content' ) {
					$result = self::update_content($_POST);
				}
				if( $_POST['controller'] == 'nervetask_insert_comment' ) {
					$result = self::insert_comment($_POST);
				}
				if( $_POST['controller'] == 'nervetask_update_assignees' ) {
					$result = self::update_assignees($_POST);
				}
				if( $_POST['controller'] == 'nervetask_update_status' ) {
					$result = self::update_status($_POST);
				}
				if( $_POST['controller'] == 'nervetask_update_priority' ) {
					$result = self::update_priority($_POST);
				}
				if( $_POST['controller'] == 'nervetask_update_category' ) {
					$result = self::update_category($_POST);
				}
				if( $_POST['controller'] == 'nervetask_update_tags' ) {
					$result = self::update_tags($_POST);
				}
				if( $_POST['controller'] == 'nervetask_update_due_date' ) {
					$result = self::update_due_date($_POST);
				}

				// If this is an ajax request
				if ( defined('DOING_AJAX') && DOING_AJAX ) {

					if ( isset( $result ) ) {
						die( json_encode( $result ) );
					} else {
						die(
							json_encode(
								array(
									'success' => false,
									'message' => __( 'An error occured. Please refresh the page and try again.', 'nervetask' )
								)
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Inserts a new task.
	 *
	 * @since    0.1.0
	 */
	public function new_task( $data ) {

		if( empty( $data ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $data['security'], 'nervetask_new_task' ) ) {
			die(
				json_encode(
					array(
						'success' => false,
						'message' => __( 'Something is wrong with your session. Try logging out and logging back in.' )
					)
				)
			);
		}

		// If the current user can't publish posts stop
		if ( !current_user_can('publish_posts') ) {
			$output = __( 'You don\'t have proper permissions to create a new task. :(', 'nervetask' );
			return $output;
		}

		if( isset( $data['nervetask-new-task-content'] ) ) {
			$post_content = $data['nervetask-new-task-content'];
		} else {
			$post_content = '';
		}
		$post_status	= 'publish';
		if( isset( $data['nervetask-new-task-title'] ) ) {
			$post_title = $data['nervetask-new-task-title'];
		} else {
			$post_title = '';
		}
		$post_type		= 'nervetask';

		$args = array(
			'post_content'	=> wp_kses_post( $post_content ),
			'post_status'	=> $post_status,
			'post_title'	=> sanitize_text_field( $post_title ),
			'post_type'		=> sanitize_title( $post_type ),
		);

		// Insert the new task and get its ID
		$post_id = wp_insert_post( $args );

		// TODO: Retrieve default status, priority, etc. from stored options
		if( isset( $data['nervetask_category'] ) ) {

			$categories = $data['nervetask_category'];
			// Convert array values from strings to integers
			$categories = array_map('intval', $categories);
			wp_set_post_terms( $post_id, $categories, 'nervetask_category' );
			
		}

		// Set the user's priority or the default if not sent
		if( isset( $data['nervetask_priority'] ) ) {

			$priorities = $data['nervetask_priority'];
			// Convert array values from strings to integers
			$priorities = array_map('intval', $priorities);
			wp_set_post_terms( $post_id, $priorities,	'nervetask_priority' );
			
		} else {
			wp_set_post_terms( $post_id, 'normal', 'nervetask_priority' );
		}

		// Set the user's status or the default if not sent
		if( isset( $data['nervetask_status'] ) ) {
			
			$statuses = $data['nervetask_status'];
			// Convert array values from strings to integers
			$statuses = array_map('intval', $statuses);
			wp_set_post_terms( $post_id, $statuses,	'nervetask_status' );
			
		} else {
			wp_set_post_terms( $post_id, 'new', 'nervetask_status' );
		}

		// Set the task's due date
		if( isset( $data['nervetask_due_date'] ) ) {
			$due_date = $data['nervetask_due_date'];

			// Validates the due date ISO 8061 format by trying to recreate the date
			$due_date = new DateTime($due_date);
			update_post_meta( $post_id, 'nervetask_due_date', $due_date );
		}

		// If the task inserted succesffully
		if ( $post_id != 0 ) {

			$post = get_post( $post_id );

			$output = array(
				'status'	=> 'success',
				'message'	=> __('Success!'),
				'post'		=> $post
			);

		} else {
			$output = __( 'There was an error while creating a new task. Please refresh the page and try again.', 'nervetask' );
		}

		return $output;
	}

	/**
	 * Updates a task's content.
	 *
	 * @since    0.1.0
	 */
	public function update_content( $data ) {

		if( empty( $data ) ) {
			return;
		}

		// If the current user can't publish posts stop
		if ( !current_user_can('edit_posts') ) {
			$output = __( 'You don\'t have proper permissions to update this task. :(', 'nervetask' );
			return $output;
		}

		if( isset( $data['nervetask-new-task-content'] ) ) {
			$post_content = $data['nervetask-new-task-content'];
		} else {
			$post_content = '';
		}

		$args = array(
			'ID'			=> absint( $data['post_id'] ),
			'post_content'	=> wp_kses_post( $post_content )
		);

		$post_id = wp_update_post( $args );

		// If the content updated succesffully
		if ( $post_id != 0 ) {

			$post = get_post( $post_id );

			$output = array(
				'status'	=> 'success',
				'message'	=> __('Success!'),
				'post'		=> $post
			);

		} else {
			$output = __( 'There was an error while creating a new task. Please refresh the page and try again.', 'nervetask' );
		}

		return $output;
	}

	/**
	 * Inserts a new comment.
	 *
	 * @since    0.1.0
	 */
	public function insert_comment( $data ) {

		if( empty( $data ) ) {
			return;
		}

		check_ajax_referer( 'nervetask_update_content', 'security' );

		// If the current user can't publish posts stop
		if ( !current_user_can('read') ) {
			$output = __( 'You don\'t have proper permissions to insert this comment. :(', 'nervetask' );
			return $output;
		}

		if( isset( $data['post_id'] ) ) {
			$comment_post_id = $data['post_id'];
		} else {
			return;
		}
		if( isset( $_POST['nervetask-new-comment-content'] ) ) {
			$comment_content = $data['nervetask-new-comment-content'];
		} else {
			$comment_content = '';
		}

		$args = array(
			'comment_post_ID'	=> $comment_post_id,
			'comment_content'	=> $comment_content
		);

		$comment_id = wp_insert_comment ( $args );

		// If the comment inserted succesffully
		if ( $comment_id != 0 ) {

			$comment = get_comment( $comment_id );

			$output = array(
				'status'	=> 'success',
				'message'	=> __('Success!'),
				'comment'	=> $comment
			);
		} else {
			$output = __( 'There was an error while creating a new task. Please refresh the page and try again.', 'nervetask' );
		}
	}

	/**
	 * Updates the users assigned to a task.
	 *
	 * @since    0.1.0
	 */
	public function update_assignees( $data ) {

		if( empty( $data ) ) {
			return;
		}

		check_ajax_referer( 'nervetask_update_assignees', 'security' );

		// If the current user can't edit posts stop
		if ( !current_user_can('edit_posts') ) {
			$output = __( 'You don\'t have proper permissions to update the assignees of this task. :(', 'nervetask' );
			return $output;
		}

		$users		= $data['users'];
		$post_id	= $data['post_id'];
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
		$comment = get_comments( array( 'post_id' => $post_id, 'number' => 1 ) );

		// If the assignee updated succesffully
		if ( !empty( $users ) ) {
			$output = array(
				'status'	=> 'success',
				'message'	=> __('Success!'),
				'users'		=> $users,
				'comment'	=> $comment
			);
		} else {
			$output = __( 'There was an error while creating a new task. Please refresh the page and try again.', 'nervetask' );
		}

		return $output;
	}

	/**
	 * Updates the status of a task.
	 *
	 * @since    0.1.0
	 */
	public function update_status( $data ) {

		if( empty( $data ) ) {
			return;
		}

		check_ajax_referer( 'nervetask_update_status', 'security' );

		// If the current user can't edit posts stop
		if ( !current_user_can('edit_posts') ) {
			$output = __( 'You don\'t have proper permissions to update the status of this task. :(', 'nervetask' );
			return $output;
		}

		$status		= $data['status'];
		$post_id	= $data['post_id'];
		
		// Convert array values from strings to integers
		$status = array_map('intval', $status);

		// Update the terms
		$result = wp_set_post_terms( $post_id, $status, 'nervetask_status' );

		// If the status updated succesffully
		if ( $result ) {

			$terms = get_the_terms( $post_id, 'nervetask_status' );
			$comment = get_comments( array( 'post_id' => $post_id, 'number' => 1 ) );

			$output = array(
				'status'	=> 'success',
				'message'	=> __('Success!'),
				'terms'		=> $terms,
				'comment'	=> $comment
			);

		} else {
			$output = __( 'There was an error while creating a new task. Please refresh the page and try again.', 'nervetask' );
		}

		return $output;
	}

	/**
	 * Updates the priority of a task.
	 *
	 * @since    0.1.0
	 */
	public function update_priority( $data ) {

		if( empty( $data ) ) {
			return;
		}

		check_ajax_referer( 'nervetask_update_priority', 'security' );

		// If the current user can't edit posts stop
		if ( !current_user_can('edit_posts') ) {
			$output = __( 'You don\'t have proper permissions to update the priority task. :(', 'nervetask' );
			return $output;
		}

		$priority	= $data['priority'];
		$post_id	= $data['post_id'];

		// Convert array values from strings to integers
		$priority = array_map('intval', $priority);

		// Update the terms
		$result = wp_set_post_terms( $post_id, $priority, 'nervetask_priority' );

		// If the priority updated succesffully
		if ( $result ) {

			$terms = get_the_terms( $post_id, 'nervetask_priority' );
			$comment = get_comments( array( 'post_id' => $post_id, 'number' => 1 ) );

			$output = array(
				'status'	=> 'success',
				'message'	=> __('Success!'),
				'terms'		=> $terms,
				'comment'	=> $comment
			);

		} else {
			$output = __( 'There was an error while creating a new task. Please refresh the page and try again.', 'nervetask' );
		}

		return $output;
	}

	/**
	 * Updates the category of a task.
	 *
	 * @since    0.1.0
	 */
	public function update_category( $data ) {

		if( empty( $data ) ) {
			return;
		}

		check_ajax_referer( 'nervetask_update_category', 'security' );

		// If the current user can't edit posts stop
		if ( !current_user_can('edit_posts') ) {
			$output = __( 'You don\'t have proper permissions to update the category of this task. :(', 'nervetask' );
			return $output;
		}

		$category	= $data['category'];
		$post_id	= $data['post_id'];

		// Convert array values from strings to integers
		$category = array_map('intval', $category);

		// Update the terms
		$result = wp_set_post_terms( $post_id, $category, 'nervetask_category' );

		// If the category succesffully
		if ( $result ) {

			$terms = get_the_terms( $post_id, 'nervetask_category' );
			$comment = get_comments( array( 'post_id' => $post_id, 'number' => 1 ) );

			$output = array(
				'status'	=> 'success',
				'message'	=> __('Success!'),
				'terms'		=> $terms,
				'comment'	=> $comment
			);

		} else {
			$output = __( 'There was an error while creating a new task. Please refresh the page and try again.', 'nervetask' );
		}

		return $output;
	}

	/**
	 * Updates the tags of a task.
	 *
	 * @since    0.1.0
	 */
	public function update_tags( $data ) {

		if( empty( $data ) ) {
			return;
		}

		check_ajax_referer( 'nervetask_update_tags', 'security' );

		// If the current user can't edit posts, stop
		if ( !current_user_can('edit_posts') ) {
			$output = __( 'You don\'t have proper permissions to update the tags for this task. :(', 'nervetask' );
			return $output;
		}

		$tags	= $data['tags'];
		$post_id	= $data['post_id'];

		// Update the terms
		$result = wp_set_post_terms( $post_id, $tags, 'nervetask_tags' );

		// If the tags saved succesffully
		if ( $result ) {

			$terms = get_the_terms( $post_id, 'nervetask_tags' );
			$comment = get_comments( array( 'post_id' => $post_id, 'number' => 1 ) );

			$output = array(
				'status'	=> 'success',
				'message'	=> __('Success!'),
				'terms'		=> $terms,
				'comment'	=> $comment
			);

		} else {
			$output = __( 'There was an error while updating the tags. Please refresh the page and try again.', 'nervetask' );
		}

		return $output;
	}
	
	/**
	 * Updates the category of a task.
	 *
	 * @since    0.1.0
	 */
	public function update_due_date( $data ) {

		if( empty( $data ) ) {
			return;
		}

		check_ajax_referer( 'nervetask_update_due_date', 'security' );

		// If the current user can't edit posts stop
		if ( !current_user_can('edit_posts') ) {
			$output = __( 'You don\'t have proper permissions to update the due date of this task. :(', 'nervetask' );
			return $output;
		}
		
		$post_id	= $data['post_id'];
		
		$meta_value = array(
			'due_date'	=> $data['nervetask_due_date'],
			'timestamp'	=> new DateTime()
		);

		// Update the meta
		$result = update_post_meta( $post_id, 'nervetask_due_date', json_encode( $meta_value ) );

		// If the meta saved successfully
		if ( $result ) {

			$due_date_object = get_post_meta( $post_id, 'nervetask_due_date', true );
			$due_date_object_decoded = json_decode( $due_date_object );
			$due_date = new DateTime( $due_date_object_decoded->due_date );
			$comment = get_comments( array( 'post_id' => $post_id, 'number' => 1 ) );

			$output = array(
				'status'	=> 'success',
				'message'	=> __( 'Success!' ),
				'due_date'	=> $due_date,
				'comment'	=> $comment
			);

		} else {
			$output = __( 'There was an error while creating a new task. Please refresh the page and try again.', 'nervetask' );
		}

		return $output;
	}

}