<?php
/*
Plugin Name: Standard Advanced Google Analytics
Plugin URI: http://github.com/eightbit/standard-advanced-google-analytics/
Description: Introduces support for "Multiple top-level domains" and "Display Advertiser Support" into the Standard dashboard.
Version: 1.0
Author: 8BIT
License:

  Copyright 2013 8BIT, LLC (info@8bit.io)

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

if( ! defined( 'STANDARD_ADVANCED_GOOGLE_ANALYTICS' ) ) {
	define( 'STANDARD_ADVANCED_GOOGLE_ANALYTICS', '1.0' );
} // end if

/**
 * @version		1.0
 * @since		3.3
 */
class Standard_Advanced_Google_Analytics {
	
	/*--------------------------------------------------------*
	 * Attributes
	 *--------------------------------------------------------*/

	 /** Static property to hold our singleton instance */
	 private static $instance = null;

	/*--------------------------------------------------------*
	 * Constructor
	 *--------------------------------------------------------*/

	/**
	 * Initializes the widget's classname, description, and JavaScripts.
	 */  	
	 public function get_instance() {
		 
		 // Get an instance of the 
		 if( null == self::$instance ) {
			 self::$instance = new self;
		 } // end if
		 
		 return self::$instance;
		 
	 } // end get_instance

	/**
	 * Initializes the widget's classname, description, and more
	 */  		
	 private function __construct() {
		
		// Load plugin textdomain
		add_action( 'init', array( $this, 'plugin_textdomain' ) );
		
		// Introduce the administration JavaScript
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		
		// Introduce the new admin fields
		add_action( 'admin_init', array( $this, 'advanced_google_analytics' ) );
		
		add_action( 'admin_notices', array( $this, 'plugin_activation' ) ) ;
		
	 } // end constructor
	 
	/*--------------------------------------------------------*
	 * Functions
	 *--------------------------------------------------------*/
	 
	 /**
	  * Defines the plugin textdomain.
	  */
	 public function plugin_textdomain() {
		 
		$domain = 'standard-advanced-google-analytics';
		$locale = apply_filters( 'standard-advanced-google-analytics', get_locale(), $domain );
		
        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		 
	} // end plugin_textdomain
	
	/**
	 * Loads the JavaScript responsible for placing the Advanced Google Analytics options below the default
	 * option that comes included with Standard.
	 */
	public function admin_scripts() {
		
		$screen = get_current_screen();
		if ('toplevel_page_theme_options' == $screen->id ) {
			wp_enqueue_script( 'standard-advanced-google-analytics', plugins_url( 'js/admin.min.js', __FILE__ ), array( 'jquery' ), STANDARD_ADVANCED_GOOGLE_ANALYTICS );
		} // end if
		
	} // end admin_scripts
	
	/**
	 * Saves the version of the plugin to the database and displays an activation notice on where users 
	 * can access the new options.
	 */
	public function plugin_activation() {
		
		if( STANDARD_ADVANCED_GOOGLE_ANALYTICS != get_option( 'standard_advanced_google_analytics' ) ) {
		
			add_option( 'standard_advanced_google_analytics', STANDARD_ADVANCED_GOOGLE_ANALYTICS );
			
			$html = '<div class="updated">';
				$html .= '<p>';
					$html .= __( 'The Advanced Google Analytics are available <a href="admin.php?page=theme_options&tab=standard_theme_global_options">on this page</a>.', 'standard' );
				$html .= '</p>';
			$html .= '</div><!-- /.updated -->';
			
			echo $html;
			
		} // end if
		
	} // end plugin_activation
	
	/**
	 * Deletes the option from the database. Optionally displays an error message if there is a
	 * problem deleting the option.
	 */
	public static function plugin_deactivation() {
		
		// Display an error message if the option isn't properly deleted.			
		if( false == delete_option( 'standard_advanced_google_analytics' ) ) {
		
			$html = '<div class="error">';
				$html .= '<p>';
					$html .= __( 'There was a problem deactivating the Advanced Google Analytics Plugin. Please try again.', 'standard' );
				$html .= '</p>';
			$html .= '</div><!-- /.updated -->';
			
			echo $html;
			
		} // end if/else

	} // end plugin_deactivation
	
	/*--------------------------------------------------------*
	 * Settings API
	 *--------------------------------------------------------*/
	
	/**
	 * Adds the two new settings fields to the Standard Theme General Options.
	 */
	public function advanced_google_analytics() {
		
		add_settings_field(
			'google_analytics_domain_name',
			__( 'Google Analytics Domain Name', 'standard' ),
			array( $this, 'google_analytics_domain_name_display' ),
			'standard_theme_global_options',
			'global'
		);

		add_settings_field(
			'google_analytics_allow_linker',
			__( 'Google Analytics Allow Linker', 'standard' ),
			array( $this, 'google_analytics_allow_linker_display' ),
			'standard_theme_global_options',
			'global'
			
		);
		
	} // end advanced_google_analytics
	
	/**
	 * Displays the option for introducing the Google Analytics Domain.
	 */
	public function google_analytics_domain_name_display() {
	
		$options = get_option( 'standard_theme_global_options' );
		
		$domain = '';
		if( isset( $options['google_analytics_domain'] ) ) {
			$domain = $options['google_analytics_domain'];
		} // end if
		
		echo '<input type="text" name="standard_theme_global_options[google_analytics_domain]" id="standard_theme_global_options[google_analytics_domain]" value="' . $domain . '" placeholder="' . get_bloginfo( 'siteurl' ) . '" />';
		
		
	} // end google_analytics_domain_name_display
	
	/**
	 * Displays the option for introducing the Google Analytics Linker.
	 */
	public function google_analytics_allow_linker_display() {
		
		$options = get_option( 'standard_theme_global_options' );
		
		$linker = '';
		if( isset( $options['google_analytics_linker'] ) ) {
			$linker = $options['google_analytics_linker'];
		} // end if
		
		$html = '<label for="standard_theme_global_options[google_analytics_linker]">';
			$html .= '<input type="checkbox" name="standard_theme_global_options[google_analytics_linker]" id="standard_theme_global_options[google_analytics_linker]" value="1"' . checked( 1, $linker, false ) . ' />';
			$html .= '&nbsp;';
			$html .= __( 'Display the linker in the header', 'standard-advanced-google-analytics' );
		$html .= '</label>';
		
		echo $html;
		
	} // end google_analytics_allow_linker_display
	
} // end class

/**
 * Instantiates the plugin using the plugins_loaded hook and the 
 * Singleton Pattern.
 */
function Standard_Advanced_Google_Analytics() {
	Standard_Advanced_Google_Analytics::get_instance();
} // end Comments_Not_Replied_To
add_action( 'plugins_loaded', 'Standard_Advanced_Google_Analytics' );

// Registers the new deactivation hook
register_deactivation_hook( __FILE__, array( 'Standard_Advanced_Google_Analytics', 'plugin_deactivation' ) );