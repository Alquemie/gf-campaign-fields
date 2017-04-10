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

/*
add_filter( 'gform_replace_merge_tags', 'replace_devicetype_value', 10, 7 );
function replace_devicetype_value( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {

    if ( strpos( $text, GF_CAMPAIGN_MERGETAG_DEVICETYPE ) !== false ) {
			require plugin_dir_path( __FILE__ ) . 'includes/whichbrowser/autoload.php';
			$result = new WhichBrowser\Parser(getallheaders());
			$devicetype = $result->device->type;
			$deviceos = $result->device->os;
			$devicebrowser = $result->browser->name;

	    $text = str_replace( GF_CAMPAIGN_MERGETAG_DEVICETYPE, $devicetype, $text );
			$text = str_replace( GF_CAMPAIGN_MERGETAG_DEVICEBROWSER, $devicebrowser, $text );
			$text = str_replace( GF_CAMPAIGN_MERGETAG_DEVICEOS, $deviceos, $text );
		}

		if ( strpos( $text, GF_CAMPAIGN_MERGETAG_SOURCE  ) !== false ) {
			$source = (isset($_GET['_source'])) ? $_GET['_source'] : '';
			$source = (isset($_GET['utm_source']) && $source == '') ? $_GET['utm_source'] : '';
			$source = (isset($_COOKIE['aq_source']) && $source == '') ? $_COOKIE['aq_source'] : '';

			$text = str_replace( GF_CAMPAIGN_MERGETAG_SOURCE, $source, $text );
		}

		if ( strpos( $text, GF_CAMPAIGN_MERGETAG_MEDIUM  ) !== false ) {
			$medium =  (isset($_GET['_medium'])) ? $_GET['_medium'] : '';
			$medium =  (isset($_GET['utm_medium']) && $medium == '') ? $_GET['utm_medium'] : '';
			$medium =  (isset($_COOKIE['aq_medium']) && $medium == '') ? $_COOKIE['aq_medium'] : '';

			$text = str_replace( GF_CAMPAIGN_MERGETAG_MEDIUM, $medium, $text );
		}

		if ( strpos( $text, GF_CAMPAIGN_MERGETAG_NAME  ) !== false ) {
			$name =  (isset($_GET['_campaign'])) ? $_GET['_campaign'] : '';
			$name =  (isset($_GET['utm_campaign']) && $name == '') ? $_GET['utm_campaign'] : '';
			$name =  (isset($_COOKIE['aq_campaign']) && $name == '') ? $_COOKIE['aq_campaign'] : $_COOKIE['aq_campaign'];
			$text = str_replace( GF_CAMPAIGN_MERGETAG_NAME, $name, $text );

		}

		if ( strpos( $text, GF_CAMPAIGN_MERGETAG_TERM  ) !== false ) {
			$term =  (isset($_GET['_term'])) ? $_GET['_term'] : '';
			$term =  (isset($_GET['utm_term']) && $term == '') ? $_GET['utm_term'] : '';
			$term =  (isset($_COOKIE['aq_term']) && $term == '') ? $_COOKIE['aq_term'] : '';

			$text = str_replace( GF_CAMPAIGN_MERGETAG_TERM, $term, $text );
		}

		if ( strpos( $text, GF_CAMPAIGN_MERGETAG_CONTENT  ) !== false ) {
			$content =  (isset($_GET['_content'])) ? $_GET['_content'] : '';
			$content =  (isset($_GET['utm_content']) && $content == '') ? $_GET['utm_content'] : '';
			$content =  (isset($_COOKIE['aq_content']) && $content == '') ? $_COOKIE['aq_content'] : '';

			$text = str_replace( GF_CAMPAIGN_MERGETAG_CONTENT, $content, $text );
		}

		if ( strpos( $text, GF_CAMPAIGN_MERGETAG_GLCID  ) !== false ) {
			$glcid =  (isset($_GET['glcid'])) ? $_GET['glcid'] : '';
			$glcid =  (isset($_COOKIE['aq_glcid']) && $content == '') ? $_COOKIE['aq_glcid'] : '';

			$text = str_replace( GF_CAMPAIGN_MERGETAG_GLCID, $glcid, $text );
		}

		if ( strpos( $text, GF_CAMPAIGN_MERGETAG_MKWID ) !== false ) {
			$glcid =  (isset($_GET['_mkwid'])) ? $_GET['_mkwid'] : '';
			$glcid =  (isset($_COOKIE['aq_mkwid']) && $content == '') ? $_COOKIE['aq_mkwid'] : '';

			$text = str_replace( GF_CAMPAIGN_MERGETAG_MKWID, $glcid, $text );
		}

		if ( strpos( $text, GF_CAMPAIGN_MERGETAG_MATCHTYPE  ) !== false ) {
			$match =  (isset($_GET['_match'])) ? $_GET['_match'] : '';
			$glcid =  (isset($_COOKIE['aq_matchtype']) && $content == '') ? $_COOKIE['aq_matchtype'] : '';

			$text = str_replace( GF_CAMPAIGN_MERGETAG_MATCHTYPE, $match, $text );
		}

    return $text;
}
*/

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
// add_action('wp_head', 'aq_capture_campaign_values');

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

/*
document.addEventListener("DOMContentLoaded", function(event) {

		var source = getUrlParameter('_source');
		var altsource = getUrlParameter('utm_source');
		if (source !== '') {
			setCookie('aq_source', source);
		} else if (altsource !== '') {
			setCookie('aq_source', altsource);
		}


	// if (getCookie('aq_campaign') != '') {
		var campaign = getUrlParameter('_campaign');
		var altcampaign = getUrlParameter('utm_campaign');
		if (campaign !== '') {
			setCookie('aq_campaign', campaign);
		} else if (altcampaign !== '') {
			setCookie('aq_campaign', altcampaign);
		}
	// }

	// if (getCookie('aq_medium') != '') {
		var medium = getUrlParameter('utm_medium');
		if (medium !== '') {
			setCookie('aq_medium', medium);
		}
	// }

	// if (getCookie('aq_term') != '') {
		var term = getUrlParameter('_kw');
		var altterm = getUrlParameter('utm_term');
		if (term !== '') {
			setCookie('aq_term', term);
		} else if (altterm !== '') {
			setCookie('aq_term', altterm);
		}
	// }

	// if (getCookie('aq_content') != '') {
		var content = getUrlParameter('_group');
		var altcontent = getUrlParameter('utm_content');
		if (content !== '') {
			setCookie('aq_content', content);
		} else if (altcontent !== '') {
			setCookie('aq_content', altcontent);
		}
	// }

	// if (getCookie('aq_matchtype') != '') {
		var matchtype = getUrlParameter('_match');
		if (matchtype !== '') {
			setCookie('aq_matchtype', matchtype);
		}
	// }

	// if (getCookie('aq_mkwid') != '') {
		var mwkid = getUrlParameter('_mkwid');
		if (mwkid !== '') {
			setCookie('aq_mkwid', mwkid);
		}
	// }

	// if (getCookie('glcid') != '') {
		var glcid = getUrlParameter('glcid');
		if (glcid !== '') {
			setCookie('aq_glcid', glcid);
		}
	// }

});
 */
