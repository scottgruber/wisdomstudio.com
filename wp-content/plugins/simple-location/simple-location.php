<?php
/**
 * Plugin Name: Simple Location
 * Plugin URI: https://wordpress.org/plugins/simple-location/
 * Description: Adds Location to WordPress
 * Version: 4.0.6
 * Author: David Shanske
 * Author URI: https://david.shanske.com
 * Text Domain: simple-location
 * Domain Path:  /languages
 */

add_action( 'plugins_loaded', array( 'Simple_Location_Plugin', 'init' ) );

// Activation and Deactivation Hooks
register_activation_hook( __FILE__, array( 'Simple_Location_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Simple_Location_Plugin', 'deactivate' ) );

if ( ! defined( 'SLOC_PER_PAGE' ) ) {
			define( 'SLOC_PER_PAGE', 100 );
}



class Simple_Location_Plugin {
	public static $version = '4.0.6';

	public static function activate() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-geo-data.php';
		WP_Geo_Data::rewrite();
		flush_rewrite_rules();
	}

	public static function deactivate() {
		flush_rewrite_rules();
	}

	public static function load( $files, $dir = 'includes/' ) {
		if ( empty( $files ) ) {
			return;
		}
		$path = plugin_dir_path( __FILE__ ) . $dir;
		foreach ( $files as $file ) {
			if ( file_exists( $path . $file ) ) {
				require_once $path . $file;
			} else {
				error_log( $path . $file );
			}
		}
	}

	public static function init() {

		load_plugin_textdomain( 'simple-location', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Load stylesheets.
		add_action( 'wp_enqueue_scripts', array( 'Simple_Location_Plugin', 'style_load' ) );

		// Settings Link
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( 'Simple_Location_Plugin', 'settings_link' ) );

		// Add Privacy Policy
		add_action( 'admin_init', array( 'Simple_Location_Plugin', 'privacy_declaration' ) );

		// Core Load Files
		$core = array(
			'class-geo-data.php', // Register Metadata Functions
			'class-venue-taxonomy.php', // Venue Taxonomy
			'class-sloc-provider.php', // Base Provider Class
			'class-map-provider.php', // Map Provider Class
			'class-geo-provider.php', // Geo Provider Class
			'class-weather-provider.php', // Weather Provider Class
			'class-location-provider.php', // Location Provider Class
			'class-sloc-weather-widget.php', // Weather Widget
			'class-sloc-station-widget.php', // Weather Station Widget
			'class-sloc-airport-widget.php', // Airport Weather Widget
			'class-sloc-lastseen-widget.php', // Last Location Seen Widget
			'class-rest-geo.php', // REST endpoint for Geo
			'class-loc-config.php', // Configuration and Settings Page
			'class-loc-metabox.php', // Location Metabox
			'class-loc-view.php', // Location View functionality
			'class-timezone-result.php',
			'class-astronomical-calculator.php', // Calculates sunrise sunset etc
			'class-location-plugins.php',
			'class-location-zones.php',
			'class-loc-timezone.php',
			'class-post-timezone.php',
			'class-airport-location.php',
			'compat-functions.php',
		);

		// Load Core Files
		self::load( $core );
		add_action( 'widgets_init', array( 'Simple_Location_Plugin', 'widgets_init' ) );

		$libraries = array(
			'Polyline.php', // Polyline Encoding Library
		);
		self::load( $libraries, 'lib/' );

		// Load Providers
		$providers = array(
			'class-location-provider-dummy.php', // Dummy Location Provider
			'class-location-provider-airport.php', // Airport Location Provider
			'class-location-provider-compass.php', // Compass https://github.com/aaronpk/Compass Location Provder
			'class-weather-provider-openweathermap.php', // Open Weather Map
			'class-weather-provider-darksky.php', // Dark Sky
			'class-weather-provider-nwsus.php', // National Weather Service (US)
			'class-weather-provider-weatherstack.php', // weatherstack.com
			'class-weather-provider-weatherbit.php', // weatherbit.com
			'class-weather-provider-here.php', // HERE
			'class-weather-provider-metoffice.php', // Met Office
			'class-map-provider-mapbox.php', // MapBox
			'class-map-provider-google.php', // Google
			'class-map-provider-bing.php', // Bing
			'class-map-provider-mapquest.php', // MapQuest
			'class-map-provider-here.php', // HERE
			'class-map-provider-wikimedia.php', // Wikimedia
			'class-map-provider-locationiq.php', // LocationIQ
			'class-geo-provider-nominatim.php', // Nominatim
			'class-geo-provider-mapquest.php', /// MapQuest Nominatim
			'class-geo-provider-google.php', // Google
			'class-geo-provider-here.php', // HERE
			'class-geo-provider-bing.php', // Bing
			'class-geo-provider-locationiq.php', // LocationIQ
			'class-geo-provider-geonames.php', // Geonames
		);
		self::load( $providers );
	}

	public static function widgets_init() {
		register_widget( 'Sloc_Weather_Widget' );
		register_widget( 'Sloc_Station_Widget' );
		register_widget( 'Sloc_Airport_Widget' );
		register_widget( 'Sloc_Lastseen_Widget' );
	}

	/** Adds link to Plugin Page for Options Page.
	 *
	 * @param array $links Array of Existing Links.
	 * @return array Modified Links.
	 */
	public static function settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=simloc">' . __( 'Settings', 'simple-location' ) . '</a>';
		$links[]       = $settings_link;
		return $links;
	}

	/**
	 * Loads the Stylesheet for the Plugin.
	 */
	public static function style_load() {
		wp_enqueue_style( 'simple-location', plugin_dir_url( __FILE__ ) . 'css/location.min.css', array(), self::$version );
	}

	public static function privacy_declaration() {
		if ( function_exists( 'wp_add_privacy_policy_content' ) ) {
			$content = __(
				'Location and weather data is optionally stored for all posts, attachments, and comments. Location data is extracted from uploaded images along with other metadata. This
				data can be removed prior to uploading if tyou do not wish this to be stored. There are options to display this information or hide it.',
				'simple-location'
			);
			wp_add_privacy_policy_content(
				'Simple Location',
				wp_kses_post( wpautop( $content, false ) )
			);
		}
	}

}

if ( ! function_exists( 'ifset' ) ) {
	function ifset( &$var, $default = false ) {
		return isset( $var ) ? $var : $default;
	}
}

if ( ! function_exists( 'ifset_round' ) ) {
	function ifset_round( &$var, $precision = 0, $default = false ) {
		$return = ifset( $var, $default );
		if ( is_float( $return ) ) {
			return round( $return, $precision );
		}
		return $return;
	}
}

