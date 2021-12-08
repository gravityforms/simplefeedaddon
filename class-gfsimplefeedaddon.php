<?php

GFForms::include_feed_addon_framework();

class GFSimpleFeedAddOn extends GFFeedAddOn {

	protected $_version = GF_SIMPLE_FEED_ADDON_VERSION;
	protected $_min_gravityforms_version = '1.9.16';
	protected $_slug = 'simplefeedaddon';
	protected $_path = 'simplefeedaddon/simplefeedaddon.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Gravity Forms Simple Feed Add-On';
	protected $_short_title = 'Simple Feed Add-On';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFSimpleFeedAddOn
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFSimpleFeedAddOn();
		}

		return self::$_instance;
	}

	/**
	 * Plugin starting point. Handles hooks, loading of language files and PayPal delayed payment support.
	 */
	public function init() {

		parent::init();
		$this->add_delayed_payment_support(
			array(
				'option_label' => esc_html__( 'Subscribe contact to service x only when payment is received.', 'simplefeedaddon' )
			)
		);

	}


	// # FEED PROCESSING -----------------------------------------------------------------------------------------------

	/**
	 * Process the feed e.g. subscribe the user to a list.
	 *
	 * @param array $feed The feed object to be processed.
	 * @param array $entry The entry object currently being processed.
	 * @param array $form The form object currently being processed.
	 *
	 * @return bool|void
	 */
	public function process_feed( $feed, $entry, $form ) {
		$feedName  = $feed['meta']['feedName'];
		$mytextbox = $feed['meta']['mytextbox'];
		$checkbox  = $feed['meta']['mycheckbox'];

		// Retrieve the name => value pairs for all fields mapped in the 'mappedFields' field map.
		$field_map = $this->get_field_map_fields( $feed, 'mappedFields' );

		// Loop through the fields from the field map setting building an array of values to be passed to the third-party service.
		$merge_vars = array();
		foreach ( $field_map as $name => $field_id ) {

			// Get the field value for the specified field id
			$merge_vars[ $name ] = $this->get_field_value( $form, $entry, $field_id );

		}

		// Send the values to the third-party service.
	}

	/**
	 * Custom format the phone type field values before they are returned by $this->get_field_value().
	 *
	 * @param array $entry The Entry currently being processed.
	 * @param string $field_id The ID of the Field currently being processed.
	 * @param GF_Field_Phone $field The Field currently being processed.
	 *
	 * @return string
	 */
	public function get_phone_field_value( $entry, $field_id, $field ) {

		// Get the field value from the Entry Object.
		$field_value = rgar( $entry, $field_id );

		// If there is a value and the field phoneFormat setting is set to standard reformat the value.
		if ( ! empty( $field_value ) && $field->phoneFormat == 'standard' && preg_match( '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/', $field_value, $matches ) ) {
			$field_value = sprintf( '%s-%s-%s', $matches[1], $matches[2], $matches[3] );
		}

		return $field_value;
	}

	// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
	public function scripts() {
		$scripts = array(
			array(
				'handle'  => 'my_script_js',
				'src'     => $this->get_base_url() . '/js/my_script.js',
				'version' => $this->_version,
				'deps'    => array( 'jquery' ),
				'strings' => array(
					'first'  => esc_html__( 'First Choice', 'simplefeedaddon' ),
					'second' => esc_html__( 'Second Choice', 'simplefeedaddon' ),
					'third'  => esc_html__( 'Third Choice', 'simplefeedaddon' ),
				),
				'enqueue' => array(
					array(
						'admin_page' => array( 'form_settings' ),
						'tab'        => 'simplefeedaddon',
					),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {

		$styles = array(
			array(
				'handle'  => 'my_styles_css',
				'src'     => $this->get_base_url() . '/css/my_styles.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( 'field_types' => array( 'poll' ) ),
				),
			),
		);

		return array_merge( parent::styles(), $styles );
	}

	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

	/**
	 * Creates a custom page for this add-on.
	 */
	public function plugin_page() {
		echo 'This page appears in the Forms menu';
	}

	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {
		return array(
			array(
				'title'  => esc_html__( 'Simple Add-On Settings', 'simplefeedaddon' ),
				'fields' => array(
					array(
						'name'    => 'textbox',
						'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
						'label'   => esc_html__( 'This is the label', 'simplefeedaddon' ),
						'type'    => 'text',
						'class'   => 'small',
					),
				),
			),
		);
	}

	/**
	 * Configures the settings which should be rendered on the feed edit page in the Form Settings > Simple Feed Add-On area.
	 *
	 * @return array
	 */
	public function feed_settings_fields() {
		return array(
			array(
				'title'  	=> esc_html__( 'Constituent Details', 'bloomerangaddon' ),
				'fields' 	=> array(
					array(
						'name'		=> 'type',
						'type'     	=> 'select',
						'label'     => esc_html__( 'Constituent Type', 'bloomerangaddon' ),
						'required'  => true,
						'onchange' 	=> "jQuery(this).parents('form').submit();",
						'choices'	=> array(
							array(
								'label' => esc_html__( 'Select a type', 'bloomerangaddon' ),
								'value' => '',
							),
							array(
								'label' => esc_html__( 'Individual', 'bloomerangaddon' ),
								'value' => 'Individual'
							),
							array(
								'label' => esc_html__( 'Organization', 'bloomerangaddon' ),
								'value' => 'Organization'
							)
						),
						'tooltip'  	=> esc_html__( 'Bloomerang requires that all constituents are added as either an individual or organization.', 'bloomerangaddon' )
					),
					array(
						'name'      => 'consituentFields',
						'label'     => esc_html__( 'Map Fields', 'bloomerangaddon' ),
						'type'      => 'field_map',
						'tooltip'   => esc_html__( 'Select which Gravity Form fields pair with their respective Bloomerang constituent fields.<br><br>For custom fields, make sure you write the exact name of the custom field and only allow the possible Bloomerang values on the form. For example, if the custom field in Bloomerang is a dropdown, you need to match the dropdown options in Gravity Forms.<br><br>If you don\'t want to map a field then leave the "Form Field" dropdown set to "Select a Field".', 'bloomerangaddon' ),
						'field_map' => array(
							array(
								'name'          => 'LastName',
								'label'         => esc_html__( 'Last Name', 'bloomerangaddon' ),
								'required'      => true,
								'field_type'    => array( 'name', 'text', 'hidden' ),
								'default_value' => '',
							),
							array(
								'name'          => 'FullName',
								'label'         => esc_html__( 'Organization Name', 'bloomerangaddon' ),
								'required'      => true,
								'field_type'    => array( 'name', 'text', 'hidden' ),
								'dependency' 	=> array( 'field' => 'type', 'values' => array( 'Organization' ) )
							),
						)
					),
				)
			)
		);
	}

	/**
	 * Configures which columns should be displayed on the feed list page.
	 *
	 * @return array
	 */
	public function feed_list_columns() {
		return array(
			'feedName'  => esc_html__( 'Name', 'simplefeedaddon' ),
			'mytextbox' => esc_html__( 'My Textbox', 'simplefeedaddon' ),
		);
	}

	/**
	 * Format the value to be displayed in the mytextbox column.
	 *
	 * @param array $feed The feed being included in the feed list.
	 *
	 * @return string
	 */
	public function get_column_value_mytextbox( $feed ) {
		return '<b>' . rgars( $feed, 'meta/mytextbox' ) . '</b>';
	}

	/**
	 * Prevent feeds being listed or created if an api key isn't valid.
	 *
	 * @return bool
	 */
	public function can_create_feed() {

		// Get the plugin settings.
		$settings = $this->get_plugin_settings();

		// Access a specific setting e.g. an api key
		$key = rgar( $settings, 'apiKey' );

		return true;
	}

}
