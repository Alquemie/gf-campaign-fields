<?php

// If Gravity Forms isn't loaded, bail.
if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/* Field names */

$mkwid = ''; /* Marin Keyword ID */
$pcrid = ''; /* Marin Creative ID */

class AqGF_Marin extends GF_Field {

    public $type = 'aq_gf_marin_field';

		public function __construct( $data = array() ) {
			parent::__construct($data);
			add_action('wp_head', array($this, 'set_marin_parms'), 100 );
		}

		public function get_form_editor_field_title() {
			return esc_attr__( 'Marin KWID', GF_CAMPAIGN_FIELD_SLUG );
		}

		public function is_conditional_logic_supported(){
			return false;
		}

		function get_form_editor_field_settings() {
			return array(
				'label_setting',
			);
		}

		/**
		 * Assign the field button to the custom group.
		 *
		 * @return array
		 */
		public function get_form_editor_button() {
		    return array(
		        'group' => 'advanced_fields',
		        'text'  => $this->get_form_editor_field_title(),
		    );
		}

		public function get_entry_inputs() {
			$this->inputs = array(
					array(
						'id'           => $this->id . '.1',
						'title'        => esc_html__( 'Marin KW', GF_CAMPAIGN_FIELD_SLUG ),
						'label'			=> esc_html__( 'MKWID', GF_CAMPAIGN_FIELD_SLUG ),
						'default_value' => array('aliases' => GF_CAMPAIGN_MERGETAG_MATCHTYPE),

					),
					array(
						'id'           => $this->id . '.2',
						'title'        => esc_html__( 'Creative', GF_CAMPAIGN_FIELD_SLUG ),
						'label'			=> esc_html__( 'PCRID', GF_CAMPAIGN_FIELD_SLUG ),
						'default_value' => array('aliases' => GF_CAMPAIGN_MERGETAG_GLCID),
					),

				);

			return $this->inputs;
		}

		public function get_field_input( $form, $value = '', $entry = null ) {
			$form_id         = $form['id'];
			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor  = $this->is_form_editor();

			$id       = (int) $this->id;
			$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

			$disabled_text = $is_form_editor ? 'disabled="disabled"' : '';

			$field_type         =  'hidden';
			$class_attribute    = "class='gform_hidden gform_aq_mkwid'";
			$required_attribute = $this->isRequired ? 'aria-required="true"' : '';
			$invalid_attribute  = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';

			return sprintf( "<input name='input_%d' id='%s' type='$field_type' {$class_attribute} {$required_attribute} {$invalid_attribute} value='%s' %s/>", $id, $field_id, esc_attr( $value ), $disabled_text );
		}

		public function get_field_content( $value, $force_frontend_label, $form ) {
			$form_id         = $form['id'];
			$admin_buttons   = $this->get_admin_buttons();
			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor  = $this->is_form_editor();
			$is_admin        = $is_entry_detail || $is_form_editor;
			$field_label     = $this->get_field_label( $force_frontend_label, $value );
			$field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";
			$field_content   = ! $is_admin ? '{FIELD}' : $field_content = sprintf( "%s<label class='gfield_label' for='%s'>%s</label>{FIELD}", $admin_buttons, $field_id, esc_html( $field_label ) );

			return $field_content;
		}

		public function get_form_editor_inline_script_on_page_render() {

		    // set the default field label for the field
		    $script = sprintf( "function SetDefaultValues_%s(field) {field.label = '%s'; field.allowsPrepopulate = 1; field:adminOnly = 1; }", $this->type, $this->get_form_editor_field_title() ) . PHP_EOL;

		    return $script;
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
			// add_action('wp_footer', array($this, 'add_marin_kwid_values'), 100 );

			return '';
		}

		public function add_marin_kwid_values() {
			// Insert JS for campaigns based on settings
			$script = '<script>' . PHP_EOL;
			$script .= "var marinfields = document.getElementsByClassName('gform_aq_mkwid'); " . PHP_EOL;
			$script .= "for( var i = 0; i < marinfields.length; i++) { " . PHP_EOL;
    	$script .= "  if (AqMKWID != '') { document.getElementById(marinfields[i].id).value = AqMKWID; }" . PHP_EOL;
			$script .= "});" . PHP_EOL;
			$script .= '</script>' . PHP_EOL;

			echo $script;
		}

		public function set_marin_parms() {
			$campaign = gf_campaign_addon();
			$mkwidqs = $campaign->get_plugin_setting('aq_marin_kwid');

			$script = '<script>' . PHP_EOL;
			$script .= "var AqMKWIDQS = '{$mkwidqs}';" . PHP_EOL;
			$script .= '</script>' . PHP_EOL;
			echo $script;
		}

		public function check_marin_kwid_values() {
			// Insert JS for campaigns based on settings
			$campaign = gf_campaign_addon();
			$attribution = $campaign->get_plugin_setting('aq_campaign_attribution');
			$mkwidqs = $campaign->get_plugin_setting('aq_marin_kwid');

			$script = '<script>' . PHP_EOL;
			$script .= "var AqMKWID = '';" . PHP_EOL;

			if ($attribution == 'first') {
				//Check if Cookie
				$script .= "AqMKWID = (AqGfCampaignData.getCookie('aq_mkwid') != '') ? AqGfCampaignData.getCookie('aq_mkwid') : '';" . PHP_EOL;
			}

			$script .= "if (AqMKWID == '') { AqMKWID = (AqGfCampaignData.getUrlParameter('{$mkwidqs}') != '') ? AqGfCampaignData.getUrlParameter('{$mkwidqs}') : ''; }" . PHP_EOL;

			if ($attribution != 'first') {
				//Check if Cookie
				$script .= "if (AqMKWID == '') { AqMKWID = (AqGfCampaignData.getCookie('aq_mkwid') != '') ? AqGfCampaignData.getCookie('aq_mkwid') : ''; }" . PHP_EOL;
			}

			$script .= "if (AqMKWID != '') { AqGfCampaignData.setCookie('aq_mkwid', AqMKWID); }" . PHP_EOL;

			$script .= '</script>' . PHP_EOL;
			echo $script;
		}

}

// Registers the Name field with the field framework.
GF_Fields::register( new AqGF_Marin() );
