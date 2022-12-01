<?php
/*
Gravity Forms Camaign Info AddOn

@package     Alquemie\CampaignFields
@author      Chris Carrel
@license     GPL-3.0+
 
Plugin Name: Gravity Forms Campaign Info AddOn
Plugin URI: https://www.gravityaddons.com/
Description: Creates new field that is populated with a JSON object containing Google Analytics campaign information (UTM Parameters) and additional advertising information sent via query string parameters.
Version: 3.0.2
Author: Alquemie
Author URI: https://www.alquemie.net/
Text Domain: gf-campaign-fields
License:     GPL-3.0+
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

------------------------------------------------------------------------
Copyright 2022 Carmack Holdings, LLC.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

namespace Alquemie\CampaignFields;

if ( !defined('ABSPATH') ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}



if ( ! class_exists( 'GravityFormsCampaign_Bootstrap' ) ) :
class GravityFormsCampaign_Bootstrap {

    public static function load() {

        if ( ! method_exists( '\GFForms', 'include_addon_framework' ) ) {
            return;
        }

		self::includes();
		self::hooks();

        \GFAddOn::register( '\Alquemie\CampaignFields\AqGFCampaignAddOn' );
    }

	public static function includes() {
		require_once _get_plugin_directory() . '/src/classes/class-GFCampainFieldsAddOn.php';
		require_once _get_plugin_directory() . '/src/classes/class-gf-field-campaign-info.php';	
	}

	public static function hooks() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__,  'enqueue_campaign_scripts') );
		add_action( 'gform_after_save_form', array( __CLASS__ , 'require_analytics_field' ), 50, 2 );
	}

	public static function require_analytics_field( $form, $is_new ) {

		$hasField = false;
		foreach ($form['fields'] as $f) {
			if ($f['type'] == 'aqGoogleAnalytics') { $hasField = true; }
		}

		if (!$hasField) {
			$form['is_active'] = '1';
			$new_field_id = \GFFormsModel::get_next_field_id( $form['fields'] );
			$properties['type'] = 'aqGoogleAnalytics';
			$properties['id']  = $new_field_id;
			$properties['label'] = 'Campagin Details';
			$properties['size'] = 'small';
			$field = \GF_Fields::create( $properties );
			$form['fields'][] = $field;
			$result = \GFAPI::update_form( $form );
		}
	}

	public static function enqueue_campaign_scripts() {

		$isDevMode = _is_in_development_mode();
		if ($isDevMode) {
			$jsFileURI = _get_plugin_url() . '/src/public/js/campaigns.js';
		} else {
			$jsFilePath = glob( _get_plugin_directory() . '/dist/js/public.*.js' );
			$jsFileURI = _get_plugin_url() . '/dist/js/' . basename($jsFilePath[0]);
		}
		wp_enqueue_script( 'js-cookie', _get_plugin_url() . '/src/dist/js/js.cookie.min.js'  , array() , null , true );
		wp_enqueue_script( 'gf-campaign-fields-js', $jsFileURI , array('jquery','js-cookie') , null , true );
		
	}

}
endif;

add_action( 'gform_loaded', array( '\Alquemie\CampaignFields\GravityFormsCampaign_Bootstrap', 'load' ), 5 );
add_action( 'admin_notices',  '\Alquemie\CampaignFields\missing_main_notice'  );
register_activation_hook( __FILE__, '\Alquemie\CampaignFields\gf_campaign_fields_activate' );

/**
 * Activate the plugin.
 */
function gf_campaign_fields_activate() { 
	add_action( 'gform_loaded', '\Alquemie\CampaignFields\addCampaignField2Existing', 20 );
}

function addCampaignField2Existing() {
	$forms = \GFAPI::get_forms();

	foreach ($forms as $form) {
		foreach ($form['fields'] as $f) {
			if ($f['type'] == 'aqGoogleAnalytics') { $hasField = true; }
		}

		if (!$hasField) {
			$form['is_active'] = '1';
			$new_field_id = \GFFormsModel::get_next_field_id( $form['fields'] );
			$properties['type'] = 'aqGoogleAnalytics';
			$properties['id']  = $new_field_id;
			$properties['label'] = 'Campagin Details';
			$properties['size'] = 'small';
			$field = \GF_Fields::create( $properties );
			$form['fields'][] = $field;
			$result = \GFAPI::update_form( $form );
		}
	}

}

function campaign_fields_addon() {
    return AqGFCampaignAddOn::get_instance();
}

/**
 * Create a warning in WP admin if GravityForms is not installed.
 *
 * @since  3.0.0
 *
 * @return string
 */
function missing_main_notice() { 
	if (! is_plugin_active( 'gravityforms/gravityforms.php' ) ) { 
	?>
	<div class="notice notice-error">
		<p><?php _e('GravityForms Campaign Fields AddOn requires a licensed version of <a href="https://gravityforms.com/" target="_blank">GravityForms</a> plugin by RocketGenius in order to function.', 'gf-campaign-fields'); ?></p>
	</div>
	<?php 
	}
}

/**
 * Gets this plugin's absolute directory path.
 *
 * @since  3.0.0
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
 * @since 3.0.0
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
 * @since  3.0.0
 *
 * @return string
 */
function _get_plugin_directory() {
	return __DIR__;
}

/**
 * Gets this plugin's URL.
 *
 * @since  3.0.0
 *
 * @return string
 */
function _get_plugin_url() {
	static $plugin_url;

	if ( empty( $plugin_url ) ) {
		$plugin_url = plugins_url( basename( __DIR__ ) . '' );
	}

	return $plugin_url;
}

/**
 * Checks if this plugin is in development mode.
 *
 * @since  3.0.0
 *
 * @return bool
 */
function _is_in_development_mode() {
	$isDebug = (defined( 'WP_DEBUG' ) )  ? WP_DEBUG : false;
	return $isDebug;
}
