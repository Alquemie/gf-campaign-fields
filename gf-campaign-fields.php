<?php
/*
  Plugin Name: Gravity Forms Campaign Fields
  Plugin URI: https://www.gravityaddons.com/
  Description: Creates new field types that are populated with Google Analytics campaign data
  Version: 1.0.5
  Author: Alquemie
  Author URI: https://www.alquemie.net
*/

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

define( 'GF_CAMPAIGN_FIELD_VERSION', '1.0.0' );
define( 'GF_CAMPAIGN_FIELD_SLUG', 'alquemiegravityfields' );

define( 'GF_CAMPAIGN_MERGETAG_DEVICETYPE', '{aq_device_type}');
define( 'GF_CAMPAIGN_MERGETAG_DEVICEBROWSER', '{aq_device_browser}');
define( 'GF_CAMPAIGN_MERGETAG_DEVICEOS', '{aq_device_os}');

define( 'GF_CAMPAIGN_MERGETAG_SOURCE', '{aq_campaign_source}');
define( 'GF_CAMPAIGN_MERGETAG_MEDIUM', '{aq_campaign_medium}');
define( 'GF_CAMPAIGN_MERGETAG_NAME', '{aq_campaign_name}');
define( 'GF_CAMPAIGN_MERGETAG_TERM', '{aq_campaign_term}');
define( 'GF_CAMPAIGN_MERGETAG_CONTENT', '{aq_campaign_content}');

define( 'GF_CAMPAIGN_MERGETAG_MKWID', '{aq_marin_kwid}');
define( 'GF_CAMPAIGN_MERGETAG_PCRID', '{aq_pcrid}');
define( 'GF_CAMPAIGN_MERGETAG_MATCHTYPE', '{aq_sem_matchtype}');
define( 'GF_CAMPAIGN_MERGETAG_GLCID', '{aq_adwords_glcid}');


include_once "class-gf-field-utm-values.php";
include_once "class-gf-field-device-values.php";
include_once "class-gf-field-sem-values.php";
include_once "class-gf-field-marin-values.php";

GFForms::include_feed_addon_framework();
/**
 *
 */
class AqGFCampaignAddOn extends GFAddOn {

	protected $_version = AQ_VELOCIFY_ADDON_VERSION;
	protected $_min_gravityforms_version = '2.0.7.4';
	protected $_slug = 'gf-campaign-fields';
	protected $_path = 'gf-campaign-fields/gf-campaign-fields.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Campaign Information Add-On';
	protected $_short_title = 'Campaign Info';

	private static $_instance = null;


	/**
	 * Get an instance of this class.
	 *
	 * @return AqGFCampaignAddOn
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new AqGFCampaignAddOn();
		}

		return self::$_instance;
	}

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {
		parent::init();
	}

	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {
		return array(
			array(
				'title'  => esc_html__( 'Campaign Add-On Settings', GF_CAMPAIGN_FIELD_SLUG ),
				'fields' => array(
					array(
						'name'              => 'aq_campaign_attribution',
						'label'             => esc_html__( 'Attribution Model', GF_CAMPAIGN_FIELD_SLUG ),
						'required'          => true,
						'type'              => 'select',
						'class'             => 'small',
						'default_value'	=> 'last',
						'choices' => array(
              array(
                  'label' => esc_html__( 'Last Touch', GF_CAMPAIGN_FIELD_SLUG ),
                  'value' => 'last'
              ),
              array(
                  'label' => esc_html__( 'First Touch', GF_CAMPAIGN_FIELD_SLUG ),
                  'value' => 'first'
              )
          	),
						'tooltip'           => esc_html__( 'Campaign attribuition model determines if the campaign information is updated on return visits or if the original campaign is maintained', GF_CAMPAIGN_FIELD_SLUG ),
            'tooltip_class'     => 'tooltipclass',
					),array(
            'type'              => 'text',
            'id'                => 'aq_campaign_name',
						'name'                => 'aq_campaign_name',
            'label'             => esc_html__( 'Campaign Name', GF_CAMPAIGN_FIELD_SLUG ),
            'required'          => true,
            'default_value'     => 'utm_campaign',
            'class'             => 'medium',
            'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign name', GF_CAMPAIGN_FIELD_SLUG ),
            'tooltip_class'     => 'tooltipclass',
						'feedback_callback' => array( $this, 'no_whitespace' ),
        ),array(
					'type'              => 'text',
					'id'                => 'aq_campaign_source',
					'name'                => 'aq_campaign_source',
					'label'             => esc_html__( 'Campaign Source', GF_CAMPAIGN_FIELD_SLUG ),
					'required'          => true,
					'default_value'     => 'utm_source',
					'class'             => 'medium',
					'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign source', GF_CAMPAIGN_FIELD_SLUG ),
					'tooltip_class'     => 'tooltipclass',
					'feedback_callback' => array( $this, 'no_whitespace' ),
				),array(
					'type'              => 'text',
					'id'                => 'aq_campaign_medium',
					'name'                => 'aq_campaign_medium',
					'label'             => esc_html__( 'Campaign Medium', GF_CAMPAIGN_FIELD_SLUG ),
					'required'          => true,
					'default_value'     => 'utm_medium',
					'class'             => 'medium',
					'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign medium', GF_CAMPAIGN_FIELD_SLUG ),
					'tooltip_class'     => 'tooltipclass',
					'feedback_callback' => array( $this, 'no_whitespace' ),
				),array(
					'type'              => 'text',
					'id'                => 'aq_campaign_term',
					'name'                => 'aq_campaign_term',
					'label'             => esc_html__( 'Campaign Term', GF_CAMPAIGN_FIELD_SLUG ),
					'required'          => true,
					'default_value'     => 'utm_term',
					'class'             => 'medium',
					'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign term', GF_CAMPAIGN_FIELD_SLUG ),
					'tooltip_class'     => 'tooltipclass',
					'feedback_callback' => array( $this, 'no_whitespace' ),
				),array(
					'type'              => 'text',
					'id'                => 'aq_campaign_content',
					'name'                => 'aq_campaign_content',
					'label'             => esc_html__( 'Campaign Content', GF_CAMPAIGN_FIELD_SLUG ),
					'required'          => true,
					'default_value'     => 'utm_content',
					'class'             => 'medium',
					'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign content', GF_CAMPAIGN_FIELD_SLUG ),
					'tooltip_class'     => 'tooltipclass',
					'feedback_callback' => array( $this, 'no_whitespace' ),
				),array(
					'type'              => 'text',
					'id'                => 'aq_matchtype',
					'name'                => 'aq_matchtype',
					'label'             => esc_html__( 'Match Type', GF_CAMPAIGN_FIELD_SLUG ),
					'required'          => true,
					'default_value'     => 'utm_matchtype',
					'class'             => 'medium',
					'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign match type', GF_CAMPAIGN_FIELD_SLUG ),
					'tooltip_class'     => 'tooltipclass',
					'feedback_callback' => array( $this, 'no_whitespace' ),
				),array(
					'type'              => 'text',
					'id'                => 'aq_marin_kwid',
					'name'                => 'aq_marin_kwid',
					'label'             => esc_html__( 'Marin KW ID', GF_CAMPAIGN_FIELD_SLUG ),
					'required'          => true,
					'default_value'     => 'mkwid',
					'class'             => 'medium',
					'tooltip'           => esc_html__( 'Query String Variable that is used to set the Marin KW ID', GF_CAMPAIGN_FIELD_SLUG ),
					'tooltip_class'     => 'tooltipclass',
					'feedback_callback' => array( $this, 'no_whitespace' ),
				),array(
					'type'              => 'text',
					'id'                => 'aq_marin_pcrid',
					'name'                => 'aq_marin_pcrid',
					'label'             => esc_html__( 'Marin Creative ID', GF_CAMPAIGN_FIELD_SLUG ),
					'required'          => true,
					'default_value'     => 'pcrid',
					'class'             => 'medium',
					'tooltip'           => esc_html__( 'Query String Variable that is used to set the Marin Content ID', GF_CAMPAIGN_FIELD_SLUG ),
					'tooltip_class'     => 'tooltipclass',
					'feedback_callback' => array( $this, 'no_whitespace' ),
				),

				)
			)
		);
	}

	/**
	 * Determine if value does NOT contain white space character(s)
	 * @param  string  $value
	 * @return boolean
	 */
	public function no_whitespace( $value ) {
		return (!preg_match('/\s+/', $value) && ($value !== ''));
	}
}

