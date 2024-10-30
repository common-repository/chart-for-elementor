<?php
/*
Plugin Name: Chart for Elementor
Plugin URI: https://wpocean.com/wp/plugins/chart
Description: This plugin is an add-on for the Elementor Page Builder, providing a simple and clean solution for integrating charts.
Version: 2.0.0
Author: imranhosain
Author URI: https://wpocean.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
Text Domain: elementorchart
Domain Path: /languages/
*/


use \Elementor\Plugin as Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	die( __( "Direct Access is not allowed", 'elementorchart' ) );
}

// Plugin URL
define( 'ELEMENTORCHART_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

final class ElementorChartExtension {

	const VERSION = "1.0.0";
	const MINIMUM_ELEMENTOR_VERSION = "2.0.0";
	const MINIMUM_PHP_VERSION = "7.4";

	private static $_instance = null;

	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	public function init() {
		load_plugin_textdomain( 'elementor-chart-extension' );

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );

			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );

			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );

			return;
		}

		add_action( 'elementor/widgets/register', [ $this, 'register' ] );

		add_action( "elementor/elements/categories_registered", [ $this, 'register_new_category' ] );

		add_action( "elementor/frontend/after_enqueue_styles", [ $this, 'elementorchart_assets_styles' ] );
		add_action( "elementor/frontend/after_enqueue_scripts", [ $this, 'elementorchart_assets_scripts' ] );
		add_action( "wp_enqueue_scripts", [ $this, 'elementorchart_frontent_scripts' ] );

	}

	function elementorchart_assets_scripts(){
		wp_enqueue_script("chart",plugins_url("/assets/js/chart.plugin.js",__FILE__),array('jquery'),'1.0',true);
		wp_enqueue_script("helper",plugins_url("/assets/js/scripts.js",__FILE__),array('jquery','chart'),time(),true);
	}

	function elementorchart_frontent_scripts(){
		wp_enqueue_script("chart-plugin",plugins_url("/assets/js/chart.plugin.js",__FILE__),array('jquery'),'1.0',false);
	}


	function elementorchart_assets_styles() {
		wp_enqueue_style("style-css",plugins_url("/assets/css/style.css",__FILE__));
	}

	public function register_new_category( $manager ) {
		$manager->add_category( 'chartcategory', [
			'title' => __( 'Chart Category', 'elementorchart' ),
			'icon'  => 'fa fa-chart'
		] );
	}

	public function register() {
		require_once( __DIR__ . '/widgets/elementor-chart-widget.php' );

		// Register widget
		// Plugin::instance()->widgets_manager->register_widget_type( new \Elementor_Chart_Widget() );

	}


	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
		/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementorchart' ),
			'<strong>' . esc_html__( 'Elementor Chart Extension', 'elementorchart' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'elementorchart' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementorchart' ),
			'<strong>' . esc_html__( 'Elementor Chart Extension', 'elementorchart' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementorchart' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementorchart' ),
			'<strong>' . esc_html__( 'Elementor Chart Extension', 'elementorchart' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementorchart' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );


	}

	public function includes() {
	}

}

ElementorChartExtension::instance();
