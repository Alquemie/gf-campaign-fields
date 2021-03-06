<?php
/*
  Plugin Name: Gravity Forms Campaign Fields
  Plugin URI: https://www.gravityaddons.com/
  Description: Creates new field types that are populated with Google Analytics campaign data
  Version: 2.4.0
  Author: Alquemie
  Author URI: https://www.alquemie.net/
*/

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

define( 'GF_CAMPAIGN_FIELD_VERSION', '2.4.0' );
define( 'GF_CAMPAIGN_FIELD_SLUG', 'gfcampaign' );

include_once "classes/class-gf-field-hiddengroup.php";
include_once "classes/class-gf-field-googleanalytics.php";
include_once "classes/class-gf-field-deviceinfo.php";
include_once "classes/class-gf-field-sem.php";
include_once "classes/class-gf-field-marin.php";
include_once "classes/class-gf-field-analyticsuserid.php";

GFForms::include_feed_addon_framework();
/**
 *
 */
class AqGFCampaignAddOn extends GFAddOn {

	protected $_version = GF_CAMPAIGN_FIELD_VERSION;
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
		add_action('wp_head', array($this, 'whichbrowser'));
		add_action('wp_head', array($this, 'set_campaign_parms'), 100 );
		add_action('wp_footer', array($this, 'loadCampaignData'), 100);
		add_action( 'wp_enqueue_scripts', array($this, 'load_campaign_js') );

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
            'id'                => 'aq_cookie_lifetime',
						'name'                => 'aq_cookie_lifetime',
            'label'             => esc_html__( 'Cookie Lifetime (days)', GF_CAMPAIGN_FIELD_SLUG ),
            'required'          => true,
            'default_value'     => '30',
            'class'             => 'medium',
            'tooltip'           => esc_html__( 'The lifetime of the first touch campaign data.  This value is extended each time a visitor returns to the site.', GF_CAMPAIGN_FIELD_SLUG ),
            'tooltip_class'     => 'tooltipclass',
						'feedback_callback' => array( $this, 'validate_int' ),
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

