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
	 * Initialize the comments class.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {

		add_action( 'set_object_terms', array( $this, 'updated_terms_comment' ), 10, 6 );
		add_action( 'updated_post_meta', array( $this, 'updated_post_meta' ), 10, 4 );
		add_action( 'p2p_created_connection', array( $this, 'updated_assignees' ), 10, 1 );
		add_action( 'save_post', array( $this, 'updated_post_comment' ), 10, 1 );
		add_action( 'wp_insert_comment', array( $this, 'new_comment' ), 10, 2 );

		add_filter( 'comment_class', array( $this, 'status_classes' ), 10, 4 );

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
		
		$data = array(
			'comment_author' => $current_user -> display_name,
			'comment_author_email' => $current_user -> user_email,
			'comment_content' => 'updated the '. $taxonomy_object->labels->singular_name .': '. $terms_list,
			'comment_post_ID' => $object_id,
			'comment_type' =>'status'
		);

		$comment_id = wp_insert_comment( $data );

		if( $comment_id ) {

			$statuses = get_the_terms( $object_id, 'nervetask_status' );

			$comment_meta = update_comment_meta( $comment_id, 'nervetask_status', $statuses );

		}
		
		return $comment_id;

	}

	/**
	 * Adds a comment when a task's content is updated.
	 *
	 * @since    0.1.0
	 */
	public function updated_post_meta( $meta_id, $post_id, $meta_key, $meta_value ) {

		$current_user = wp_get_current_user();
		$post = get_post( $post_id );

		if( empty( $post ) ) {
			return;
		}
		
		if ( 'nervetask' != get_post_type( $post ) ) {
			return;
		}
		
		if( 'nervetask_due_date' == $meta_key ) {

			$data = array(
				'comment_author' => $current_user -> display_name,
				'comment_author_email' => $current_user -> user_email,
				'comment_content' => ' updated the due date to <strong>'. $_POST['nervetask_due_date'] .'</strong>',
				'comment_post_ID' => $post_id,
				'comment_type' =>'status'
			);

			$comment_id = wp_insert_comment( $data );

			if( $comment_id ) {

				$statuses = get_the_terms( $post_id, 'nervetask_status' );

				$comment_meta = update_comment_meta( $comment_id, 'nervetask_status', $statuses );

			}

			return $comment_id;

		} else {
			return;
		}

	}

	/**
	 * Adds a comment when a task's content is updated.
	 *
	 * @since    0.1.0
	 */
	public function updated_post_comment( $object_id ) {
		
		$current_user = wp_get_current_user();
		$post = get_post( $object_id );

		if( empty( $post ) ) {
			return;
		}
		
		if ( 'nervetask' != get_post_type( $post ) ) {
			return;
		}

		if( isset( $_POST['controller'] ) && ('nervetask_update_due_date' == $_POST['controller'] ) ) {

			$data = array(
				'comment_author' => $current_user -> display_name,
				'comment_author_email' => $current_user -> user_email,
				'comment_content' => 'updated the due date to <strong>'. $_POST['nervetask_due_date'] .'</strong>',
				'comment_post_ID' => $object_id,
				'comment_type' =>'status'
			);

		} else {
		
			$revisions = wp_get_post_revisions($post->ID, array( 'posts_per_page' => 1 ) );

			foreach( $revisions as $revision ) {
				$revision_id = $revision->ID;
			}

			if( empty( $revision_id ) ) {
				return;
			}

			$data = array(
				'comment_author' => $current_user -> display_name,
				'comment_author_email' => $current_user -> user_email,
				'comment_content' => 'updated the task content - <a href="'. esc_url( admin_url( 'revision.php?revision='. $revision_id ) ) .'">revisions</a>',
				'comment_post_ID' => $object_id,
				'comment_type' =>'status'
			);
			
		}

		$comment_id = wp_insert_comment( $data );

		if( $comment_id ) {

			$statuses = get_the_terms( $object_id, 'nervetask_status' );

			$comment_meta = update_comment_meta( $comment_id, 'nervetask_status', $statuses );

		}
		
		return $comment_id;

	}

	/**
	 * Adds a comment when a task's assignees are updated.
	 *
	 * @since    0.1.0
	 */
	public function updated_assignees( $p2p_id ) {
		
		$current_user = wp_get_current_user();

		$connection = p2p_get_connection( $p2p_id );

		if ( 'nervetask_to_user' == $connection->p2p_type ) {

			$user_query = new WP_User_Query(
				array(
					'fields' => array(
						'ID',
						'display_name'
					)
				)
			);

			// Query for users based on the meta data
			$assigned_user_query = new WP_User_Query(
				array(
					'fields'			=> 'ID',
					'connected_type'	=> 'nervetask_to_user',
					'connected_items'	=> $connection->p2p_from
				)
			);
			
			$assigned_users = '';
			foreach ( $assigned_user_query->results as $user ) {
				$user = get_user_by( 'id', $user );
				if( isset( $prefix ) ) {
					$assigned_users .= $prefix;
				}
				$assigned_users .= esc_html( $user->display_name );
				$prefix = ', ';
			}
			
			$data = array(
				'comment_author' => $current_user -> display_name,
				'comment_author_email' => $current_user -> user_email,
				'comment_content' => 'updated the assignee(s) to <strong>'. $assigned_users .'</strong>',
				'comment_post_ID' => $connection->p2p_from,
				'comment_type' =>'status'
			);
			
			$comment_id = wp_insert_comment( $data );

			if( $comment_id ) {

				$statuses = get_the_terms( $connection->p2p_from, 'nervetask_status' );

				$comment_meta = update_comment_meta( $comment_id, 'nervetask_status', $statuses );

			}

			return $comment_id;
			
		}

	}

	/**
	 * Updates the comment meta when a new comment is inserted.
	 *
	 * @since    0.1.0
	 */
	public function new_comment( $comment_id, $comment_object ) {
		
		$post = get_post( $comment_object->comment_parent );

		if( empty( $post ) ) {
			return;
		}
		
		if ( 'nervetask' != get_post_type( $post ) ) {
			return;
		}

		$statuses = get_the_terms( $post->ID, 'nervetask_status' );

		$comment_meta = update_comment_meta( $comment_id, 'nervetask_status', $statuses );

	}

	function status_classes( $classes ) {

		$statuses = get_comment_meta( get_comment_ID(), 'nervetask_status', true );

		if ( isset( $statuses ) && is_array( $statuses ) ) {
			foreach( $statuses as $status ) {
				$status = get_term( $status->term_id, 'nervetask_status' );
				$classes[] = 'nervetask-status-'. $status->slug;
			}
		}

		// Return the result
		return $classes;

	}
}