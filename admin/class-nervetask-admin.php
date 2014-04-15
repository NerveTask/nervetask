<?php
/**
 * Plugin Name.
 *
 * @package   NerveTask_Admin
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://nervetask.com
 * @copyright 2014 NerveTask
 */

/**
 * Require or suggest other plugins.
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-tgm-plugin-activation.php' );


/**
 * @package NerveTask_Admin
 * @author  Patrick Daly <patrick@developdaly.com>
 */
class NerveTask_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     0.1.0
	 */
	private function __construct() {

		$plugin = NerveTask::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		
		add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

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
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.1.0
	 */
	public function add_plugin_admin_menu() {
		
		$this->settings_page = new NerveTask_Settings();
		
		add_submenu_page( 'edit.php?post_type=nervetask', __( 'Settings', 'nervetask' ), __( 'Settings', 'nervetask' ), 'manage_options', 'nervetask-settings', array( $this->settings_page, 'output' ) );

		if ( apply_filters( 'nervetask_show_addons_page', true ) ) {
			add_submenu_page(  'edit.php?post_type=nervetask', __( 'NerveTask Add-ons', 'nervetask' ),  __( 'Add-ons', 'nervetask' ) , 'manage_options', 'nervetask-addons', array( $this, 'addons_page' ) );
		}

	}
	
	/**
	 * Output addons page
	 */
	public function addons_page() {
		$addons = include( 'includes/class-addons.php' );
		$addons->output();
	}
	
	/**
	 * Register the required plugins for this theme.
	 */
	function register_required_plugins() {

		$plugins = array(

			array(
				'name' 		=> 'Posts 2 Posts (used for task assignment to users)',
				'slug' 		=> 'posts-to-posts',
				'required' 	=> false,
			),
			array(
				'name' 		=> 'Email (used for sending email notifications for tasks)',
				'slug' 		=> 'email',
				'required' 	=> false,
			)

		);

		$plugin_text_domain = 'nervetask';

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'domain'       		=> $plugin_text_domain,         // Text domain - likely want to be the same as your plugin.
			'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
			'menu'         		=> 'install-required-plugins', 	// Menu slug
			'has_notices'      	=> true,                       	// Show admin notices or not
			'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
			'message' 			=> '',							// Message to output right before the plugins table
			'strings'      		=> array(
				'page_title'                       			=> __( 'Install Required Plugins', $plugin_text_domain ),
				'menu_title'                       			=> __( 'Install Plugins', $plugin_text_domain ),
				'installing'                       			=> __( 'Installing Plugin: %s', $plugin_text_domain ), // %1$s = plugin name
				'oops'                             			=> __( 'Something went wrong with the plugin API.', $plugin_text_domain ),
				'notice_can_install_required'     			=> _n_noop( 'This plugin requires the following plugin: %1$s.', 'This plugin requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
				'notice_can_install_recommended'			=> _n_noop( 'This plugin recommends the following plugin: %1$s.', 'This plugin recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
				'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
				'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
				'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this plugin: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this plugin: %1$s.' ), // %1$s = plugin name(s)
				'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
				'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
				'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
				'return'                           			=> __( 'Return to Required Plugins Installer', $plugin_text_domain ),
				'plugin_activated'                 			=> __( 'Plugin activated successfully.', $plugin_text_domain ),
				'complete' 									=> __( 'All plugins installed and activated successfully. %s', $plugin_text_domain ), // %1$s = dashboard link
				'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
			)
		);

		tgmpa( $plugins, $config );

	}

}