	public function whichbrowser() {
		$mypath = plugins_url( 'lib/whichbrowser/server/detect.php', __FILE__ );
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

	public function set_campaign_parms() {

		$attribution = $this->get_plugin_setting('aq_campaign_attribution');
		$nameqs = $this->get_plugin_setting('aq_campaign_name');
		$sourceqs = $this->get_plugin_setting('aq_campaign_source');
		$mediumqs = $this->get_plugin_setting('aq_campaign_medium');
		$termqs = $this->get_plugin_setting('aq_campaign_term');
		$contentqs = $this->get_plugin_setting('aq_campaign_content');
		$matchtypeqs = $this->get_plugin_setting('aq_matchtype');
		$mkwidqs = $this->get_plugin_setting('aq_marin_kwid');
		$pcridqs = $this->get_plugin_setting('aq_marin_pcrid');

		$script = '<script>' . PHP_EOL;
		$script .= "var alquemie = { attribution: '{$attribution}'," . PHP_EOL;
		$script .= "	QS: {" . PHP_EOL;
		$script .= "		campaign: '{$nameqs}'," . PHP_EOL;
		$script .= "		source:  '{$sourceqs}'," . PHP_EOL;
		$script .= "		medium: '{$mediumqs}'," . PHP_EOL;
		$script .= "		term: '{$termqs}'," . PHP_EOL;
		$script .= "		content: '{$contentqs}'," . PHP_EOL;
		$script .= "		matchtype: '{$matchtypeqs}'," . PHP_EOL;
		$script .= "		marinkwid: '{$mkwidqs}'," . PHP_EOL;
		$script .= "		marinpcrid: '{$pcridqs}'," . PHP_EOL;
		$script .= "	}	};" . PHP_EOL;
		$script .= '</script>' . PHP_EOL;
		echo $script;

	}

	public function loadCampaignData() {
		$cookieLife = ($this->get_plugin_setting('aq_cookie_lifetime'));
		$cookieLife = ($cookieLife) ? $cookieLife : 30;
	?>
	<script>
	const AlquemieJS = {
		getUrlParameter: function (name) {
			name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
			var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
			var results = regex.exec(location.search);
			return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
		}
	};

	alquemie.LastCamp = Cookies.getJSON('aqcamplast');
	alquemie.Campaign = Cookies.getJSON('aqcamp');

	if (AlquemieJS.getUrlParameter(alquemie.QS.source) != '') {
		alquemie.LastCamp = {
			"campaign": AlquemieJS.getUrlParameter(alquemie.QS.campaign).toLowerCase(),
			"source": AlquemieJS.getUrlParameter(alquemie.QS.source).toLowerCase(),
			"medium": AlquemieJS.getUrlParameter(alquemie.QS.medium).toLowerCase(),
			"term": AlquemieJS.getUrlParameter(alquemie.QS.term).toLowerCase(),
			"content": AlquemieJS.getUrlParameter(alquemie.QS.content).toLowerCase()
		};
	} else if (AlquemieJS.getUrlParameter('utm_source') != '') {
		alquemie.LastCamp = {
			"campaign": AlquemieJS.getUrlParameter('utm_campaign').toLowerCase(),
			"source": AlquemieJS.getUrlParameter('utm_source').toLowerCase(),
			"medium": AlquemieJS.getUrlParameter('utm_medium').toLowerCase(),
			"term": AlquemieJS.getUrlParameter('utm_term').toLowerCase(),
			"content": AlquemieJS.getUrlParameter('utm_content').toLowerCase()
		};
	} else if (typeof alquemie.LastCamp == 'undefined') {
		var source = campaign = '';
		try {
			if (typeof document.referrer != 'undefined') {
				var a=document.createElement('a');
				a.href = document.referrer;
			}
			if (a.hostname != location.hostname) {
				source = a.hostname;
				campaign = 'seo';
			}

		} catch(e) {
			console.log(e.message);
		}

		alquemie.LastCamp = {
			"campaign": campaign,
			"source": source.toLowerCase(),
			"medium": "",
			"term": "",
			"content": ""
		};
	}

	var mtype = AlquemieJS.getUrlParameter(alquemie.QS.matchtype);
	if (mtype != '' || (typeof alquemie.LastCamp.matchtype == 'undefined')) alquemie.LastCamp.matchtype = mtype;

	var mkwid = AlquemieJS.getUrlParameter(alquemie.QS.marinkwid);
	if (mkwid != '' || (typeof alquemie.LastCamp.mkwid == 'undefined')) alquemie.LastCamp.mkwid = mkwid;

	var pcrid = AlquemieJS.getUrlParameter(alquemie.QS.marinpcrid);
	if (pcrid != '' || (typeof alquemie.LastCamp.pcrid == 'undefined')) alquemie.LastCamp.pcrid = pcrid;

	var gclid = AlquemieJS.getUrlParameter('gclid');
	if (gclid != '' || (typeof alquemie.LastCamp.gclid == 'undefined')) alquemie.LastCamp.gclid = gclid;

	if (typeof alquemie.Campaign == 'undefined') {
		alquemie.Campaign = alquemie.LastCamp;
	}

	Cookies.withAttributes({ path: '/', expires: <?php echo $cookieLife; ?>, secure: true, sameSite: 'Lax'  })
	Cookies.set('aqcamplast', alquemie.LastCamp);
	Cookies.set('aqcamp', alquemie.Campaign, { expires: <?php echo $cookieLife; ?> });

	alquemie.attribution = (typeof alquemie.attribution == 'undefined') ? 'last' : alquemie.attribution;

	if (alquemie.attribution == 'first') {
		alquemie.thisCampaign = alquemie.Campaign;
	} else {
		alquemie.thisCampaign = alquemie.LastCamp;
	}
	if (typeof dataLayer != 'undefined') dataLayer.push(alquemie.thisCampaign);

	var whichURL = document.URL.substr(0,document.URL.lastIndexOf('/')) + '/lib/whichbrowser/server/detect.php';

	function waitForWhichBrowser(cb) {
		var callback = cb;

		function wait() {
			if (typeof WhichBrowser == 'undefined')
				window.setTimeout(wait, 100)
			else
				callback();
		}

		wait();
	}

	function updateCampaignFields() {
		waitForWhichBrowser(function() {

			try {
				deviceinfo = new WhichBrowser();

				var deviceFields = document.getElementsByClassName('gfield_aqDeviceInfo');
				for( var i = 0; i < deviceFields.length; i++) {
					document.getElementById(deviceFields[i].id + "_1").value = deviceinfo.device.type;
					document.getElementById(deviceFields[i].id + "_2").value = deviceinfo.browser.name;
					document.getElementById(deviceFields[i].id + "_3").value = deviceinfo.os.name;
				};

				if (typeof dataLayer != 'undefined') dataLayer.push({"deviceType": deviceinfo.device.type, "deviceBrowser":deviceinfo.browser.name, "deviceOS": deviceinfo.os.name} );
			} catch(e) {
				alert(e);
			}
		});

		var utmfields = document.getElementsByClassName('gfield_aqGoogleAnalytics');
		for( var i = 0; i < utmfields.length; i++) {
			document.getElementById(utmfields[i].id + '_3').value = alquemie.thisCampaign.campaign;
			document.getElementById(utmfields[i].id + '_1').value = alquemie.thisCampaign.source;
			document.getElementById(utmfields[i].id + '_2').value = alquemie.thisCampaign.medium;
			document.getElementById(utmfields[i].id + '_4').value = alquemie.thisCampaign.term;
			document.getElementById(utmfields[i].id + '_5').value = alquemie.thisCampaign.content;
		}

		var semfields = document.getElementsByClassName('gfield_aqSEM');
		for( i = 0; i < semfields.length; i++) {
			document.getElementById(semfields[i].id + '_1').value = alquemie.thisCampaign.matchtype;
			document.getElementById(semfields[i].id + '_2').value = alquemie.thisCampaign.gclid;
		}

		var marinfields = document.getElementsByClassName('gfield_aqMarin');
		for( i = 0; i < marinfields.length; i++) {
			document.getElementById(marinfields[i].id + '_1').value = alquemie.thisCampaign.mkwid;
			document.getElementById(marinfields[i].id + '_2').value = alquemie.thisCampaign.pcrid;
		}
	}

	document.addEventListener("DOMContentLoaded",function(event) {
			updateCampaignFields();
	});
	var gforms = document.getElementsByClassName("gform_wrapper");
	for (var f = 0; f < gforms.length; f++) {
		gforms[f].addEventListener("DOMSubtreeModified", function(event) {
			updateCampaignFields();
		});
	}
	</script>
	<script>
	if (typeof ga != 'undefined') {
		ga(function(tracker) {
		  var clientId = tracker.get('clientId');
			console.log('ClientID: ' + clientId);
		});
	}
	</script>
	<?php

	}

	/**
	 * Determine if value does NOT contain white space character(s)
	 * @param  string  $value
	 * @return boolean
	 */
	public function no_whitespace( $value ) {
		return (!preg_match('/\s+/', $value) && ($value !== ''));
	}

	/**
	 * Determine if value is number
	 * @param  string  $value
	 * @return boolean
	 */
	public function validate_int( $value ) {
    return (preg_match('/^[1-9][0-9]*$/', $value) === 1);
	}

	public function load_campaign_js() {
	    wp_enqueue_script( 'aq_js_cookie', plugins_url( 'js/js.cookie.min.js', __FILE__ ), null, '2.2.1', true );
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
        GFAddOn::register( 'AqGFCampaignAddOn' );
    }

  }

endif;

function gf_campaign_addon() {
    return AqGFCampaignAddOn::get_instance();
}
