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

	public function plugin_help_links() {

		return array(
			array(
				'url'  => 'http://example.com',
				'text' => __( 'Plugin Documentation', 'textdomain' ),
				'icon' => '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" fill="none" height="14.976" width="16.048"><path fill="#242748" d="M14.293 5.44c.747-.267 1.547.267 1.654 1.013.053.32.053.694.053 1.014 0 .32 0 .693-.16 1.013a1.222 1.222 0 01-1.227 1.12c-.16 0-.266 0-.426-.053-.374-.16-.747.053-.907.373-.16.32-.373.693-.587 1.013-.213.32-.16.747.107.96.267.214.427.587.427.96 0 .374-.16.747-.48.96-.534.427-1.12.747-1.707 1.014-.16.053-.32.106-.48.106-.587 0-1.12-.426-1.227-1.013-.053-.373-.426-.64-.8-.587-.373.054-.8.054-1.173 0-.427-.053-.747.214-.8.587a1.255 1.255 0 01-1.227 1.013c-.16 0-.32-.053-.48-.106-.586-.267-1.173-.587-1.706-1.014-.267-.213-.48-.586-.48-.96 0-.373.16-.746.426-.96.267-.213.32-.64.107-.96-.213-.32-.427-.64-.587-1.013a.76.76 0 00-.906-.427C.96 9.76.16 9.227.053 8.48 0 8.16 0 7.787 0 7.467c0-.32 0-.694.053-1.014.054-.64.587-1.12 1.227-1.12.16 0 .267 0 .427.054.373.16.746-.054.906-.374.16-.32.374-.693.587-1.013.267-.32.213-.693-.107-.96-.266-.213-.426-.587-.426-.96s.213-.747.48-.96C3.68.693 4.267.373 4.907.107c.16-.054.32-.107.48-.107.586 0 1.12.427 1.226 1.013.054.374.427.64.8.587.374-.053.8-.053 1.174 0 .426.053.746-.213.8-.587A1.255 1.255 0 0110.613 0c.127 0 .254.033.407.074l.127.033c.586.266 1.173.586 1.706 1.013.267.213.48.587.48.96s-.16.747-.426 1.013c-.267.214-.32.64-.107.96.213.32.427.64.587 1.014.16.32.586.48.906.373zm-6.292 4.693a2.64 2.64 0 002.666-2.666A2.64 2.64 0 008.001 4.8a2.64 2.64 0 00-2.667 2.667A2.64 2.64 0 008 10.133z" clip-rule="evenodd" fill-rule="evenodd"/></svg>',
			),
		);
	}

	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

	/**
	 * Creates a custom page for this add-on.
	 */
	public function plugin_page() {
		echo 'This page appears in the Forms menu';

		$this->render_beacon();
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
				'title'  => esc_html__( 'Simple Feed Settings', 'simplefeedaddon' ),
				'fields' => array(
					array(
						'label'   => esc_html__( 'Feed name', 'simplefeedaddon' ),
						'type'    => 'text',
						'name'    => 'feedName',
						'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
						'class'   => 'small',
					),
					array(
						'label'   => esc_html__( 'Textbox', 'simplefeedaddon' ),
						'type'    => 'text',
						'name'    => 'mytextbox',
						'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
						'class'   => 'small',
					),
					array(
						'label'   => esc_html__( 'My checkbox', 'simplefeedaddon' ),
						'type'    => 'checkbox',
						'name'    => 'mycheckbox',
						'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'Enabled', 'simplefeedaddon' ),
								'name'  => 'mycheckbox',
							),
						),
					),
					array(
						'name'      => 'mappedFields',
						'label'     => esc_html__( 'Map Fields', 'simplefeedaddon' ),
						'type'      => 'field_map',
						'field_map' => array(
							array(
								'name'       => 'email',
								'label'      => esc_html__( 'Email', 'simplefeedaddon' ),
								'required'   => 0,
								'field_type' => array( 'email', 'hidden' ),
								'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
							),
							array(
								'name'     => 'name',
								'label'    => esc_html__( 'Name', 'simplefeedaddon' ),
								'required' => 0,
							),
							array(
								'name'       => 'phone',
								'label'      => esc_html__( 'Phone', 'simplefeedaddon' ),
								'required'   => 0,
								'field_type' => 'phone',
							),
						),
					),
					array(
						'name'           => 'condition',
						'label'          => esc_html__( 'Condition', 'simplefeedaddon' ),
						'type'           => 'feed_condition',
						'checkbox_label' => esc_html__( 'Enable Condition', 'simplefeedaddon' ),
						'instructions'   => esc_html__( 'Process this simple feed if', 'simplefeedaddon' ),
					),
				),
			),
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
