<?php
require_once ES_PROJECT_INC_DIR . 'lib/scssphp/scss.inc.php';
require_once ES_PROJECT_INC_DIR . 'lib/scssphp/example/Server.php';
use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Server;

/**
* Class EstProject_Settings
 *
 * A simple settings framework that works with the customizer in magical ways.
*/
class EstProject_Settings {

	/**
	 * @var array Default setting values
	 */
	private $defaults;

	/**
	 * @var The current theme name
	 */
	private $theme_name;

	/**
	 * @var array The theme settings
	 */
	private $settings;

	/**
	 * @var array The settings sections
	 */
	private $sections;

	static $control_classes = array(
		'media' => 'WP_Customize_Media_Control',
		'color' => 'WP_Customize_Color_Control',
	);

	static $sanitize_callbacks = array(
		'url' => 'esc_url_raw',
		'color' => 'sanitize_hex_color',
	);

	function __construct(){
		$this->add_actions();

		$this->defaults = array();
		$this->settings = array();
		$this->sections = array();
		$this->loc = array();

		if( !empty( $_POST['wp_customize'] ) && $_POST['wp_customize'] == 'on' && is_customize_preview() ) {
			add_filter( 'est_project_setting', array( $this, 'customizer_filter' ), 15, 2 );
		}

	}

	/**
	 * Create the singleton
	 *
	 * @return EstProject_Settings
	 */
	static function single(){
		static $single;
		return empty( $single ) ? $single = new self() : $single;
	}

	/**
	 * Get a theme setting value
	 *
	 * @param $setting
	 *
	 * @return string
	 */
	function get( $setting ) {
		static $old_settings = false;
		if( $old_settings === false ) {
			$old_settings = get_option( get_template() . '_theme_settings' );
		}

		if( isset( $old_settings[$setting] ) ) {
			$default = $old_settings[$setting];
		}
		else {
			$default = isset( $this->defaults[$setting] ) ? $this->defaults[$setting] : false;
		}

		// Handle setting migration
		return apply_filters( 'est_project_setting', get_theme_mod( 'theme_settings_' . $setting, $default ), $setting );
	}


	/**
	 * Filter EstProject settings based on customizer values. Gets around early use of setting values in customizer preview.
	 *
	 * @param $value
	 * @param $setting
	 *
	 * @return mixed
	 */
	function customizer_filter( $value, $setting ){
		if (
			empty( $_REQUEST['nonce'] ) ||
			!wp_verify_nonce( $_REQUEST['nonce'], 'preview-customize_' . get_stylesheet() )
		) return $value;

		static $customzier_values = null;
		if( is_null( $customzier_values ) && ! empty( $_POST['customized'] ) ) {
			$customzier_values =  json_decode( stripslashes( $_POST['customized'] ), true );
		}

		if( isset( $customzier_values[ 'theme_settings_' . $setting ] ) ) {
			$value = $customzier_values[ 'theme_settings_' . $setting ];
		}

		return $value;
	}

	/**
	 * Get all theme settings values currently in the database.
	 *
	 * @param bool $defaults Should we add the defaults.
	 *
	 * @return array|void
	 */
	function get_all( $defaults = false ){
		$settings = get_theme_mods();
		if( empty($settings) ) return array();

		foreach( array_keys($settings) as $k ) {
			if( strpos( $k, 'theme_settings_' ) !== 0 ) {
				unset($settings[$k]);
			}
		}

		if( $defaults ) {
			$settings = wp_parse_args( $settings, $this->defaults );
		}

		return $settings;
	}

	/**
	 * Set a theme setting value. Simple wrapper for set theme mod.
	 *
	 * @param $setting
	 * @param $value
	 */
	function set( $setting, $value ) {
		set_theme_mod( 'theme_settings_' . $setting, $value );
	}

	/**
	 * Add all the necessary actions
	 */
	function add_actions(){
		add_action( 'after_setup_theme', array( $this, 'init' ), 5 );
		add_action( 'customize_register', array( $this, 'customize_register' ) );

		add_action( 'customize_preview_init', array( $this, 'enqueue_preview' ) );

		add_action( 'customize_save_after', array( $this, 'regenerate_css' ), 10, 1 );
	}

	/**
	 * Check if a setting is currently at its default value
	 *
	 * @param string $setting The setting name.
	 *
	 * @return bool Is the setting current at its default value.
	 */
	function is_default( $setting ){
		$default = $this->get_default( $setting );
		return $this->get($setting) == $default;
	}

	/**
	 * Get the default value for the setting
	 *
	 * @param string $setting The name of the setting
	 *
	 * @return bool|mixed
	 */
	function get_default( $setting ) {
		return isset( $this->defaults[$setting] ) ? $this->defaults[$setting] : false;
	}