add_action( 'gform_loaded', array( 'AQ_Campaign_AddOn_Bootstrap', 'load' ), 5 );

if ( ! class_exists( 'AQ_Campaign_AddOn_Bootstrap' ) ) :
/**
 *
 */
  class AQ_Campaign_AddOn_Bootstrap {

    public static function load() {
        if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
            return;
        }
        // require_once( 'class-gfvelocifyaddon.php' );
        GFAddOn::register( 'AqGFCampaignAddOn' );
    }

  }

endif;

function gf_campaign_addon() {
    return AqGFCampaignAddOn::get_instance();
}

function aq_capture_campaign_values() {
?>
<script>
	var AqGfCampaignData = {
		getUrlParameter: function (name) {
			name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
			var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
			var results = regex.exec(location.search);
			return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
		},
		setCookie: function (cname, cvalue) {
		    var d = new Date();
		    d.setTime(d.getTime() + (30*24*60*60*1000));
		    var expires = "expires="+ d.toUTCString();
		    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
		},
		getCookie: function (cname) {
	    var name = cname + "=";
	    var decodedCookie = decodeURIComponent(document.cookie);
	    var ca = decodedCookie.split(';');
	    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
        	c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
        	return c.substring(name.length, c.length);
        }
	    }
	    return '';
		}
	};
</script>
    <?php
}

function aq_include_whichbrowser() {
	$mypath = plugins_url( 'includes/whichbrowser/server/detect.php', __FILE__ );
?>
	<script>
	(function(){var p=[],w=window,d=document,e=f=0;p.push('ua='+encodeURIComponent(navigator.userAgent));e|=w.ActiveXObject?1:0;e|=w.opera?2:0;e|=w.chrome?4:0;
	e|='getBoxObjectFor' in d || 'mozInnerScreenX' in w?8:0;e|=('WebKitCSSMatrix' in w||'WebKitPoint' in w||'webkitStorageInfo' in w||'webkitURL' in w)?16:0;
	e|=(e&16&&({}.toString).toString().indexOf("\\n")===-1)?32:0;p.push('e='+e);f|='sandbox' in d.createElement('iframe')?1:0;f|='WebSocket' in w?2:0;
	f|=w.Worker?4:0;f|=w.applicationCache?8:0;f|=w.history && history.pushState?16:0;f|=d.documentElement.webkitRequestFullScreen?32:0;f|='FileReader' in w?64:0;
	p.push('f='+f);p.push('r='+Math.random().toString(36).substring(7));p.push('w='+screen.width);p.push('h='+screen.height);var s=d.createElement('script');
	s.src='<?php echo $mypath; ?>?' + p.join('&');d.getElementsByTagName('head')[0].appendChild(s);})();
	</script>
<?php
}
add_action('wp_head', 'aq_include_whichbrowser');

wp_enqueue_script( 'aq_campaign_js', plugins_url( 'gf-campaigns.js', __FILE__ ), null, null, true );
