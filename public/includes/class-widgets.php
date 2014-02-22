<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * NerveTask Widget base
 */
class NerveTask_Widget extends WP_Widget {

	public $widget_cssclass;
	public $widget_description;
	public $widget_id;
	public $widget_name;
	public $settings;

	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		$this->WP_Widget( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * get_cached_widget function.
	 */
	function get_cached_widget( $args ) {
		$cache = wp_cache_get( $this->widget_id, 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget
	 */
	public function cache_widget( $args, $content ) {
		$cache[ $args['widget_id'] ] = $content;

		wp_cache_set( $this->widget_id, $cache, 'widget' );
	}

	/**
	 * Flush the cache
	 * @return [type]
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->widget_id, 'widget' );
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( ! $this->settings )
			return $instance;

		foreach ( $this->settings as $key => $setting ) {
			$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
		}

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		if ( ! $this->settings )
			return;

		foreach ( $this->settings as $key => $setting ) {

			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			switch ( $setting['type'] ) {
				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case 'number' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case 'checkbox' :
					?>
					<p>
						<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="checkbox" value="1" <?php checked( '1', $value ); ?> />
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					</p>
					<?php
				break;
			}
		}
	}
}

/**
 * New Task Widget
 */
class NerveTask_Widget_New_Task extends NerveTask_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'nervetask widget_new_task';
		$this->widget_description = __( 'Display a form to create a new task.', 'nervetask' );
		$this->widget_id          = 'widget_new_task';
		$this->widget_name        = __( 'New Task Form', 'nervetask' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => __( 'New Task', 'nervetask' ),
				'label' => __( 'Title', 'nervetask' )
			),
			'display_title' => array(
				'type'  => 'checkbox',
				'std'   => __( 1, 'nervetask' ),
				'label' => __( 'Display task title field', 'nervetask' )
			),
			'display_content' => array(
				'type'  => 'checkbox',
				'std'   => __( 1, 'nervetask' ),
				'label' => __( 'Display task content field', 'nervetask' )
			),
			'display_category' => array(
				'type'  => 'checkbox',
				'std'   => __( 0, 'nervetask' ),
				'label' => __( 'Display task category field', 'nervetask' )
			),
			'display_priority' => array(
				'type'  => 'checkbox',
				'std'   => __( 0, 'nervetask' ),
				'label' => __( 'Display task priority field', 'nervetask' )
			),
			'display_users' => array(
				'type'  => 'checkbox',
				'std'   => __( 0, 'nervetask' ),
				'label' => __( 'Display task users field', 'nervetask' )
			)
		);
		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		if ( $this->get_cached_widget( $args ) )
			return;

		ob_start();

		extract( $args );

		$title 		= apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$atts = array(
			'title'		=> absint( $instance['display_title'] ),
			'content'	=> absint( $instance['display_content'] ),
			'category'	=> absint( $instance['display_category'] ),
			'priority'	=> absint( $instance['display_priority'] ),
			'users'		=> absint( $instance['display_users'] )
		);

		nervetask_new_task_form( $atts );

		$content = ob_get_clean();

		echo $content;

		$this->cache_widget( $args, $content );
	}
}

function register_nervetask_widgets() {
    register_widget( 'NerveTask_Widget_New_Task' );
}
add_action( 'widgets_init', 'register_nervetask_widgets' );
