<?php namespace Lamosty\Bedrock_Plugin_Control;
/*
Plugin Name: Bedrock plugin control
Description: Force-enables or force-disables plugins specified in .env. Based on Mark Jaquith's code.
Version: 0.1.0
License: GPL version 2 or any later version
Author: Rastislav Lamos
Author URI: https://lamosty.com/
*/

class Disable_Plugins {
	protected $plugins = array();
	protected $message = 'Disabled in this environment';

	/**
	 * Sets up the options filter, and optionally handles an array of plugins to disable
	 *
	 * @param array $disables Optional array of plugin filenames to disable
	 */
	public function __construct( Array $plugins, $message = null ) {
		// Handle what was passed in
		foreach ( $plugins as $plugin ) {
			$this->choose( $plugin );
		}

		if ( ! is_null( $message ) ) {
			$this->message = $message;
		}

		// Add the filter
		add_filter( 'option_active_plugins', array( $this, 'alter' ) );
	}

	/**
	 * Adds a filename to the list of plugins to disable
	 */
	public function choose( $file ) {
		$this->plugins[] = $file;
		add_filter( 'plugin_action_links_' . plugin_basename( $file ), array( $this, 'change_action_links' ) );
	}

	function change_action_links( $actions ) {
		unset( $actions['activate'] );
		unset( $actions['delete'] );
		$actions['disabled'] = '<i>' . esc_html( $this->message ) . '</i>';

		return $actions;
	}

	/**
	 * Hooks in to the option_active_plugins filter and does the disabling
	 *
	 * @param array $plugins WP-provided list of plugin filenames
	 *
	 * @return array The filtered array of plugin filenames
	 */
	public function alter( $plugins ) {
		if ( count( $this->plugins ) ) {
			foreach ( (array) $this->plugins as $plugin ) {
				$key = array_search( $plugin, $plugins );
				if ( false !== $key ) {
					do_action( 'deactivate_plugin', $plugin );
					unset( $plugins[ $key ] );
				}
			}
		}

		return $plugins;
	}
}

class Enable_Plugins extends Disable_Plugins {
	protected $message = 'Force-enabled';

	function change_action_links( $actions ) {
		unset( $actions['deactivate'] );
		unset( $actions['delete'] );
		$actions['enabled'] = '<i>' . esc_html( $this->message ) . '</i>';

		return $actions;
	}

	/**
	 * Hooks in to the option_active_plugins filter and does the enabling
	 *
	 * @param array $plugins WP-provided list of plugin filenames
	 *
	 * @return array The filtered array of plugin filenames
	 */
	public function alter( $plugins ) {
		if ( count( $this->plugins ) ) {
			foreach ( (array) $this->plugins as $plugin ) {
				$key = array_search( $plugin, $plugins );
				if ( false === $key ) {
					do_action( 'activate_plugin', $plugin );
					$plugins[] = $plugin;
				}
			}
		}

		return $plugins;
	}
}

function handle_bedrock_dev_plugins() {
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

	if ( ! defined( 'WP_ENV' ) || empty( $BEDROCK_DEV_PLUGINS ) ) {
		return;
	}

	if ( WP_ENV == 'development' ) {
		new Enable_Plugins( $BEDROCK_DEV_PLUGINS, 'Enabled for development' );

	} elseif ( WP_ENV == 'production' ) {
		new Disable_Plugins( $BEDROCK_DEV_PLUGINS, 'Enabled for development' );
	}
}

handle_bedrock_dev_plugins();
