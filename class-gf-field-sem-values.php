<?php

// If Gravity Forms isn't loaded, bail.
if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/* Field names */

$matchType = '';
$glcid = '';

/**
 * Class GF_Field_Name
 *
 * Handles the behavior of the Name field.
 *
 * @since Unknown
 */
class GF_Field_SEM_Values extends GF_Field {

	/**
	 * Sets the field type.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @var string The type of field.
	 */
	public $type = 'aq_gf_sem_field';

	public function __construct( $data = array() ) {
		parent::__construct($data);

	}

	public function get_form_editor_button()
	{
			return array(
					'group' => 'advanced_fields',
					'text'  => $this->get_form_editor_field_title()
			);
	}

	/**
	 * Sets the field title of the Name field.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFCommon::get_field_type_title()
	 * @used-by GF_Field::get_form_editor_button()
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return esc_attr__( 'SEM Values', GF_CAMPAIGN_FIELD_SLUG );
	}

	/**
	 * Defines if conditional logic is supported by the Name field.
	 *
	 * @since Unknown
	 * @access public
	 *
	 * @used-by GFFormDetail::inline_scripts()
	 * @used-by GFFormSettings::output_field_scripts()
	 *
	 * @return bool true
	 */
	public function is_conditional_logic_supported() {
		return true;
	}


	public function get_form_editor_inline_script_on_page_render() {
			return "
			gform.addFilter('gform_form_editor_can_field_be_added', function (canFieldBeAdded, type) {
						if (type == '" . $this->type . "') {
							if (GetFieldsByType(['" . $this->type . "']).length > 0) {
									alert(" . json_encode( esc_html__( 'SORRY! Only one ', GF_CAMPAIGN_FIELD_SLUG ) . $this->get_form_editor_field_title() . esc_html__(' Field Allowed', GF_CAMPAIGN_FIELD_SLUG ) ) . ");
									return false;
							}
						}
					return canFieldBeAdded;
			});" . PHP_EOL . sprintf( "function SetDefaultValues_%s(field) {field.label = '%s';}", $this->type, $this->get_form_editor_field_title() ) . PHP_EOL;
	}

	/**
	 * Defines the field settings available for the Name field in the form editor.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFFormDetail::inline_scripts()
	 *
	 * @return array The field settings available.
	 */
	function get_form_editor_field_settings() {
		return array(
			'label_setting',

		);
	}

	public function get_entry_inputs() {
		$this->inputs = array(
				array(
					'id'           => $this->id . '.1',
					'title'        => esc_html__( 'Match Type', GF_CAMPAIGN_FIELD_SLUG ),
					'label'			=> esc_html__( 'Match Type', GF_CAMPAIGN_FIELD_SLUG ),
					'default_value' => array('aliases' => GF_CAMPAIGN_MERGETAG_MATCHTYPE),

				),
				array(
					'id'           => $this->id . '.2',
					'title'        => esc_html__( 'GLCID', GF_CAMPAIGN_FIELD_SLUG ),
					'label'			=> esc_html__( 'GLCID', GF_CAMPAIGN_FIELD_SLUG ),
					'default_value' => array('aliases' => GF_CAMPAIGN_MERGETAG_GLCID),
				),

			);

		return $this->inputs;
	}

