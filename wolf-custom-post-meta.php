<?php
/**
 * Plugin Name: Wolf Custom Post Meta
 * Plugin URI: https://wlfthm.es/wolf-custom-post-meta
 * Description: Custom post meta (views, likes, reading time).
 * Version: 1.0.2
 * Author: WolfThemes
 * Author URI: https://wolfthemes.com
 * Requires at least: 5.0
 * Tested up to: 5.5
 *
 * Text Domain: wolf-custom-post-meta
 * Domain Path: /languages/
 *
 * @package WolfCustomPostMeta
 * @category Core
 * @author WolfThemes
 *
 * Verified customers who have purchased a premium theme at https://wlfthm.es/tf/
 * will have access to support for this plugin in the forums
 * https://wlfthm.es/help/
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wolf_Custom_Post_Meta' ) ) {
	/**
	 * Main Wolf_Custom_Post_Meta Class
	 *
	 * Contains the main functions for Wolf_Custom_Post_Meta
	 *
	 * @class Wolf_Custom_Post_Meta
	 * @version 1.0.2
	 * @since 1.0.0
	 */
	class Wolf_Custom_Post_Meta {

		/**
		 * @var string
		 */
		public $version = '1.0.2';

		/**
		 * @var Custom Post Meta The single instance of the class
		 */
		protected static $_instance = null;



		/**
		 * Main Custom Post Meta Instance
		 *
		 * Ensures only one instance of Custom Post Meta is loaded or can be loaded.
		 *
		 * @static
		 * @see WCPM()
		 * @return Custom Post Meta - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Custom Post Meta Constructor.
		 */
		public function __construct() {

			$this->define_constants();
			$this->includes();
			$this->init_hooks();

			do_action( 'wolf_custom_post_meta_loaded' );
		}

		/**
		 * Hook into actions and filters
		 */
		private function init_hooks() {

			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			add_action( 'init', array( $this, 'init' ), 0 );

			add_action( 'admin_init', array( $this, 'plugin_update' ) );
		}

		/**
		 * Activation function
		 */
		public function activate() {

			do_action( 'wolf_custom_post_meta_activated' );
		}

		/**
		 * Define WPB Constants
		 */
		private function define_constants() {

			$constants = array(
				'WCPM_DEV' => false,
				'WCPM_DIR' => $this->plugin_path(),
				'WCPM_URI' => $this->plugin_url(),
				'WCPM_CSS' => $this->plugin_url() . '/assets/css',
				'WCPM_JS' => $this->plugin_url() . '/assets/js',
				'WCPM_IMG' => $this->plugin_url() . '/assets/img',
				'WCPM_SLUG' => plugin_basename( dirname( __FILE__ ) ),
				'WCPM_PATH' => plugin_basename( __FILE__ ),
				'WCPM_VERSION' => $this->version,
				'WCPM_DOC_URI' => 'https://docs.wolfthemes.com/documentation/plugins/' . plugin_basename( dirname( __FILE__ ) ),
				'WCPM_WOLF_DOMAIN' => 'wolfthemes.com',
			);

			foreach ( $constants as $name => $value ) {
				$this->define( $name, $value );
			}
		}

		/**
		 * Define constant if not already set
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * What type of request is this?
		 * string $type ajax, frontend or admin
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {

			include_once( 'inc/wcpm-core-functions.php' );

			if ( $this->is_request( 'admin' ) ) {
				include_once( 'inc/admin/class-wcpm-admin.php' );
			}

			if ( $this->is_request( 'ajax' ) ) {
				include_once( 'inc/ajax/wcpm-ajax-functions.php' );
			}

			if ( $this->is_request( 'frontend' ) ) {
				include_once( 'inc/frontend/wcpm-frontend-functions.php' );
				include_once( 'inc/frontend/class-wcpm-shortcodes.php' );
			}
		}

		/**
		 * Init Custom Post Meta when WordPress Initialises.
		 */
		public function init() {
			// Before init action
			do_action( 'before_wolf_custom_post_meta_init' );

			// Set up localisation
			$this->load_plugin_textdomain();

			// Init action
			do_action( 'wolf_custom_post_meta_init' );
		}

		/**
		 * Loads the plugin text domain for translation
		 */
		public function load_plugin_textdomain() {

			$domain = 'wolf-custom-post-meta';
			$locale = apply_filters( 'wolf-custom-post-meta', get_locale(), $domain );
			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Get the plugin url.
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Plugin update
		 */
		public function plugin_update() {

			if ( ! class_exists( 'WP_GitHub_Updater' ) ) {
				include_once 'inc/admin/updater.php';
			}

			$repo = 'wolfthemes/wolf-custom-post-meta';

			$config = array(
				'slug' => plugin_basename( __FILE__ ),
				'proper_folder_name' => 'wolf-custom-post-meta',
				'api_url' => 'https://api.github.com/repos/' . $repo . '',
				'raw_url' => 'https://raw.github.com/' . $repo . '/master/',
				'github_url' => 'https://github.com/' . $repo . '',
				'zip_url' => 'https://github.com/' . $repo . '/archive/master.zip',
				'sslverify' => true,
				'requires' => '5.0',
				'tested' => '5.5',
				'readme' => 'README.md',
				'access_token' => '',
			);

			new WP_GitHub_Updater( $config );
		}
	}
}
/**
 * Returns the main instance of WCPM to prevent the need to use globals.
 *
 * @return Wolf_Recipes
 */
function WCPM() {
	return Wolf_Custom_Post_Meta::instance();
}

WCPM(); // Go
