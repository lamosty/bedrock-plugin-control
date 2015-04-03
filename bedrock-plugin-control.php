<?php namespace Lamosty\Bedrock_Plugin_Control;
/*
Plugin Name: Bedrock plugin control
Description: Force-enables or force-disables plugins specified in config/application.php.
Version: 1.0.0
License: GPL version 2 or any later version
Author: Rastislav Lamos
Author URI: https://lamosty.com/
*/

class Bedrock_Plugin_Control {
	protected $plugins = [ ];
	protected $wp_env;

	protected function __construct( array $plugins ) {
		$this->plugins = $plugins;
		$this->wp_env = WP_ENV;

		$this->handle_dev_plugins();
	}

	public static function init( array $plugins = [ ] ) {
		static $instance = null;

		if ( ! $instance ) {

			if ( empty( $plugins ) || ! defined( 'WP_ENV' ) ) {
				return false;
			}

			$instance = new Bedrock_Plugin_Control( $plugins );
		}

		return $instance;
	}

	private function handle_dev_plugins() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( $this->wp_env == 'development' ) {
			foreach ( $this->plugins as $plugin ) {
				activate_plugin( $plugin );
			}

		} elseif ( $this->wp_env == 'production' ) {
			deactivate_plugins( $this->plugins );
		}
	}
}

/**
 * Add this variable to config/application.php.
 *
 * Plugins which get force-enabled in development environment.
 * Include them in your composer.json "require-dev" so they get installed only on dev machine.
 *
 * Specify relative path to plugin's main PHP file.
 *
 * Example:
 *
 * $BEDROCK_DEV_PLUGINS = array(
 * 'query-monitor/query-monitor.php',
 * 'debug-bar-console/debug-bar-console.php');
 */

global $BEDROCK_DEV_PLUGINS;

Bedrock_Plugin_Control::init( $BEDROCK_DEV_PLUGINS );