	/**
	 * Initialize the theme settings
	 */
	function init(){
		$theme = wp_get_theme();
		$this->theme_name = $theme->get_template();
		$this->defaults = apply_filters( 'est_project_settings_defaults', $this->defaults );
	}

	/**
	 * @param array $settings
	 */
    function configure($settings){
        foreach ($settings as $section_id => $section) {
            $this->add_section($section_id, !empty($section['title']) ? $section['title'] : '');
            $fields = !empty($section['fields']) ? $section['fields'] : array();
            foreach ($fields as $field_id => $field) {
                $args = array_merge(
                    !empty($field['args']) ? $field['args'] : array(),
                    $field
                );
                unset($args['label']);
                unset($args['type']);

                $this->add_field(
                    $section_id,
                    $field_id,
                    $field['type'],
                    !empty($field['label']) ? $field['label'] : '',
                    $args
                );

            }
        }
    }

	/**
	 * @param $id
	 * @param $title
	 * @param string|bool $after Add this section after another one
	 */
	function add_section( $id, $title, $after = false ) {

		if( $after === false ) {
			$index = null;
		}
		else if( $after === '' ) {
			$index = 0;
		}
		else if( $after !== false ) {
			$index = array_search( $after, array_keys( $this->sections ) ) + 1;
			if( $index == count( array_keys($this->sections) ) ) {
				$index = null;
			}
		}

		$new_section = array( $id => array(
			'id' => $id,
			'title' => $title,
		) );

		if( $index === null ) {
			// Null means we add this at the end or the current position
			$this->sections = array_merge(
				$this->sections,
				$new_section
			);
		}
		else if( $index === 0 ) {
			$this->sections = array_merge(
				$new_section,
				$this->sections
			);
		}
		else {
			$this->sections = array_merge(
				array_slice( $this->sections, 0, $index, true ),
				$new_section,
				array_slice( $this->sections, $index, count($this->sections), true )
			);
		}

		if( empty($this->settings[$id]) ) {
			$this->settings[$id] = array();
		}
	}

	/**
	 * Add a new settings field
	 *
	 * @param $section
	 * @param $id
	 * @param $type
	 * @param null $label
	 * @param array $args
	 * @param string|bool $after Add this field after another one
	 */
	function add_field( $section, $id, $type, $label = null, $args = array(), $after = false ) {

		if( empty($this->settings[$section]) ) {
			$this->settings[$section] = array();
		}

		$new_field = array(
			'id' => $id,
			'type' => $type,
			'label' => $label,
			'args' => $args,
		);

		if( isset($this->settings[$section][$id]) ) {
			$this->settings[$section][$id] = wp_parse_args(
				$new_field,
				$this->settings[$section][$id]
			);
		}

		if( $after === false ) {
			$index = null;
		}
		else if( $after === '' ) {
			$index = 0;
		}
		else if( $after !== false ) {
			$index = array_search( $after, array_keys( $this->settings[$section] ) ) + 1;
			if( $index == count( $this->settings[$section] ) ) {
				$index = null;
			}
		}

		if( $index === null ) {
			// Null means we add this at the end or the current position
			$this->settings[$section] = array_merge(
				$this->settings[$section],
				array( $id => $new_field )
			);
		}
		else if( $index === 0 ) {
			$this->settings[$section] = array_merge(
				array( $id => $new_field ),
				$this->settings[$section]
			);
		}
		else {
			$this->settings[$section] = array_merge(
				array_slice( $this->settings[$section], 0, $index, true ),
				array( $id => $new_field ),
				array_slice( $this->settings[$section], $index, count( $this->settings[$section] ), true )
			);
		}

	}

