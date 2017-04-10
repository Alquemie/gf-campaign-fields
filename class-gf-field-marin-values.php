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
			return esc_attr__( 'Marin Tracking', GF_CAMPAIGN_FIELD_SLUG );
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
						'title'        => esc_html__( 'Creative ID', GF_CAMPAIGN_FIELD_SLUG ),
						'label'			=> esc_html__( 'PCRID', GF_CAMPAIGN_FIELD_SLUG ),
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

			$mkwid = '';
			$pcrid  = '';

			if ( is_array( $value ) ) {
				$matchType =  esc_attr( GFForms::get( $this->id . '.1', $value ) );
				$glcid =  esc_attr( GFForms::get( $this->id . '.2', $value ) );
			}

			$mkwid_input = GFFormsModel::get_input( $this, $this->id . '.1' );
			$pcrid_input = GFFormsModel::get_input( $this, $this->id . '.2' );

			$mkwid_label  = esc_attr__( 'MKWID', GF_CAMPAIGN_FIELD_SLUG );
			$pcrid_label  = esc_attr__( 'PCRID', GF_CAMPAIGN_FIELD_SLUG );

			$style = ( $is_admin && rgar( $mkwid_input, 'isHidden' ) ) ? "style='display:none;'" : '';
			if ( $is_admin || ! rgar( $mkwid_input, 'isHidden' ) ) {
				$mkwid_markup = "<span id='{$field_id}_1_container' {$style}>
		      <input type='{$field_type}' name='input_{$id}.1' id='{$field_id}_1' value='" . $mkwid ."' placeholder='{$mkwid_label}' {$disabled_text} />
	      </span>";
			}

			$style = ( $is_admin && rgar( $pcrid_input, 'isHidden' ) ) ? "style='display:none;'" : '';
			if ( $is_admin || ! rgar( $pcrid_input, 'isHidden' ) ) {
				$pcrid_markup = "<span id='{$field_id}_2_container' {$style}>
		      <input type='{$field_type}' name='input_{$id}.2' id='{$field_id}_2' value='" . $pcrid . "' placeholder='{$pcrid_label}' {$disabled_text} />
	      </span>";
			}

			return "<div class='ginput_complex{$class_suffix} ginput_container gfield_aq_marin' id='{$field_id}'>
	        {$mkwid_markup}
					{$pcrid_markup}
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
			return 'gfield_label gfield_label_before_marin';
		}

		/*
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
		*/

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
		/*
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
		*/

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
				$mkwid = trim( rgget( $this->id . '.1', $value ) );
				$pcrid = trim( rgget( $this->id . '.2', $value ) );

				if ( $format === 'html' ) {
					$mkwid = esc_html( $matchType );
					$pcrid = esc_html( $glcid );

					$line_break = '<br />';
					$pre_label = '<b>';
					$post_label = '</b>';
				} else {
					$line_break = "\n";
					$pre_label = $post_label = '';
				}

				$return = ! empty( $mkwid) ? $pre_label . '- Marin KW ID: ' . $post_label . $mkwid : '';
				$return .= ! empty( $return ) && ! empty( $glcid ) ? $line_break : '';
				$return .= ! empty( $pcrid) ? $pre_label . '- Marin Creative ID: ' . $post_label . $pcrid : '';
			} else {
				$return = "NO ARRAY" . $value;
			}

			return $return;
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
			$pcridqs = $campaign->get_plugin_setting('aq_marin_pcrid');

			$script = '<script>' . PHP_EOL;
			$script .= "var AqMKWIDQS = '{$mkwidqs}';" . PHP_EOL;
			$script .= "var AqPCRIDQS = '{$pcridqs}';" . PHP_EOL;
			$script .= '</script>' . PHP_EOL;
			echo $script;
		}

}

// Registers the Name field with the field framework.
GF_Fields::register( new AqGF_Marin() );
