<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * NerveTask_Install
 */
class NerveTask_Install {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		add_action( 'init', array( $this, 'default_terms' ) );
		add_action( 'init', array( $this, 'default_email' ) );
		add_action( 'init', array( $this, 'cron' ), 11 );
	}

	/**
	 * default_terms function.
	 *
	 * @access public
	 * @return void
	 */
	public function default_terms() {
		
		//if ( get_option( 'nervetask_installed_terms' ) == 1 )
			//return;

		$taxonomies = array(
			'nervetask_status' => array(
				'New',
				'In Progress',
				'Complete',
				'Review',
				'Revise',
				'Dismiss',
				'Defer'
			),
			'nervetask_priority' => array(
				'Low',
				'Normal',
				'High',
				'Critical'
			)
		);

		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term ) {
				if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) ) {
					wp_insert_term( $term, $taxonomy );
				}
			}
		}

		update_option( 'nervetask_installed_terms', 1 );
	}
	
	public function default_email() {
	
$message = 'A new task (#[post_id] "[post_title]") was created by [post_author]

--------------------

== Task details ==
Title: [post_title]
Author: [post_author] ([post_author_email])

[post_content]

== Actions ==
Edit: [edit_post_url]
View: [permalink]

--------------------

[site_name] | [home_url]';

		// Create emails
		$post = array(
			'post_title'	=> 'New Task',
			'post_content'	=> $message,
			'post_status'	=> 'publish',
			'post_type'		=> 'email'
		);

		// Insert the post into the database
		$post_id = wp_insert_post( $post );

		$email_action 		= 'new';
		$email_type 		= 'nervetask';
		$email_from 		= '[author_email]';
		$email_from_name	= '[display_name]';
		$email_to 			= '[subscribed]';
		$email_to_role 		= '';
		$email_cc 			= '';
		$email_cc_role 		= '';
		$email_bcc 			= '';
		$email_bcc_role 	= '';
		$email_subject 		= '[post_title]';
		$email_message 		= '[post_content]';

		update_post_meta( $post_id, 'email_action', $email_action );
		update_post_meta( $post_id, 'email_type', $email_type );
		update_post_meta( $post_id, 'email_from', $email_from );
		update_post_meta( $post_id, 'email_from_name', $email_from_name );
		update_post_meta( $post_id, 'email_to', $email_to );
		update_post_meta( $post_id, 'email_to_role', $email_to_role );
		update_post_meta( $post_id, 'email_cc', $email_cc );
		update_post_meta( $post_id, 'email_cc_role', $email_cc_role );
		update_post_meta( $post_id, 'email_bcc', $email_bcc );
		update_post_meta( $post_id, 'email_bcc_role',$email_bcc_role );
		update_post_meta( $post_id, 'email_subject', $email_subject );
		
	}

	/**
	 * Setup cron jobs
	 */
	public function cron() {
		wp_clear_scheduled_hook( 'nervetask_check_for_past_due_tasks' );
		wp_schedule_event( time(), 'hourly', 'nervetask_check_for_past_due_tasks' );
	}
}

new NerveTask_Install();