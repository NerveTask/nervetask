<?php

function nervetask_shortcode_new_task( $atts ) {
	extract( shortcode_atts( array(
		'foo' => 'something',
		'bar' => 'something else',
	), $atts ) );

	ob_start();
	require_once( plugin_dir_path( __FILE__ ) . '../views/form-new-task.php' );
	return ob_get_clean();
}
add_shortcode( 'nervetask_new_task', 'nervetask_shortcode_new_task' );