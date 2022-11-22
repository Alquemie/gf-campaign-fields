<?php
/*
  Plugin Name: Gravity Forms Campaign Fields
  Plugin URI: https://www.gravityaddons.com/
  Description: Creates new field types that are populated with Google Analytics campaign data
  Version: 2.5.0
  Author: Alquemie
  Author URI: https://www.alquemie.net/
*/
// namespace Alquemie\Campaigns;

/*
if ( ! class_exists( 'GFForms' ) ) {
	die();
}
*/

// define( 'GF_CAMPAIGN_FIELD_VERSION', '2.5.0' );
// define( 'GF_CAMPAIGN_FIELD_DIR', __DIR__ );
// define( 'GF_CAMPAIGN_FIELD_URL', plugin_dir_url( __FILE__ ) );


add_action( 'gform_loaded', array( 'GF_Camapign_Fields_AddOn_Bootstrap', 'load' ), 5 );
 
class GF_Camapign_Fields_AddOn_Bootstrap {
 
		public static function load() {
 
				if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
						return;
				}
 
 				// require_once( 'lib/autoload.php' );
				require_once( 'src/classes/class-gf-field-campaign-info.php' );
				require_once( 'src/classes/class-GFCampainFieldsAddOn.php' );
				
				\GFAddOn::register( 'AqGFCampaignAddOn' );
		}
 
}
 
/**
 * Gets this plugin's absolute directory path.
 *
 * @since  1.0.2
 * @ignore
 * @access private
 *
 * @return string
 */
function _get_plugin_version() {
	$plugin_data = get_plugin_data( __FILE__ );
	return $plugin_data['Version'];
}

/**
 * Get's the asset file's version number by using it's modification timestamp.
 *
 * @since 1.0.0
 *
 * @param string $relative_path Relative path to the asset file.
 *
 * @return bool|int
 */
function _get_asset_version( $relative_path ) {
	return filemtime( _get_plugin_directory() . $relative_path );
}

/**
 * Gets this plugin's absolute directory path.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @return string
 */
function _get_plugin_directory() {
	return __DIR__;
}

/**
 * Gets this plugin's URL.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @return string
 */
function _get_plugin_url() {
	static $plugin_url;

	if ( empty( $plugin_url ) ) {
		$plugin_url = plugins_url( null, __FILE__ );
	}

	return $plugin_url;
}

/**
 * Checks if this plugin is in development mode.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @return bool
 */
function _is_in_development_mode() {
	$isDebug = (defined( 'WP_DEBUG' ) )  ? WP_DEBUG : false;
	return $isDebug;
}

