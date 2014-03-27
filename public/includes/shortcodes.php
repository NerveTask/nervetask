<?php

function nervetask_shortcode_new_task( $atts ) {
	extract( shortcode_atts( array(
		'title'		=> true,
		'content'	=> true,
		'category'	=> false,
		'priority'	=> false,
		'users'		=> false
	), $atts ) );

	ob_start();
	nervetask_new_task_form( $atts );
	return ob_get_clean();
}
add_shortcode( 'nervetask_new_task', 'nervetask_shortcode_new_task' );

function nervetask_shortcode_update_assignees() {
	ob_start();
	require_once( plugin_dir_path( __FILE__ ) . '../views/form-update-assignees.php' );
	return ob_get_clean();
}
add_shortcode( 'nervetask_update_assignees', 'nervetask_shortcode_update_assignees' );

function nervetask_shortcode_update_status() {
	ob_start();
	require_once( plugin_dir_path( __FILE__ ) . '../views/form-update-status.php' );
	return ob_get_clean();
}
add_shortcode( 'nervetask_update_status', 'nervetask_shortcode_update_status' );

function nervetask_shortcode_update_priority() {
	ob_start();
	require_once( plugin_dir_path( __FILE__ ) . '../views/form-update-priority.php' );
	return ob_get_clean();
}
add_shortcode( 'nervetask_update_priority', 'nervetask_shortcode_update_priority' );

function nervetask_shortcode_update_category() {
	ob_start();
	require_once( plugin_dir_path( __FILE__ ) . '../views/form-update-category.php' );
	return ob_get_clean();
}
add_shortcode( 'nervetask_update_category', 'nervetask_shortcode_update_category' );

function nervetask_shortcode_update_tags() {
	ob_start();
	require_once( plugin_dir_path( __FILE__ ) . '../views/form-update-tags.php' );
	return ob_get_clean();
}
add_shortcode( 'nervetask_update_tags', 'nervetask_shortcode_update_tags' );

function nervetask_shortcode_update_content() {
	ob_start();
	require_once( plugin_dir_path( __FILE__ ) . '../views/form-update-content.php' );
	return ob_get_clean();
}
add_shortcode( 'nervetask_update_content', 'nervetask_shortcode_update_content' );

function nervetask_shortcode_update_due_date() {
	ob_start();
	require_once( plugin_dir_path( __FILE__ ) . '../views/form-update-due_date.php' );
	return ob_get_clean();
}
add_shortcode( 'nervetask_update_due_date', 'nervetask_shortcode_update_due_date' );

function nervetask_new_task_form( $atts ) {
	require_once( plugin_dir_path( __FILE__ ) . '../views/form-new-task.php' );
}