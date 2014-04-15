<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class NerveTask_Status_Colors {

	private static $instance;

	public static function instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		$this->setup_actions();
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

	private function setup_actions() {
		add_filter( 'nervetask_settings', array( $this, 'nervetask_settings' ) );
		add_action( 'wp_head', array( $this, 'output_colors' ) );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'colorpickers' ) );
			add_action( 'admin_footer', array( $this, 'colorpickersjs' ) );
		}
	}

	public function nervetask_settings( $settings ) {
		$settings[ 'status_colors' ] = array(
			__( 'Status Colors', 'nervetask_colors' ),
			$this->create_options()
		);

		return $settings;
	}

	private function create_options() {
		$terms   = get_terms( 'nervetask_status', array( 'hide_empty' => false ) );
		$options = array();

		$options[] = array(
			'name' 		  => 'nervetask_status_what_color',
			'std' 		  => 'background',
			'placeholder' => '',
			'label' 	  => __( 'What', 'nervetask_colors' ),
			'desc'        => __( 'Should these colors be applied to the text color, or background color?', 'nervetask_colors' ),
			'type'        => 'select',
			'options'     => array(
				'background' => __( 'Background', 'nervetask_colors' )
			)
		);

		foreach ( $terms as $term ) {
			$options[] = array(
				'name' 		  => 'nervetask_status_' . $term->term_id . '_color',
				'std' 		  => '#cccccc',
				'placeholder' => '#',
				'label' 	  => '<strong>' . $term->name . '</strong>',
				'desc'		  => __( 'Hex value for the color of this task status.', 'nervetask_colors' ),
				'attributes'  => array(
					'data-default-color' => '#fff',
					'data-type'          => 'colorpicker'
				)
			);
		}

		return $options;
	}

	public function output_colors() {
		$terms   = get_terms( 'nervetask_status', array( 'hide_empty' => false ) );

		echo "<style id='nervetask_colors'>\n";

		foreach ( $terms as $term ) {
			$what = 'background' == get_option( 'nervetask_status_what_color' ) ? 'background-color' : 'color';

			printf( ".nervetask-status.nervetask-status-term-%s, .nervetask-status.nervetask-status-%s { background: %s; } \n", $term->term_id, $term->slug, get_option( 'nervetask_status_' . $term->term_id . '_color', '#fff' ) );
		}

		echo "</style>\n";
	}

	public function colorpickers( $hook ) {
		$screen = get_current_screen();

		if ( 'nervetask_page_nervetask-settings' != $screen->id )
			return;

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	public function colorpickersjs() {
		$screen = get_current_screen();

		if ( 'nervetask_page_nervetask-settings' != $screen->id )
			return;
		?>
			<script>
				jQuery(document).ready(function($){
					$( 'input[data-type="colorpicker"]' ).wpColorPicker();
				});
			</script>
		<?php
	}
}