	/**
	 * Gets the HTML markup for the field input.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFCommon::get_field_input()
	 * @uses    GF_Field::is_entry_detail()
	 * @uses    GF_Field::is_form_editor()
	 * @uses    GF_Field_Name::$size
	 * @uses    GF_Field_Name::$id
	 * @uses    GF_Field_Name::$subLabelPlacement
	 * @uses    GF_Field_Name::$isRequired
	 * @uses    GF_Field_Name::$failed_validation
	 * @uses    GFForms::get()
	 * @uses    GFFormsModel::get_input()
	 * @uses    GFCommon::get_input_placeholder_attribute()
	 * @uses    GFCommon::get_tabindex()
	 * @uses    GFCommon::get_field_placeholder_attribute()
	 *
	 * @param array      $form  The Form Object.
	 * @param string     $value The value of the field. Defaults to empty string.
	 * @param array|null $entry The Entry Object. Defaults to null.
	 *
	 * @return string The HTML markup for the field input.
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {

		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$is_admin = $is_entry_detail || $is_form_editor;

		$form_id  = $form['id'];
		$id       = intval( $this->id );
		$field_id = $is_admin || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
		$form_id  = ( $is_admin ) && empty( $form_id ) ? rgget( 'id' ) : $form_id;

		$size         = $this->size;
		$disabled_text = $is_form_editor ? "disabled='disabled'" : '';
		$class_suffix = ($is_entry_detail) ? '_admin' : '';
		$field_type  =  ($is_form_editor) ? 'text' : 'hidden';

		$matchType = '';
		$glcid  = '';

		if ( is_array( $value ) ) {
			$matchType =  esc_attr( GFForms::get( $this->id . '.1', $value ) );
			$glcid =  esc_attr( GFForms::get( $this->id . '.2', $value ) );
		}

		$matchType_input = GFFormsModel::get_input( $this, $this->id . '.1' );
		$glcid_input = GFFormsModel::get_input( $this, $this->id . '.2' );

		$matchType_label  = esc_attr__( 'Match Type', GF_CAMPAIGN_FIELD_SLUG );
		$glcid_label  = esc_attr__( 'GLCID', GF_CAMPAIGN_FIELD_SLUG );

		$style = ( $is_admin && rgar( $matchType_input, 'isHidden' ) ) ? "style='display:none;'" : '';
		if ( $is_admin || ! rgar( $matchType_input, 'isHidden' ) ) {
			$mt_markup = "<span id='{$field_id}_1_container' {$style}>
	      <input type='{$field_type}' name='input_{$id}.1' id='{$field_id}_1' value='" . $matchType ."' placeholder='{$matchType_label}' {$disabled_text} />
      </span>";
		}

		$style = ( $is_admin && rgar( $glcid_input, 'isHidden' ) ) ? "style='display:none;'" : '';
		if ( $is_admin || ! rgar( $glcid_input, 'isHidden' ) ) {
			$glcid_markup = "<span id='{$field_id}_2_container' {$style}>
	      <input type='{$field_type}' name='input_{$id}.2' id='{$field_id}_2' value='" . $glcid . "' placeholder='{$glcid_label}' {$disabled_text} />
      </span>";
		}

		return "<div class='ginput_complex{$class_suffix} ginput_container gfield_aq_sem' id='{$field_id}'>
        {$mt_markup}
				{$glcid_markup}
    </div>";

	}

	/**
	 * Defines the CSS class to be applied to the field label.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GF_Field::get_field_content()
	 *
	 * @return string The CSS class.
	 */
	public function get_field_label_class() {
		return 'gfield_label gfield_label_before_sem';
	}

	/**
	 * Returns the field markup; including field label, description, validation, and the form editor admin buttons.
	 *
	 * The {FIELD} placeholder will be replaced in GFFormDisplay::get_field_content with the markup returned by GF_Field::get_field_input().
	 *
	 * @param string|array $value                The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param bool         $force_frontend_label Should the frontend label be displayed in the admin even if an admin label is configured.
	 * @param array        $form                 The Form Object currently being processed.
	 *
	 * @return string
	 */
	public function get_field_content( $value, $force_frontend_label, $form ) {

		$field_label = $this->get_field_label( $force_frontend_label, $value );

		$validation_message = ( $this->failed_validation && ! empty( $this->validation_message ) ) ? sprintf( "<div class='gfield_description validation_message'>%s</div>", $this->validation_message ) : '';

		$is_form_editor  = $this->is_form_editor();
		$is_entry_detail = $this->is_entry_detail();
		$is_admin        = $is_form_editor || $is_entry_detail;

		$required_div = $is_admin || $this->isRequired ? sprintf( "<span class='gfield_required'>%s</span>", $this->isRequired ? '*' : '' ) : '';

		$admin_buttons = $this->get_admin_buttons();

		$target_input_id = $this->get_first_input_id( $form );

		$for_attribute = empty( $target_input_id ) ? '' : "for='{$target_input_id}'";

		$description = $this->get_description( $this->description, 'gfield_description' );
		if ( $this->is_description_above( $form ) ) {
			$clear         = $is_admin ? "<div class='gf_clear'></div>" : '';
			if ($is_admin) {
				$field_content = sprintf( "%s<label class='%s' $for_attribute >%s%s</label>%s{FIELD}%s$clear", $admin_buttons, esc_attr( $this->get_field_label_class() ), esc_html( $field_label ), $required_div, $description, $validation_message );
			} else {
				$field_content = sprintf( "{FIELD}%s", $validation_message );
			}
		} else {
			if ($is_admin) {
				$field_content = sprintf( "%s<label class='%s' $for_attribute >%s%s</label>{FIELD}%s%s", $admin_buttons, esc_attr( $this->get_field_label_class() ), esc_html( $field_label ), $required_div, $description, $validation_message );
			} else {
				$field_content = sprintf( "{FIELD}%s", $validation_message );
			}
		}

		return $field_content;
	}

