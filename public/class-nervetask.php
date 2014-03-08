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

add_action( 'plugins_loaded', array( 'NerveTask_Task', 'get_instance' ) );

/**
 * @package NerveTask
 * @author  Patrick Daly <patrick@developdaly.com>
 */
class NerveTask {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   0.1.0
	 *
	 * @var     string
	 */
	const VERSION = '0.1.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'nervetask';

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

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Register post types and taxonomies
		add_action( 'init', array( $this, 'register' ) );

		// Add NerveTask slugs to the the body and post classes
		add_filter( 'post_class', array( $this, 'body_class' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    0.1.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
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
	 * Fired when the plugin is activated.
	 *
	 * @since    0.1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    0.1.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    0.1.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    0.1.0
	 */
	private static function single_activate() {
		
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
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    0.1.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		wp_localize_script( $this->plugin_slug . '-plugin-script', 'nervetask', array(
			'ajaxurl'	=> admin_url( 'admin-ajax.php' )
		) );
	}

	/**
	 * Register custom post types and taxonomies.
	 *
	 * @since    0.1.0
	 */
	public function register() {

		$task_labels = array(
			'name' => _x( 'Tasks', 'nervetask' ),
			'singular_name' => _x( 'Task', 'nervetask' ),
			'add_new' => _x( 'Add New', 'nervetask' ),
			'add_new_item' => _x( 'Add New Task', 'nervetask' ),
			'edit_item' => _x( 'Edit Task', 'nervetask' ),
			'new_item' => _x( 'New Task', 'nervetask' ),
			'view_item' => _x( 'View Task', 'nervetask' ),
			'search_items' => _x( 'Search Tasks', 'nervetask' ),
			'not_found' => _x( 'No tasks found', 'nervetask' ),
			'not_found_in_trash' => _x( 'No tasks found in Trash', 'nervetask' ),
			'parent_item_colon' => _x( 'Parent task:', 'nervetask' ),
			'menu_name' => _x( 'Tasks', 'nervetask' )
		);
		$task_args = array(
			'labels' => $task_labels,
			'hierarchical' => true,
			'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'discussion' ),
			'taxonomies' => array( 'category', 'post_tag', 'nervetask_status', 'nervetask_priority' ),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_position' => 5,
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => array(
				'slug' => 'tasks'
			),
			'capability_type' => 'post'
		);

		register_post_type( 'nervetask', $task_args );

		$status_labels = array(
			'name' => _x( 'Statuses', 'nervetask' ),
			'singular_name' => _x( 'Status', 'nervetask' ),
			'search_items' => _x( 'Search Statuses', 'nervetask' ),
			'popular_items' => _x( 'Popular Statuses', 'nervetask' ),
			'all_items' => _x( 'All Statuses', 'nervetask' ),
			'parent_item' => _x( 'Parent Status', 'nervetask' ),
			'parent_item_colon' => _x( 'Parent Status:', 'nervetask' ),
			'edit_item' => _x( 'Edit Status', 'nervetask' ),
			'update_item' => _x( 'Update Status', 'nervetask' ),
			'add_new_item' => _x( 'Add New Status', 'nervetask' ),
			'new_item_name' => _x( 'New Status', 'nervetask' ),
			'separate_items_with_commas' => _x( 'Separate statuses with commas', 'nervetask' ),
			'add_or_remove_items' => _x( 'Add or remove statuses', 'nervetask' ),
			'choose_from_most_used' => _x( 'Choose from the most used statuses', 'nervetask' ),
			'menu_name' => _x( 'Statuses', 'nervetask' )
		);

		$status_args = array(
			'labels' => $status_labels,
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => false,
			'rewrite' => array(
				'slug' => 'statuses',
				'with_front' => true,
				'hierarchical' => false
			),
			'query_var' => true
		);

		$priority_labels = array(
			'name' => _x( 'Priorities', 'nervetask' ),
			'singular_name' => _x( 'Priority', 'nervetask' ),
			'search_items' => _x( 'Search Priorities', 'nervetask' ),
			'popular_items' => _x( 'Popular Priorities', 'nervetask' ),
			'all_items' => _x( 'All Priorities', 'nervetask' ),
			'parent_item' => _x( 'Parent Priority', 'nervetask' ),
			'parent_item_colon' => _x( 'Parent Priority:', 'nervetask' ),
			'edit_item' => _x( 'Edit Priority', 'nervetask' ),
			'update_item' => _x( 'Update Priority', 'nervetask' ),
			'add_new_item' => _x( 'Add New Priority', 'nervetask' ),
			'new_item_name' => _x( 'New Priority', 'nervetask' ),
			'separate_items_with_commas' => _x( 'Separate priorities with commas', 'nervetask' ),
			'add_or_remove_items' => _x( 'Add or remove priorities', 'nervetask' ),
			'choose_from_most_used' => _x( 'Choose from the most used priorities', 'nervetask' ),
			'menu_name' => _x( 'Priorities', 'nervetask' )
		);

		$priority_args = array(
			'labels' => $priority_labels,
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => false,
			'rewrite' => array(
				'slug' => 'priority',
				'with_front' => true,
				'hierarchical' => false
			),
			'query_var' => true
		);

		$category_labels = array(
			'name' => _x( 'Task Categories', 'nervetask' ),
			'singular_name' => _x( 'Task Category', 'nervetask' ),
			'search_items' => _x( 'Search Task Categories', 'nervetask' ),
			'popular_items' => _x( 'Popular Task Categories', 'nervetask' ),
			'all_items' => _x( 'All Task Categories', 'nervetask' ),
			'parent_item' => _x( 'Parent Task Category', 'nervetask' ),
			'parent_item_colon' => _x( 'Parent Task Category:', 'nervetask' ),
			'edit_item' => _x( 'Edit Task Category', 'nervetask' ),
			'update_item' => _x( 'Update Task Category', 'nervetask' ),
			'add_new_item' => _x( 'Add New Task Category', 'nervetask' ),
			'new_item_name' => _x( 'New Task Category', 'nervetask' ),
			'separate_items_with_commas' => _x( 'Separate task categories with commas', 'nervetask' ),
			'add_or_remove_items' => _x( 'Add or remove task categories', 'nervetask' ),
			'choose_from_most_used' => _x( 'Choose from the most used task categories', 'nervetask' ),
			'menu_name' => _x( 'Task Categories', 'nervetask' )
		);

		$category_args = array(
			'labels' => $category_labels,
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => false,
			'rewrite' => array(
				'slug' => 'category',
				'with_front' => true,
				'hierarchical' => false
			),
			'query_var' => true
		);

		register_taxonomy( 'nervetask_status',		array( 'nervetask' ), $status_args );
		register_taxonomy( 'nervetask_priority',	array( 'nervetask' ), $priority_args );
		register_taxonomy( 'nervetask_category',	array( 'nervetask' ), $category_args );

		if( function_exists( 'p2p_register_connection_type' ) ) {

			p2p_register_connection_type(
				array(
				  'name'	=> 'nervetask_to_user',
				  'from'	=> 'nervetask',
				  'to'		=> 'user'
				)
			);
		}
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    0.1.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    0.1.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

	public function body_class( $classes ) {
		global $post;

		if( !$post ) {
			return;
		}

		$statuses = get_the_terms($post -> ID, 'nervetask_status');
		if( $statuses ) {
			foreach( $statuses as $status) {
				$classes[] = 'status-'. $status->slug;
			}
		}

		$priorities = get_the_terms($post -> ID, 'nervetask_priority');
		if( $priorities ) {
			foreach( $priorities as $priority) {
				$classes[] = 'priority-'. $priority->slug;
			}
		}

		return $classes;

	}

}
