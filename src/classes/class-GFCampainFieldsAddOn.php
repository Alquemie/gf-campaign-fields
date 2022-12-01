<?php
/*
*
*/
namespace Alquemie\CampaignFields;

/**
 *
 */
class AqGFCampaignAddOn extends \GFAddOn {
	protected $_version;
	protected $_min_gravityforms_version = '2.5';
	protected $_slug = 'gf-campaign-fields';
	protected $_path = 'gf-campaign-fields/gf-campaign-fields.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Marketing Campaign Add-On';
	protected $_short_title = 'Marketing Campaign';

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
		$this->_version = _get_plugin_version();

		add_action('wp_head', array($this, 'set_campaign_parms'), 100 );
		
		parent::init();
	}

	public function get_menu_icon() {
		return $this->is_gravityforms_supported( '2.5-beta-4' ) ? _get_plugin_url() . '/dist/img/data-analytics.png' : 'dashicons-admin-generic';
	}

	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {
		return array(
			array(
				'title'  => esc_html__( 'Campaign Add-On Settings', 'gf-campaign-fields' ),
				'fields' => array(
					array(
						'name'              => 'aq_campaign_attribution',
						'label'             => esc_html__( 'Attribution Model', 'gf-campaign-fields' ),
						'required'          => true,
						'type'              => 'select',
						'class'             => 'small',
						'default_value'	=> 'last',
						'choices' => array(
							array(
								'label' => esc_html__( 'Last Touch', 'gf-campaign-fields' ),
								'value' => 'last'
							),
							array(
								'label' => esc_html__( 'First Touch', 'gf-campaign-fields' ),
								'value' => 'first'
							)
							),
						'tooltip'           => esc_html__( 'Campaign attribuition model determines if the campaign information is updated on return visits or if the original campaign is maintained', 'gf-campaign-fields' ),
            			'tooltip_class'     => 'tooltipclass',
					),
					array(
						'type'              => 'text',
						'id'                => 'aq_cookie_lifetime',
						'name'              => 'aq_cookie_lifetime',
						'label'             => esc_html__( 'Cookie Lifetime (days)', 'gf-campaign-fields' ),
						'required'          => true,
						'default_value'     => '30',
						'class'             => 'medium',
						'tooltip'           => esc_html__( 'The lifetime of the first touch campaign data.  This value is extended each time a visitor returns to the site.', 'gf-campaign-fields' ),
            			'tooltip_class'     => 'tooltipclass',
						'feedback_callback' => array( $this, 'validate_int' ),
					),array(
						'type'              => 'text',
						'id'                => 'aq_campaign_name',
						'name'              => 'aq_campaign_name',
						'label'             => esc_html__( 'Campaign Name', 'gf-campaign-fields' ),
						'required'          => true,
						'default_value'     => 'utm_campaign',
						'class'             => 'medium',
						'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign name', 'gf-campaign-fields' ),
						'tooltip_class'     => 'tooltipclass',
						'feedback_callback' => array( $this, 'no_whitespace' ),
					),array(
						'type'              => 'text',
						'id'                => 'aq_campaign_source',
						'name'                => 'aq_campaign_source',
						'label'             => esc_html__( 'Campaign Source', 'gf-campaign-fields' ),
						'required'          => true,
						'default_value'     => 'utm_source',
						'class'             => 'medium',
						'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign source', 'gf-campaign-fields' ),
						'tooltip_class'     => 'tooltipclass',
						'feedback_callback' => array( $this, 'no_whitespace' ),
					),array(
						'type'              => 'text',
						'id'                => 'aq_campaign_medium',
						'name'                => 'aq_campaign_medium',
						'label'             => esc_html__( 'Campaign Medium', 'gf-campaign-fields' ),
						'required'          => true,
						'default_value'     => 'utm_medium',
						'class'             => 'medium',
						'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign medium', 'gf-campaign-fields' ),
						'tooltip_class'     => 'tooltipclass',
						'feedback_callback' => array( $this, 'no_whitespace' ),
					),array(
						'type'              => 'text',
						'id'                => 'aq_campaign_term',
						'name'                => 'aq_campaign_term',
						'label'             => esc_html__( 'Campaign Term', 'gf-campaign-fields' ),
						'required'          => true,
						'default_value'     => 'utm_term',
						'class'             => 'medium',
						'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign term', 'gf-campaign-fields' ),
						'tooltip_class'     => 'tooltipclass',
						'feedback_callback' => array( $this, 'no_whitespace' ),
					),array(
						'type'              => 'text',
						'id'                => 'aq_campaign_content',
						'name'                => 'aq_campaign_content',
						'label'             => esc_html__( 'Campaign Content', 'gf-campaign-fields' ),
						'required'          => true,
						'default_value'     => 'utm_content',
						'class'             => 'medium',
						'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign content', 'gf-campaign-fields' ),
						'tooltip_class'     => 'tooltipclass',
						'feedback_callback' => array( $this, 'no_whitespace' ),
					),array(
						'type'              => 'text',
						'id'                => 'aq_matchtype',
						'name'              => 'aq_matchtype',
						'label'             => esc_html__( 'Match Type', 'gf-campaign-fields' ),
						'required'          => true,
						'default_value'     => 'matchtype',
						'class'             => 'medium',
						'tooltip'           => esc_html__( 'Query String Variable that is used to set campaign match type', 'gf-campaign-fields' ),
						'tooltip_class'     => 'tooltipclass',
						'feedback_callback' => array( $this, 'no_whitespace' ),
					)
				)
			)
		);
	}


	public function set_campaign_parms() {

		$attribution = $this->get_plugin_setting('aq_campaign_attribution');
		$attribution = ($attribution == '') ? 'last' : $attribution;
		$nameqs = $this->get_plugin_setting('aq_campaign_name');
		$nameqs = ($nameqs == '') ? 'utm_campaign' : $nameqs;
		$sourceqs = $this->get_plugin_setting('aq_campaign_source');
		$sourceqs = ($sourceqs == '') ? 'utm_source' : $sourceqs;
		$mediumqs = $this->get_plugin_setting('aq_campaign_medium');
		$mediumqs = ($mediumqs == '') ? 'utm_medium' : $mediumqs;
		$termqs = $this->get_plugin_setting('aq_campaign_term');
		$termqs = ($termqs == '') ? 'utm_term' : $termqs;
		$contentqs = $this->get_plugin_setting('aq_campaign_content');
		$contentqs = ($contentqs == '') ? 'utm_content' : $contentqs;
		$matchtypeqs = $this->get_plugin_setting('aq_matchtype');
		$matchtypeqs = ($matchtypeqs == '') ? 'matchtype' : $matchtypeqs;
		$cookieLife = ($this->get_plugin_setting('aq_cookie_lifetime'));
		$cookieLife = ($cookieLife) ? $cookieLife : 30;

		$script = '<script>' . PHP_EOL;
		$script .= "var alqCampaignSettings = { attribution: '{$attribution}',";
		$script .= "parameters: {";
		$script .= " campaign: '{$nameqs}',";
		$script .= " source:  '{$sourceqs}'," ;
		$script .= " medium: '{$mediumqs}',";
		$script .= " term: '{$termqs}',";
		$script .= " content: '{$contentqs}',";
		$script .= " matchtype: '{$matchtypeqs}',";
		$script .= "}, ";
		$script .= "cookieLife: {$cookieLife}	}; ";
		$script .= '</script>' . PHP_EOL;
		echo $script;
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

}