	/**
	 * Gets the field value to be displayed on the entry detail page.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFCommon::get_lead_field_display()
	 * @uses    GF_Field_Name::$id
	 *
	 * @param array|string $value    The value of the field input.
	 * @param string       $currency Not used.
	 * @param bool         $use_text Not used.
	 * @param string       $format   The format to output the value. Defaults to 'html'.
	 * @param string       $media    Not used.
	 *
	 * @return array|string The value to be displayed on the entry detail page.
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		if ( is_array( $value ) ) {
			$matchType = trim( rgget( $this->id . '.1', $value ) );
			$glcid = trim( rgget( $this->id . '.2', $value ) );

			if ( $format === 'html' ) {
				$matchType = esc_html( $matchType );
				$glcid = esc_html( $glcid );

				$line_break = '<br />';
				$pre_label = '<b>';
				$post_label = '</b>';
			} else {
				$line_break = "\n";
				$pre_label = $post_label = '';
			}

			$return = ! empty( $matchType) ? $pre_label . '- Match Type: ' . $post_label . $matchType : '';
			$return .= ! empty( $return ) && ! empty( $glcid ) ? $line_break : '';
			$return .= ! empty( $glcid) ? $pre_label . '- GLCID: ' . $post_label . $glcid : '';
		} else {
			$return = "NO ARRAY" . $value;
		}

		return $return;
	}

	/**
	 * Gets a property value from an input.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GF_Field_Name::validate()
	 * @uses    GFFormsModel::get_input()
	 *
	 * @param int    $input_id      The input ID to obtain the property from.
	 * @param string $property_name The property name to search for.
	 *
	 * @return null|string The property value if found. Otherwise, null.
	 */
	public function get_input_property( $input_id, $property_name ) {
		$input = GFFormsModel::get_input( $this, $this->id . '.' . (string) $input_id );

		return rgar( $input, $property_name );
	}

	/**
	 * Sanitizes the field settings choices.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFFormDetail::add_field()
	 * @used-by GFFormsModel::sanitize_settings()
	 * @uses    GF_Field::sanitize_settings()
	 * @uses    GF_Field::sanitize_settings_choices()
	 *
	 * @return void
	 */
	public function sanitize_settings() {
		parent::sanitize_settings();
		if ( is_array( $this->inputs ) ) {
			foreach ( $this->inputs as &$input ) {
				if ( isset ( $input['choices'] ) && is_array( $input['choices'] ) ) {
					$input['choices'] = $this->sanitize_settings_choices( $input['choices'] );
				}
			}
		}
	}

	/**
	 * Gets the field value to be used when exporting.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFExport::start_export()
	 * @used-by GFAddOn::get_field_value()
	 * @used-by GFAddOn::get_full_name()
	 *
	 * @param array  $entry    The Entry Object.
	 * @param string $input_id The input ID to format. Defaults to empty string. If not set, uses t
	 * @param bool   $use_text Not used.
	 * @param bool   $is_csv   Not used.
	 *
	 * @return string The field value.
	 */
	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		if ( empty( $input_id ) ) {
			$input_id = $this->id;
		}