	/**
	 * Register everything for the customizer
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	function customize_register( $wp_customize ){
		// Let everything setup the settings
		if( !did_action( 'est_project_settings_init' ) ) {
			do_action( 'est_project_settings_init' );
		}

		// We'll use a single panel for theme settings
		if( method_exists($wp_customize, 'add_panel') ) {
			$wp_customize->add_panel( 'theme_settings', array(
				'title' => __( 'Theme Settings', 'es-project' ),
				'description' => __( 'Change settings for Estatik Project theme.', 'es-project' ),
				'priority' => 10,
			) );
		}

		// Add sections for what would have been tabs before
		$i = 0;
		foreach( $this->sections as $id => $args ) {
			$i++;
			$wp_customize->add_section( 'theme_settings_' . $id, array(
				'title' => $args['title'],
				'priority' => ( $i * 5 ) + 10,
				'panel' => 'theme_settings',
			) );
		}

		// Finally, add the settings
		foreach( $this->settings as $section_id => $settings ) {
			foreach( $settings as $setting_id => $setting_args ) {
				$control_class = false;

				// Setup the sanitize callback
				$sanitize_callback = 'sanitize_text_field';
				if( !empty( $setting_args['args']['sanitize_callback'] ) ) {
					$sanitize_callback = $setting_args['args']['sanitize_callback'];
				}
				else if( !empty( self::$sanitize_callbacks[ $setting_args['type'] ] ) ) {
					$sanitize_callback = self::$sanitize_callbacks[ $setting_args['type'] ];
				}

				// Get the default value
                $default = isset( $this->defaults[ $section_id . '_' . $setting_id ] ) ? $this->defaults[ $section_id . '_' . $setting_id ] : '';

				// Create the customizer setting
				$wp_customize->add_setting( 'theme_settings_' . $section_id . '_' . $setting_id , array(
					'default' => $default,
					'transport' => empty($setting_args['args']['live']) ? 'refresh' : 'postMessage',
					'capability' => 'edit_theme_options',
					'type' => 'theme_mod',
					'sanitize_callback' => $sanitize_callback,
				) );

				// Setup the control arguments for the controller
				$control_args = array(
					'label' => $setting_args['label'],
					'section'  => 'theme_settings_' . $section_id,
					'settings' => 'theme_settings_' . $section_id . '_' . $setting_id,
				);

				if( $setting_args['type'] == 'radio' || $setting_args['type'] == 'select' ) {
					if( !empty($setting_args['args']['options']) ) {
						$control_args['choices'] = $setting_args['args']['options'];
					}
				}

				if( !empty( $setting_args['args']['description'] ) ) {
					$control_args['description'] = $setting_args['args']['description'];
				}

				// Arguments for the range field
				if( $setting_args['type'] == 'media' ) {
					$control_args = wp_parse_args( $control_args, array(
						'section' => 'media',
						'mime_type' => 'image',
					) );
				}

				if( empty( $control_class ) ) {
					$control_class = !empty( self::$control_classes[ $setting_args['type'] ] ) ? self::$control_classes[ $setting_args['type'] ] : false;
				}

				if( !empty( $control_class ) ) {
					$wp_customize->add_control(
						new $control_class(
							$wp_customize,
							'theme_settings_' . $section_id . '_' . $setting_id,
							$control_args
						)
					);
				}
				else {
					$control_args['type'] = $setting_args['type'];
					$wp_customize->add_control(
						'theme_settings_' . $section_id . '_' . $setting_id,
						$control_args
					);
				}

			}
		}
	}

	/**
	 * Enqueue everything necessary for the live previewing in the Customizer
	 */
	function enqueue_preview(){
		if( !did_action('est_project_settings_init') ) {
			do_action('est_project_settings_init');
		}

		$values = array();
		foreach( $this->settings as $section_id => $section ) {
			foreach( $section as $setting_id => $setting ) {
				$values[$section_id . '_' . $setting_id] = $this->get($section_id . '_' . $setting_id);
			}
		}
	}


    /**
     * @param $manager
     * @return bool
     */
    function regenerate_css( $manager )
    {
        $customized = json_decode(stripslashes($_POST['customized']), true);

        if (!empty($customized['theme_settings_color_theme_color'])) {
            $color = $customized['theme_settings_color_theme_color'];
        } else {
            $color = '#de001d';
        }

        if ( $color ) {
            $scss = new Compiler();
            $scss->setFormatter('Leafo\ScssPhp\Formatter\Compressed');

            if (file_exists(ES_PROJECT_CUSTOM_STYLES_DIR . '_color.scss')) {
                $data = '$themeColor: ' . $color . ';';
                file_put_contents(ES_PROJECT_CUSTOM_STYLES_DIR . '_color.scss', $data);
            }
            $server = new Server(ES_PROJECT_CUSTOM_STYLES_DIR, null, $scss);
            $server->compileFile(ES_PROJECT_CUSTOM_STYLES_DIR . 'style.scss', ES_PROJECT_CUSTOM_STYLES_DIR . 'style.css');
        }

        return true;
    }

	/**
	 * Convert an attachment URL to a post ID
	 *
	 * @param $image_url
	 *
	 * @return mixed
	 */
	static function get_image_id( $image_url ){
		if( empty( $image_url ) ) return false;

		$attachment_id = wp_cache_get( $image_url, 'est_project_image_id' );

		if( $attachment_id === false ) {
			global $wpdb;
			$attachment = $wpdb->get_col(
				$wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )
			);
			$attachment_id = !empty($attachment[0]) ? $attachment[0] : 0;
			wp_cache_set( $image_url, $attachment_id, 'est_project_image_id', 86400 );
		}

		return $attachment_id;
	}
}

// Setup the single
EstProject_Settings::single();

/**
 * Access a single setting
 *
 * @param $setting string The name of the setting.
 *
 * @return mixed The setting value
 */
function est_project_setting( $setting ){
	return EstProject_Settings::single()->get( $setting );
}

/**
 * Set the value of a single setting. Included here for backwards compatibility.
 *
 * @param $setting
 * @param $value
 */
function est_project_settings_set( $setting, $value ){
	EstProject_Settings::single()->set( $setting, $value );
}
