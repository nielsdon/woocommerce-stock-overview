<?php
/*
Plugin Name: Woocommerce Stock Overview
Plugin URI: http://donninger.nl
Description: Give a clear, one-page, stock overview of all the products and variations and be able to do live changes to the stock.
Version: 0.1
Author: Donninger Consultancy
Author Email: niels@donninger.nl
License:

  Copyright 2011 Donninger Consultancy (niels@donninger.nl)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

class Woocommerce_Stock_Overview {

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const name = 'Woocommerce Stock Overview';
	const slug = 'woocommerce_stock_overview';
	
	/**
	 * Constructor
	 */
	function __construct() {
		//register an activation hook for the plugin
		register_activation_hook( __FILE__, array( &$this, 'install_woocommerce_stock_overview' ) );

		//Hook up to the init action
		add_action( 'init', array( &$this, 'init_woocommerce_stock_overview' ) );
	}
  
	/**
	 * Runs when the plugin is activated
	 */  
	function install_woocommerce_stock_overview() {
		// do not generate any output here
	}
  
	/**
	 * Runs when the plugin is initialized
	 */
	function init_woocommerce_stock_overview() {
		// Setup localization
		load_plugin_textdomain( self::slug, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		// Load JavaScript and stylesheets
		$this->register_scripts_and_styles();

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_submenu_page' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * TODO: Define custom functionality for your plugin here
		 *
		 * For more information: 
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'your_action_here', array( &$this, 'action_callback_method_name' ) );
		add_filter( 'your_filter_here', array( &$this, 'filter_callback_method_name' ) );    
		
		/*
		 * Always manage stock for all products
		 */
		global $wpdb;
		$prefix = $wpdb->prefix;
		$query = "UPDATE $wpdb->postmeta set meta_value='yes' WHERE meta_key='_manage_stock'";
	}
	
		/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_submenu_page() {
		/*
		 * Add a settings page for this plugin to the Woocommerce menu.
		 *
		 */
		$this->plugin_screen_hook_suffix = add_submenu_page(
			'woocommerce',
			__( 'Stock Overview', $this->plugin_slug ),
			__( 'Stock Overview', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);
	}
	
	public function display_plugin_admin_page() {
		if ( is_admin() ) {
			//this will run when in the WordPress admin
			include_once(dirname(__FILE__) . '/admin.php');
		} else {
			//this will run when on the frontend
		}	
	}

	function action_callback_method_name() {
		// TODO define your action method here
	}

	function filter_callback_method_name() {
		// TODO define your filter method here
	}
  
	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	private function register_scripts_and_styles() {
		if ( is_admin() ) {
			$this->load_file( self::slug . '-admin-script', '/js/admin.js', true );
			$this->load_file( self::slug . '-admin-style', '/css/admin.css' );
		} else {
			$this->load_file( self::slug . '-script', '/js/widget.js', true );
			$this->load_file( self::slug . '-style', '/css/widget.css' );
		} // end if/else
	} // end register_scripts_and_styles
	
	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 */
	private function load_file( $name, $file_path, $is_script = false ) {

		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') ); //depends on jquery
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} // end if
		} // end if

	} // end load_file
  
} // end class
new Woocommerce_Stock_Overview();

?>