		if ( absint( $input_id ) == $input_id ) {
			// If field is simple (one input), simply return full content.
			$name = rgar( $entry, $input_id );
			if ( ! empty( $name ) ) {
				return $name;
			}

			// Complex field (multiple inputs). Join all pieces and create name.
			$matchType = trim( rgar( $entry, $input_id . '.1' ) );
			$glcid = trim( rgar( $entry, $input_id . '.2' ) );

			$calcresult = $matchType;
			$calcresult .= ! empty( $glcid ) && ! empty( $glcid ) ? ' ' . $glcid : $glcid;

			return $calcresult;
		} else {

			return rgar( $entry, $input_id );
		}
	}

	/**
	 * Returns the field admin buttons for display in the form editor.
	 *
	 * @return string
	 */
	public function get_admin_buttons() {

		$duplicate_field_link = '';

		$delete_field_link = "<a class='field_delete_icon' id='gfield_delete_{$this->id}' title='" . esc_attr__( 'click to delete this field', 'gravityforms' ) . "' href='#' onclick='DeleteField(this); return false;' onkeypress='DeleteField(this); return false;'><i class='fa fa-times fa-lg'></i></a>";
		$delete_field_link = apply_filters( 'gform_delete_field_link', $delete_field_link );
		$field_type_title  = esc_html( GFCommon::get_field_type_title( $this->type ) );

		$is_form_editor  = $this->is_form_editor();
		$is_entry_detail = $this->is_entry_detail();
		$is_admin        = $is_form_editor || $is_entry_detail;

		$admin_buttons = $is_admin ? "<div class='gfield_admin_icons'><div class='gfield_admin_header_title'>{$field_type_title} : " . esc_html__( 'Field ID', 'gravityforms' ) . " {$this->id}</div>" . $delete_field_link . $duplicate_field_link . "<a class='field_edit_icon edit_icon_collapsed' title='" . esc_attr__( 'click to expand and edit the options for this field', 'gravityforms' ) . "'><i class='fa fa-caret-down fa-lg'></i></a></div>" : '';

		return $admin_buttons;
	}

	/**
	 * Returns the scripts to be included for this field type in the form editor.
	 *
	 * @return string
	 */
	public function get_form_inline_script_on_page_render($form) {
		//add_action('wp_footer', array($this, 'add_sem_values'), 100 );

		return '';
	}

	public function add_sem_values() {
		// Insert JS for campaigns based on settings
		$script = '<script>' . PHP_EOL;
		$script .= "var semfields = document.getElementsByClassName('gfield_aq_sem');" . PHP_EOL;
		$script .= "for( var i = 0; i < semfields.length; i++) {" . PHP_EOL;
		$script .= "  if (AqMatchType != '') { document.getElementById(semfields[i].id + \"_1\").value = AqMatchType.toLowerCase(); }" . PHP_EOL;
		$script .= "  if (AqGLCID != '') { document.getElementById(semfields[i].id + \"_2\").value = AqGLCID; }" . PHP_EOL;
		$script .= "});" . PHP_EOL;
		$script .= '</script>' . PHP_EOL;

		echo $script;
	}

	public function check_sem_values() {
		// Insert JS for campaigns based on settings
		$script = '<script>' . PHP_EOL;

		$campaign = gf_campaign_addon();
		$attribution = $campaign->get_plugin_setting('aq_campaign_attribution');
		$matchtypeqs = $campaign->get_plugin_setting('aq_matchtype');

		$script .= "var AqMatchType = '';" . PHP_EOL;
		$script .= "var AqGLCID =  '';" . PHP_EOL;

		if ($attribution == 'first') {
			//Check if Cookie
			$script .= "AqMatchType = (AqGfCampaignData.getCookie('aq_matchtype') != '') ? AqGfCampaignData.getCookie('aq_matchtype') : '';" . PHP_EOL;
			$script .= "AqGLCID = (AqGfCampaignData.getCookie('aq_glcid') != '') ? AqGfCampaignData.getCookie('aq_glcid') : '';" . PHP_EOL;
		}

		$script .= "if (AqMatchType == '') { AqMatchType = (AqGfCampaignData.getUrlParameter('{$matchtypeqs}') != '') ? AqGfCampaignData.getUrlParameter('{$matchtypeqs}') : ''; }" . PHP_EOL;
		$script .= "if (AqGLCID == '') { AqGLCID = (AqGfCampaignData.getUrlParameter('glcid') != '') ? AqGfCampaignData.getUrlParameter('glcid') : ''; }" . PHP_EOL;

		if ($attribution != 'first') {
			//Check if Cookie
			$script .= "if (AqMatchType == '') { AqMatchType = (AqGfCampaignData.getCookie('aq_matchtype') != '') ? AqGfCampaignData.getCookie('aq_matchtype') : ''; }" . PHP_EOL;
			$script .= "if (AqGLCID == '') { AqGLCID = (AqGfCampaignData.getCookie('aq_glcid') != '') ? AqGfCampaignData.getCookie('aq_glcid') : ''; }" . PHP_EOL;
		}

		$script .= "if (AqMatchType != '') { AqGfCampaignData.setCookie('aq_matchtype', AqMatchType);  }" . PHP_EOL;
		$script .= "if (AqGLCID != '') { AqGfCampaignData.setCookie('aq_glcid', AqGLCID); }" . PHP_EOL;

		$script .= '</script>' . PHP_EOL;
		echo $script;
	}

}

// Registers the Name field with the field framework.
GF_Fields::register( new GF_Field_SEM_Values() );
