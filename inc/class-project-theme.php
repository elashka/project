<?php
/**
 * Estatik Project Theme functions and definitions
 * @package    Estatik_Theme_Project
 * @author     Estatik
 */

class Project_Theme {

	/**
	 * Theme version.
	 *
	 * @var string
	 */
	protected $_version;

	/**
	 * Theme instance.
	 *
	 * @var Estatik
	 */
	protected static $_instance;

	/**
	 * Returns the instance.
	 *
	 * @access public
	 * @return object
	 */
	public static function getInstance() {

		static $_instance = null;

		if ( is_null( $_instance ) ) {
			$_instance = new self;
		}

		return $_instance;
	}

	/**
	 * Constructor.
	 */
	protected function __construct()
	{
		$this->setup_actions();
		$this->init();
	}

	/**
	 * Initialize theme.
	 *
	 * @return void
	 */
	public static function run()
	{
		static::$_instance = static::getInstance();
	}

	/**
	 * Return theme version.
	 *
	 * @return string
	 */
	public static function getVersion()
	{
		$es_theme = wp_get_theme();
		return $es_theme->get( 'Version' );
	}

	/**
	 * Sets up initial actions.
	 *
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Theme setup.
		add_action( 'after_setup_theme', array( $this, 'theme_setup'             ),  5 );
		add_action( 'after_setup_theme', array( $this, 'load_text_domain' ) );

        add_action( 'tgmpa_register', array( $this, 'recommended_plugins' ) );
		add_filter( 'pt-ocdi/import_files', array( $this, 'ocdi_import_files' ) );
		add_action( 'pt-ocdi/after_import', array( $this, 'ocdi_after_import_setup' ) );
		add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );

		add_action('admin_menu', array( $this, 'add_theme_page' ), 10 );
		add_action( 'admin_init', array( $this, 'theme_page_redirect' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_custom_admin_style' ) );

		// Register menus.
		add_action( 'init', array( $this, 'register_menus' ) );

		// Register post types.
		add_action( 'init', array( $this, 'register_post_types' ) );

		// Add meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// Save testimonials.
		add_action( 'save_post', array( $this, 'save_gallery_item' ) );

		// Register scripts, styles, and fonts.
		add_action( 'wp_enqueue_scripts',    array( $this, 'register_scripts' ), 99 );
		add_action( 'enqueue_embed_scripts', array( $this, 'register_scripts' ), 99 );

		// Register admin scripts.
		add_action ( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Theme settings.
		add_action('est_project_settings_init', array( $this, 'es_project_theme_settings' ) );
		add_filter('est_project_settings_defaults', array( $this, 'es_project_theme_setting_defaults' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_enqueue' ) );

		// Register sidebars.
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );

		// Amount of news per page.
		add_action('pre_get_posts', array( $this, 'posts_per_page' ) );

		// Change excerpt more.
		add_filter( 'excerpt_more', array( $this, 'excerpt_more' ) );

		// Register image sizes.
		add_action( 'init', array( $this, 'register_image_sizes' ) );

		// Property single template path.
		add_filter( 'es_single_template_path', array( $this, 'property_single_template_path' ), 10, 1 );

		// Property features template path.
		add_filter( 'es_features_list_template_path', array( $this, 'property_features_template_path' ), 10, 1 );

		// Property fields template path.
		add_filter( 'es_single_gallery_fields_path', array( $this, 'property_fields_template_path' ), 10, 1 );

		// Property gallery template path.
		add_filter( 'es_single_gallery_template_path', array( $this, 'property_gallery_template_path' ), 10, 1 );

		// Property tabs template path.
		add_filter( 'es_single_tabs_template_path', array( $this, 'property_tabs_template_path' ), 10, 1 );

		// Property share template path.
		add_filter( 'es_single_share_template_path', array( $this, 'property_share_template_path' ), 10, 1 );

		// Property Listing template path.
		add_filter( 'es_get_my_listings_template_path', array( $this, 'listing_template_path' ), 10, 1 );

		// Remove plugin "to top" button.
		add_filter( 'es_single_top_button_markup', array( $this, 'es_single_top_button_markup' ), 10, 1 );

		// Slideshow template path.
		add_filter( 'es_property_slideshow_shortcode_template_path', array( $this, 'slideshow_template_path' ), 10, 1 );

		// Agent template path.
		add_filter( 'es_agents_list_shortcode_template', array( $this, 'agents_list_shortcode_template' ), 10, 1 );

		// Custom folder for siteoriginwidgets.
		add_filter( 'siteorigin_widgets_widget_folders', array( $this, 'add_widgets_collection' ) );

		// Add pdf link after description.
		add_filter( 'es_the_content', array( $this, 'es_the_content'), 10, 1 );

		// Testimonials form.
		add_filter( 'siteorigin_widgets_form_options_sow-testimonials', array( $this, 'extend_testimonials_form' ), 10, 2);

		// Testimonials template.
		add_filter( 'siteorigin_widgets_template_file_sow-testimonials', array( $this, 'testimonials_template_file' ), 10, 3 );

		// Change search properties template path.
		add_filter( 'template_include', array( $this, 'template_loader' ) );

		// Change excerpt length.
        add_filter( 'excerpt_length', array( $this, 'custom_excerpt_length' ), 10 );

        add_action('wp', array($this, 'reset_estatik_layout_setting'));

        add_filter( 'siteorigin_widgets_active_widgets', array( $this, 'activate_siteorigin_widgets' ), 11 );

	}

    /**
     * Activate theme widgets by default.
     *
     * @param $widgets
     * @return array
     */
    public function activate_siteorigin_widgets( $widgets ) {

	    $widgets['4-properties-grid'] = true;
	    $widgets['6-properties-grid'] = true;
	    $widgets['facts'] = true;
	    $widgets['gallery-grid'] = true;
	    $widgets['line'] = true;
	    $widgets['location'] = true;
	    $widgets['properties-grid'] = true;
	    $widgets['properties-slider'] = true;
	    $widgets['property-teaser'] = true;

	    return $widgets;
    }

	/**
	 * Reset estatik layout setting.
	 */
	public function reset_estatik_layout_setting(){
		global $es_settings;
		$es_settings->single_layout = 'left';
    }

    /**
     * Add some plugins to TGM plugin activation
     */
    public function recommended_plugins(){
        $plugins = array(
            array(
                'name'      => __('SiteOrigin Page Builder', 'es-project'),
                'slug'      => 'siteorigin-panels',
                'required'  => true,
            ),
            array(
                'name'      => __('SiteOrigin Widgets Bundle', 'es-project'),
                'slug'      => 'so-widgets-bundle',
                'required'  => true,
            ),
            array(
                'name'      => __('Estatik Real Estate', 'es-project'),
                'slug'      => 'estatik',
                'external_url'    => 'https://estatik.net/',
                'required'  => false,
            ),
            array(
                'name'      => __('Estatik Mortgage Calculator', 'es-project'),
                'slug'      => 'estatik-mortgage-calculator',
                'required'  => false,
            ),
	        array(
                'name'      => __('Easy Forms for MailChimp', 'es-project'),
                'slug'      => 'yikes-inc-easy-mailchimp-extender',
                'required'  => false,
            ),
            array(
                'name'      => __('One Click Demo Import', 'es-project'),
                'slug'    => 'one-click-demo-import',
                'required'  => false,
            )
        );

        $config = array(
            'id'           => 'tgmpa-project',         // Unique ID for hashing notices for multiple instances of TGMPA.
            'menu'         => 'tgmpa-install-plugins', // Menu slug.
            'parent_slug'  => 'themes.php',            // Parent menu slug.
            'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
            'has_notices'  => true,                    // Show admin notices or not.
            'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
            'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => false,                   // Automatically activate plugins after installation or not.
            'message'      => '',                      // Message to output right before the plugins table.
        );

        tgmpa( $plugins, $config );
    }


	/**
     * Settings for import demo content.
	 * @return array
	 */
	public function ocdi_import_files() {
		return array(
			array(
				'import_file_name'             => 'Demo Import',
				'local_import_file'            => ES_PROJECT_DIR . 'demo-content/demo-content.xml',
				'local_import_widget_file'     => ES_PROJECT_DIR . 'demo-content/widgets.wie',
				'local_import_customizer_file' => ES_PROJECT_DIR . 'demo-content/est-project-export.dat',
				'import_notice'                => __( 'After you import this demo, you will have to setup the revolution slider separately and add MailChimp settings.', 'es-project' ),
			)
		);
	}

	/**
	 * Assign menus and front page.
	 */
	public function ocdi_after_import_setup() {
		// Assign menus to their locations.
		$main_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );
		$footer_menu = get_term_by( 'name', 'Footer Menu', 'nav_menu' );

		set_theme_mod( 'nav_menu_locations', array(
				'main_menu' => $main_menu->term_id,
				'footer_menu' => $footer_menu->term_id
			)
		);

		// Assign front page.
		$front_page_id = get_page_by_title( 'Home V1' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front_page_id->ID );

		// Set active PD widgets.
		$active_widgets = array
		(
			'button'               => 1,
			'google-map'           => 1,
			'image'                => 1,
			'slider'               => 1,
			'post-carousel'        => 1,
			'editor'               => 1,
			'contact'              => 1,
			'headline'             => 1,
			'image-grid'           => 1,
			'layout-slider'        => 1,
			'simple-masonry'       => 1,
			'social-media-buttons' => 1,
			'testimonial'          => 1,
			'video'                => 1,
			'hero'                 => 1,
			'features'             => 1,
			'icon'                 => 1,
			'taxonomy'             => 1,
			'facts'                => 1,
			'line'                 => 1,
			'properties-grid'      => 1,
			'gallery-grid'         => 1,
			'location'             => 1,
			'properties-slider'    => 1,
			'accordion'            => 1,
			'cta'                  => 1,
			'price-table'          => 1,
			'property-teaser'       => 1,
			'6-properties-grid'    => 1,
			'4-properties-grid'    => 1
		);
		$active_widgets_default = get_option( 'siteorigin_widgets_active' );
		if ($active_widgets_default) {
			update_option( 'siteorigin_widgets_active', $active_widgets );
        }
        else{
	        add_option( 'siteorigin_widgets_active', $active_widgets );
        }

	}

	/**
	 * Initialize entity classes using specific conditions.
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		// TGM class.
		require_once( 'lib/class-tgm-plugin-activation.php');

		// Theme Settings.
		require_once( 'settings/settings.php');
	}

	/**
	 * The theme setup function.
	 *
	 * @access public
	 * @return void
	 */
	public function theme_setup() {

		add_theme_support( 'post-thumbnails' );

		add_theme_support( 'menus' );

		add_theme_support( 'title-tag' );

	}

	/**
	 * Load text domain.
	 *
	 * @access public
	 * @return void
	 */
	public function load_text_domain() {
		load_theme_textdomain( 'es-project', get_template_directory() . '/languages' );
	}

	/**
	 * Add theme settinga page.
	 */
	public function add_theme_page() {
		add_theme_page( __( 'Theme Settings', 'es-project' ), __( 'Theme Settings', 'es-project' ), 'edit_theme_options', 'theme-settings', array( $this, 'theme_settings_page' ) );
	}

	/**
	 * Theme settings page callback.
	 */
	public function theme_settings_page() {
		_e( 'Our theme settings now take advantage of the WordPress customizer. Navigate to Appearance > Customize > Theme Settings to access theme settings.', 'es-project' );
	}

	/**
	 * Theme page redirect.
	 */
	public function theme_page_redirect(){
		global $pagenow;
		if( $pagenow == 'themes.php' && isset( $_GET['page'] ) && $_GET['page'] == 'theme-settings' ){
			$query['autofocus[panel]'] = 'theme_settings';
			$panel_link = add_query_arg( $query, admin_url( 'customize.php' ) );
			wp_redirect( $panel_link );
		}
	}

	/**
	 * Load styles to admin panel.
	 */
	public function load_custom_admin_style(){
		wp_register_style( 'custom_admin_css', ES_PROJECT_CUSTOM_STYLES_URL . 'admin-style.css' );
		wp_enqueue_style( 'custom_admin_css' );
	}

	/**
	 * Registers nav menus.
	 *
	 * @access public
	 * @return void
	 */
	public function register_menus() {
		register_nav_menus( array(
			'footer_menu'    => __( 'Footer Menu', 'es-project' ),
			'main_menu' => __( 'Main Menu', 'es-project' ),
		) );
	}

	/**
	 * Registers post types.
	 *
	 * @access public
	 * @return void
	 */
	public function register_post_types() {
		// Offers post type.
		$labels = array(
			'name'               => _x( 'Offers', 'post type general name', "es-project" ),
			'singular_name'      => _x( 'Offer', 'post type singular name', "es-project" ),
			'add_new'            => _x( 'Add New', 'Offer', "es-project" ),
			'add_new_item'       => __( 'Add New Offer', "es-project" ),
			'edit_item'          => __( 'Edit Offer', "es-project" ),
			'new_item'           => __( 'New Offer', "es-project" ),
			'all_items'          => __( 'All Offers', "es-project" ),
			'view_item'          => __( 'View Offers', "es-project" ),
			'search_items'       => __( 'Search Offer', "es-project" ),
			'not_found'          => __( 'No Offers found', "es-project" ),
			'not_found_in_trash' => __( 'No Offers found in the Trash', "es-project" ),
			'parent_item_colon'  => '',
			'menu_name'          => 'Offers'
		);
		$args = array(
			'labels'        => $labels,
			'description'   => 'Offers',
			'public'        => true,
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'has_archive'   => true,
			'taxonomies' => array(),
			'show_in_nav_menus' => true
		);
		register_post_type( 'offers', $args );

		// Testimonials post type.
		$labels = array(
			'name'               => _x( 'Gallery items', 'post type general name', "es-project" ),
			'singular_name'      => _x( 'Gallery item', 'post type singular name', "es-project" ),
			'add_new'            => _x( 'Add New', 'Gallery item', "es-project" ),
			'add_new_item'       => __( 'Add New Gallery item', "es-project" ),
			'edit_item'          => __( 'Edit Gallery item', "es-project" ),
			'new_item'           => __( 'New Gallery item', "es-project" ),
			'all_items'          => __( 'All Gallery items', "es-project" ),
			'view_item'          => __( 'View Gallery item', "es-project" ),
			'search_items'       => __( 'Search Gallery items', "es-project" ),
			'not_found'          => __( 'No Gallery items found', "es-project" ),
			'not_found_in_trash' => __( 'No Gallery items found in the Trash', "es-project" ),
			'parent_item_colon'  => '',
			'menu_name'          => 'Gallery items'
		);
		$args = array(
			'labels'        => $labels,
			'description'   => 'Gallery items',
			'public'        => true,
			'menu_position' => 6,
			'supports'      => array( 'title' ),
			'has_archive'   => false,
		);
		register_post_type( 'gallery_item', $args );
	}

	/**
	 * Add metaboxes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box('gallery_info', __( 'Gallery Item Info', 'es-project' ), array( $this, 'gallery_item_fields' ), 'gallery_item');
	}

	/**
	 * Callback function for gallery item fields.
	 *
	 * @access public
	 * @param WP_Post $post Post object.
	 * @return void
	 */
	public function gallery_item_fields( $post ) {
		wp_enqueue_media();
		wp_nonce_field( 'es_project_gallery_item_box_nonce', 'es_project_gallery_item_box_nonce' );

		$type  = get_post_meta( $post->ID, 'gallery_type', TRUE );
		$text  = get_post_meta( $post->ID, 'gallery_text', TRUE );
		$file_id = get_post_meta( $post->ID, 'gallery_file', TRUE );
		?>
        <div>
            <div class="es-gallery-field">
                <label class="es-field__label"><?php _e( 'Item type', 'es-project' ); ?></label>
                <div class="es-field__content">
                    <select name="gallery_item[type]">
                        <option value="image" <?php selected( $type, 'image' );?> ><?php _e( 'Image', 'es-project' );?></option>
                        <option value="video" <?php selected( $type, 'video' );?>><?php _e( 'Video', 'es-project' );?></option>
                    </select>
                </div>
            </div>
            <div class="es-gallery-field">
                <label class="es-field__label"><?php _e( 'Item text', 'es-project' ); ?></label>
                <div class="es-field__content">
                     <textarea rows="1" cols="40" name="gallery_item[text]"
                          id="excerpt"><?php echo $text; ?></textarea>
                </div>
            </div>
            <div class="es-gallery-field">
                <p class="es-field__label"><?php _e( 'Gallery File', 'es-property' ); ?></p>
                <div class="es-field__content">
                    <div class='image-preview-wrapper'>
		                <?php
		                if( wp_attachment_is_image( $file_id ) ){
			                if( wp_get_attachment_image( $file_id )){
				                echo wp_get_attachment_image( $file_id, 'thumbnail' );
			                }
		                }
		                else{
			                echo '<a href="' . wp_get_attachment_url( $file_id ) . '">' . basename( get_attached_file( $file_id ) ) . '</a>';
		                }
		                ?>
                    </div>
                    <div>
                        <input id="upload_image_button" type="button" class="button"
                           value="<?php _e( 'Upload File', 'es-project' ); ?>"/>
                        <input type='hidden' name='gallery_item[file]'
                           id='image_attachment_id' value='<?php echo $file_id;?>'>
                    </div>
                </div>
            </div>
        </div>

<?php
	}

	/**
	 * Save gallery item callback.
	 *
	 * @access public
	 * @param int $post_id ID.
	 * @return void
	 */
	public function save_gallery_item( $post_id) {
		if ( ! isset( $_POST['es_project_gallery_item_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['es_project_gallery_item_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'es_project_gallery_item_box_nonce' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		$data = $_POST['gallery_item'];
		foreach ( $data as $key => $field) {
			update_post_meta( $post_id, 'gallery_' . $key, $field );
		}

	}

	/**
	 * Registers scripts/styles.
	 *
	 * @access public
	 * @return void
	 */
	public function register_scripts() {
		global $es_project_options, $es_settings;
		// Register scripts.

		wp_register_script( 'es-load-pagination', ES_PROJECT_CUSTOM_SCRIPTS_URL . 'jquery.loadpagination.js', array(
			'jquery'
		), null, true );
		wp_enqueue_script( 'es-load-pagination' );

		//Slick slider.
		wp_register_script( 'es-project-slick', ES_PROJECT_VENDOR_SCRIPTS_URL . 'slick/slick/slick.min.js', array(
			'jquery'
		), null, true );
		wp_enqueue_script( 'es-project-slick' );

		// Mobile menu.
		wp_register_script( 'es-project-sidr', ES_PROJECT_VENDOR_SCRIPTS_URL . 'sidr/dist/jquery.sidr.min.js', array(
			'jquery'
		), null, true );
		wp_enqueue_script( 'es-project-sidr' );

		wp_register_script( 'es-project', ES_PROJECT_CUSTOM_SCRIPTS_URL . 'theme.js', array(
			'jquery', 'es-load-pagination', 'es-project-slick', 'es-project-sidr'
		), null, true );
		wp_enqueue_script( 'es-project' );

		wp_localize_script( 'es-project', 'Es_Project', static::register_js_variables() );

		// Register styles.
		wp_enqueue_style( 'es-project-reset', ES_PROJECT_CUSTOM_STYLES_URL . 'reset.css' );
		wp_enqueue_style( 'es-project-slick', ES_PROJECT_VENDOR_SCRIPTS_URL . 'slick/slick/slick.css' );

		wp_enqueue_style( 'es-project-style', ES_PROJECT_CUSTOM_STYLES_URL . 'style.css' );
		wp_enqueue_style( 'es-project-awesome', ES_PROJECT_FONTS_URL . 'font-awesome/css/font-awesome.min.css' );

	}

	/**
	 * Register scripts for admin panel.
	 */
    public function admin_enqueue_scripts() {
	    wp_register_script( 'es-project-admin', ES_PROJECT_CUSTOM_SCRIPTS_URL . 'admin.js', array(
		    'jquery' ), null, true );
	    wp_enqueue_script( 'es-project-admin' );

    }

	/**
	 * Register global javascript variables.
	 *
	 * @access public
	 * @return array
	 */
	public static function register_js_variables() {
		return apply_filters( 'es_project_global_js_variables', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'theme_url' => ES_PROJECT_URL
		) );
	}

	/**
	 * Register theme sidebars.
	 *
	 * @access public
	 * @return void
	 */
	public function register_sidebars() {
		register_sidebar( array(
			'name'          => __( 'Post Sidebar Right', 'es-project' ),
			'id'            => 'posts-sidebar-right',
			'description'   => __( 'Add widgets at the right side on the posts and archives pages.', 'es-project' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );


        register_sidebar( array(
            'name'          => __( 'Property Sidebar Right', 'es-project' ),
            'id'            => 'property-sidebar-right',
            'description'   => __( 'Add widgets at the right side on the property pages.', 'es-project' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ) );

		register_sidebar( array(
			'name'          => __( 'First Footer Sidebar', 'es-project' ),
			'id'            => 'first-footer-sidebar',
			'description'   => __( 'Add widgets to first footer column.', 'es-project' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => __( 'Second Footer Sidebar', 'es-project' ),
			'id'            => 'second-footer-sidebar',
			'description'   => __( 'Add widgets to second footer column.', 'es-project' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => __( 'Third Footer Sidebar', 'es-project' ),
			'id'            => 'third-footer-sidebar',
			'description'   => __( 'Add widgets to third footer column.', 'es-project' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );

		register_sidebar( array(
			'name'          => __( 'Fourth Footer Sidebar', 'es-project' ),
			'id'            => 'fourth-footer-sidebar',
			'description'   => __( 'Add widgets to fourth footer column.', 'es-project' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );


	}

	/**
	 * Return estatik logo markup.
	 *
	 * @return string;
	 */
	public static function get_logo() {
		ob_start();

		echo "<div class='es-logo clearfix'><img src='" . ES_PROJECT_ADMIN_IMAGES_URL . 'logo.png' . "'><br>
            <span class='es-version'>" . __( 'Ver', 'es-project' ) . ". " . self::getVersion() .  "</span></div>";

		return ob_get_clean();
	}

	/**
	 * Change archive and categories page titles.
	 *
	 * @access public
	 * @return title
	 */
	public function get_the_archive_title( $title ) {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( 'Tag: ', false );
		} elseif ( is_author() ) {
			$title = get_the_author();
		} elseif ( is_year() ) {
			$title = get_the_date( _x( 'Y', 'yearly archives date format' ) );
		} elseif ( is_month() ) {
			$title = get_the_date( _x( 'F Y', 'monthly archives date format' ) );
		} elseif ( is_day() ) {
			$title = get_the_date( _x( 'F j, Y', 'daily archives date format' ) );
		} elseif ( is_tax( 'post_format' ) ) {
			if ( is_tax( 'post_format', 'post-format-aside' ) ) {
				$title = _x( 'Asides', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
				$title = _x( 'Galleries', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
				$title = _x( 'Images', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
				$title = _x( 'Videos', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
				$title = _x( 'Quotes', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
				$title = _x( 'Links', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
				$title = _x( 'Statuses', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
				$title = _x( 'Audio', 'post format archive title' );
			} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
				$title = _x( 'Chats', 'post format archive title' );
			}
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		} else {
			$title = __( 'Archives' );
		}
		return $title;
	}

	/**
	 * Change amount of news per page.
	 *
	 * @access public
	 * @return string $title
	 */
	public function posts_per_page( $query ) {
		if ( !is_admin() && $query->is_main_query() && is_post_type_archive( 'offers' ) ) {
			$amount = est_project_setting( 'general_offers_amount' );
			$query->set( 'posts_per_page', $amount );
		}
	}

	/**
	 * Registers image sizes.
	 *
	 * @access public
	 * @return void
	 */
	public function register_image_sizes() {

		// Archives post image sizes.
		add_image_size( 'posts-archive', 480, 480, true );
		add_image_size( 'single-posts-thm', 885, 450, true );
		add_image_size( 'offer-thumbnail', 495, 400, true );
		add_image_size( 'single-offer-thm', 520, 300, false );
		add_image_size( 'property-archive-thm', 380, 315, true );
		add_image_size( 'thumbnail_140x140', 140, 140, true );
		add_image_size( 'property-single-gallery', 780, 500, true );
		add_image_size( 'property-loop', 280, 290, true );
		add_image_size( 'agent_thm', 550, 550, true );
		add_image_size( 'gallery_image', 294, 294, true );
		add_image_size( 'map_icon', 26, 35, true );
		add_image_size( 'property_teaser', 580, 480, true );
		add_image_size( 'top_slider', 1580, 670, true );
		add_image_size( 'thm_375', 375, 440, false );
		add_image_size( 'thm_380x330', 380, 330, true );
		add_image_size( 'thm_780x330', 780, 330, true );
		add_image_size( 'thm_390x680', 390, 680, true );
		add_image_size( 'thm_768', 768, 'auto', false );
	}

	/**
	 * Change excerpt more.
	 *
	 * @access public
	 * @return string
	 */
	public function excerpt_more( $more ) {
		return '...';
	}

	/**
	 * Change search properties template path.
	 *
	 * @access public
	 * @return string
	 */
	public function template_loader( $template ) {
		$find = array();

		if ( function_exists( 'es_get_property' ) ) {
			$property = es_get_property( null );
			$type = $property::get_post_type_name();
			// Template for archive properties page.
			if( is_post_type_archive( $type ) && ! is_search() ) {
				$file = 'archive-' . $type . '.php';

				// Template for property taxonomies page.
			} else if ( is_tax( get_object_taxonomies( $type ) ) ) {
				$file = 'archive-' . $type . '.php';

				// If search page.
			} else if ( is_search() && ! is_admin() && isset( $_GET['post_type'] ) && $_GET['post_type'] == $type ) {
				$file = 'search-properties.php';
			}

			if ( ! empty( $file ) ) {
				$find[] = ES_PROJECT_TEMPLATES_DIR . $file;

				$template = locate_template( array_unique( $find ) );
				if ( ! $template ) {
					$template = ES_PROJECT_TEMPLATES_DIR . $file;
				}
			}
		}

		return $template;
	}

	/**
	 * Change Property Single template path.
	 *
	 * @access public
	 * @return string
	 */
	public function property_single_template_path( $path ) {
		$path = ES_PROJECT_TEMPLATES_DIR . 'content-single.php';
		return $path;
	}

	/**
	 * Change Property Features template path.
	 *
	 * @access public
	 * @return string
	 */
	public function property_features_template_path( $path ) {
		$path = ES_PROJECT_TEMPLATES_DIR . 'property/features-list.php';
		return $path;
	}

	/**
	 * Change Property Fields template path.
	 *
	 * @access public
	 * @return string
	 */
	public function property_fields_template_path( $path ) {
		$path = ES_PROJECT_TEMPLATES_DIR . 'property/fields.php';
		return $path;
	}

	/**
	 * Change Property Gallery template path.
	 *
	 * @access public
	 * @return string
	 */
	public function property_gallery_template_path( $path ) {
		$path = ES_PROJECT_TEMPLATES_DIR . 'property/gallery.php';
		return $path;
	}

	/**
	 * Change Property tabs template path.
	 *
	 * @access public
	 * @return string
	 */
	public function property_tabs_template_path( $path ) {
		$path = ES_PROJECT_TEMPLATES_DIR . 'property/tabs.php';
		return $path;
	}

	/**
	 * Change Property share template path.
	 *
	 * @access public
	 * @return string
	 */
	public function property_share_template_path( $path ) {
		$path = ES_PROJECT_TEMPLATES_DIR . 'property/share.php';
		return $path;
	}

	/**
	 * Change listing template path.
	 *
	 * @access public
	 * @return string
	 */
	public function listing_template_path( $path ) {
		$path = ES_PROJECT_TEMPLATES_DIR . 'my-listing.php';
		return $path;
	}

    /**
     * Setup theme settings.
     */
	public function es_project_theme_settings(){
	    $sliders = array();
		$filters = array();
        $settings = EstProject_Settings::single();

        $settings->add_section( 'logo', __( 'Logo', 'es-project' ) );
        $settings->add_section( 'color', __( 'Color', 'es-project' ) );
        $settings->add_section( 'social', __( 'Social', 'es-project' ) );
        $settings->add_section( 'general', __( 'General', 'es-project' ) );
        $settings->add_section( 'contact', __( 'Contact', 'es-project' ) );

        /**
         * Logo Settings
         */

        $settings->add_field('logo', 'logo', 'media', __('Logo Image', 'es-project'), array(
            'choose' => __('Choose Image', 'es-project'),
            'update' => __('Set Logo', 'es-project'),
            'description' => __('Your own custom logo.', 'es-project')
        ) );

        $settings->add_field('logo', 'favicon', 'media', __('Favicon', 'es-project'), array(
            'choose' => __('Choose Image', 'es-project'),
            'update' => __('Set Favicon', 'es-project'),
            'description' => __('Your own custom favicon.', 'es-project')
        ) );

        /**
         * Color Settings
         */

        $settings->add_field('color', 'theme_color', 'color', __('Main Theme Color', 'es-project'), array(
            'description' => __('Choose the main theme color.', 'es-project')
        ) );

		$settings->add_field( 'color', 'header_theme', 'radio', __( 'Header Color Theme', 'es-project' ), array(
			'options'     => array(
				'header_transparent' => __( 'Transparent', 'es-project' ),
				'header_white'       => __( 'White', 'es-project' )
			),
			'description' => __( 'Choose the header theme.', 'es-project' )
		) );

		$settings->add_field( 'color', 'footer_theme', 'radio', __( 'Footer Color Theme', 'es-project' ), array(
			'options'     => array(
				'dark'  => __( 'Dark', 'es-project' ),
				'light' => __( 'Light', 'es-project' )
			),
			'description' => __( 'Choose the footer color.', 'es-project' )
		) );

        /**
         * General Settings
         */

		if ( class_exists( 'RevSlider' ) ) {
			$rev_slider = new RevSlider();
			$rev_sliders = $rev_slider->getArrSliders();
			if ( !empty( $rev_sliders ) ) {
				foreach ( $rev_sliders as $slider ) {
                    $sliders[$slider->getAlias()] = $slider->getTitle();
                }
			}
		}

		$filters[] = __( 'Select parameters for filtering', 'es-project' );
		$filters = $this->get_filter_fields_data();

        $settings->add_field( 'general', 'top_slider', 'checkbox', __( 'Show Top Slider', 'es-project' ), array(
            'description' => __( "Add or remove a slider at the page top.", 'es-project' ),
        ) );

		if ( !empty( $sliders ) ) {
			$settings->add_field( 'general', 'slider', 'radio', __( 'Select slider type', 'es-project' ), array(
				'options'     => array(
					'theme_slider' => __( 'Theme Slider', 'es-project' ),
					'rev_slider'       => __( 'Revolution Slider', 'es-project' )
				),
				'description' => __( 'Choose the header theme.', 'es-project' )
			) );
		}

		if ( !empty( $filters ) ) {
			$settings->add_field( 'general', 'slider_filters', 'select', __( 'Select Properties for Top Slider', 'es-project' ), array(
				'options' => $filters,
				'description' => __( "Filters of properties for top slider.", 'es-project' ),
			) );
		}

		$settings->add_field( 'general', 'slider_properties_ids', 'text', __( 'Listings IDs', 'es-project' ), array(
			'description' => __( "The IDs of the properties.", 'es-project' ),
		) );

		$settings->add_field( 'general', 'slider_limit', 'text', __( 'Limit', 'es-project' ), array(
			'description' => __( "The amount of the slides.", 'es-project' ),
		) );

		if ( !empty( $sliders ) ) {
			$settings->add_field( 'general', 'rev_slider_id', 'select', __( 'Select Revolution Slider', 'es-project' ), array(
                'options' => $sliders,
				'description' => __( "Select revolution slider.", 'es-project' ),
			) );
        }

		$settings->add_field('general', '404_image', 'media', __('404 Background Image', 'es-project'), array(
			'choose' => __('Choose Image', 'es-project'),
			'update' => __('Set Background', 'es-project'),
			'description' => __('Your own custom 404 background.', 'es-project')
		) );

		$settings->add_field( 'general', 'offers_amount', 'text', __( 'Offers amount per page', 'es-project' ), array(
			'description' => __( "Amount of offers per page.", 'es-project' ),
		) );

		$settings->add_field( 'general', 'copyright', 'text', __( 'Copyright', 'es-project' ), array(
			'description' => __( "Text displayed in your footer.", 'es-project' ),
			'sanitize_callback' => 'wp_kses_post'
		) );

		/**
		 * Social Settings
		 */

		$settings->add_field( 'social', 'twitter', 'text', __( 'Twitter Link', 'es-project' ), array(
			'description' => __( "Link to your twitter account", 'es-project' ),
			'sanitize_callback' => 'wp_kses_post'
		) );

		$settings->add_field( 'social', 'facebook', 'text', __( 'Facebook Link', 'es-project' ), array(
			'description' => __( "Link to your facebook account", 'es-project' ),
			'sanitize_callback' => 'wp_kses_post'
		) );

		$settings->add_field( 'social', 'google_plus', 'text', __( 'Google+ Link', 'es-project' ), array(
			'description' => __( "Link to your google+ account", 'es-project' ),
			'sanitize_callback' => 'wp_kses_post'
		) );

		$settings->add_field( 'social', 'linkedin', 'text', __( 'Linkedin Link', 'es-project' ), array(
			'description' => __( "Link to your linkedin account", 'es-project' ),
			'sanitize_callback' => 'wp_kses_post'
		) );

        /**
         * Contact Settings
         */

        $settings->add_field( 'contact', 'phone', 'text', __( 'Contact Phone', 'es-project' ), array(
            'description' => __( "Your contact phone number.", 'es-project' ),
            'sanitize_callback' => 'wp_kses_post'
        ) );

        $settings->add_field( 'contact', 'email', 'text', __( 'Contact Email', 'es-project' ), array(
            'description' => __( "Your contact email.", 'es-project' ),
            'sanitize_callback' => 'wp_kses_post'
        ) );

    }

     /**
     * Setup theme default settings.
     *
     * @param $defaults
     * @return mixed
     */
   public  function es_project_theme_setting_defaults( $defaults ){
        $defaults['logo_logo'] = false;
        $defaults['logo_favicon'] = false;
        $defaults['general_top_slider'] = true;
        $defaults['general_slider'] = 'theme_slider';
        $defaults['general_slider_limit'] = 5;
        $defaults['general_copyright'] = 'Copyright ' . date('Y');
        $defaults['general_offers_amount'] = 3;
        $defaults['color_footer_theme'] = 'dark';
        $defaults['color_header_theme'] = 'header_white';

        return $defaults;
   }

	/**
	 * Enqueue script for custom customize control.
	 */
	public function customize_enqueue() {
		wp_enqueue_script( 'customize-script', ES_PROJECT_CUSTOM_SCRIPTS_URL . 'customize.js', array( 'jquery', 'customize-controls' ), false, true );
	}


	/**
	 * To top button template on property single page.
	 * @param $result
	 */
	public function es_single_top_button_markup( $result ){
   	    $result = '';
	    echo $result;
    }

	/**
	 * Change slideshow template path.
	 *
	 * @access public
	 * @return string
	 */
	public function slideshow_template_path( $path ) {
		$path = ES_PROJECT_TEMPLATES_DIR . 'slideshow.php';
		return $path;
	}

	/**
	 * Change Property Single template path.
	 *
	 * @access public
	 * @return string
	 */
	public function agents_list_shortcode_template( $path ) {
		$path = ES_PROJECT_TEMPLATES_DIR . 'agent.php';
		return $path;
	}

	/**
	 * Siteorigin widgets folder.
	 * @param $folders
	 *
	 * @return array
	 */
	public function add_widgets_collection( $folders ){
		$folders[] = ES_PROJECT_INC_DIR . 'widgets/';
		return $folders;
	}

	/**
	 * @param $content
	 *
	 * @return string
	 */
	public function es_the_content( $content ) {
		global $es_settings;

		 if ( $es_settings->use_pdf ){
			 $content .= '<a href="' . add_query_arg( 'es-pdf', get_the_ID(), get_the_permalink() ) . '" target="_blank">
							<i class="fa fa-file-pdf-o" aria-hidden="true"></i>' . __( 'PDF Brochure', 'es-project' ) . '</a>';
		 }
		 return $content;
	}

	/**
	 * Change testimonials widget template.
	 * @param $filename
	 * @param $instance
	 * @param $widget
	 *
	 * @return string
	 */
	public function testimonials_template_file( $filename, $instance, $widget ){
		$filename = get_stylesheet_directory() . '/templates/testimonials.php';
		return $filename;
	}

	/**
	 * change testimonials widget form.
	 * @param $form_options
	 * @param $widget
	 *
	 * @return mixed
	 */
	public function extend_testimonials_form( $form_options, $widget ){
		// Lets add a new theme option
		$form_options['settings']['fields']['per_line']['default'] = 1;
		$form_options['settings']['fields']['per_line']['max'] = 1;
		$form_options['settings']['fields']['responsive']['fields']['tablet']['fields']['per_line']['default'] = 1;
		$form_options['settings']['fields']['responsive']['fields']['tablet']['fields']['per_line']['max'] = 1;
		unset($form_options['settings']['fields']['responsive']['fields']['tablet']['fields']['image_size']);
		$form_options['settings']['fields']['responsive']['fields']['mobile']['fields']['per_line']['default'] = 1;
		$form_options['settings']['fields']['responsive']['fields']['mobile']['fields']['per_line']['max'] = 1;
		unset($form_options['settings']['fields']['responsive']['fields']['mobile']['fields']['image_size']);
		unset($form_options['testimonials']['fields']['image']);
		unset($form_options['testimonials']['fields']['link_image']);
		unset($form_options['design']['fields']['user_position']);
		unset($form_options['design']['fields']['image']);
		unset($form_options['design']['fields']['colors']['fields']['testimonial_background']);
		unset($form_options['design']['fields']['colors']['fields']['text_background']);
		$form_options['design']['fields']['layout']['options'] = array(
			'text_above' => 'Text above user',
			'text_below' => 'Text below user'
		);
		$form_options['design']['fields']['layout']['default'] = 'text_above';

		return $form_options;
	}

    /**
     * Change excerpt length.
     * @param $length
     * @return int
     */
    public function custom_excerpt_length( $length ) {
        return 47;
    }

	/**
     * Return filters for properties widgets.
	 * @return array
	 */
	public function get_filter_fields_data() {
		/** @var Es_Settings_Container $es_settings */
		global $es_settings;

		$data = array();
		$filters_data = array();

		if ( ! empty($es_settings) ) {
			$taxonomies = $es_settings::get_setting_values( 'taxonomies' );

			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $name => $taxonomy ) {
					if ( taxonomy_exists( $name ) ) {
						$taxonomy = get_taxonomy( $name );
						$data[ $taxonomy->label ] = get_terms( array( 'taxonomy' => $taxonomy->name, 'hide_empty' => false ) );
					}
				}
			}
		}

		foreach ( $data as $group => $items ){
			if ( empty( $items ) ) continue;
			foreach ( $items as $value => $item ){
				if ( $item instanceof WP_Term) {
					$filters_data[$item->term_id] = $item->name;
				}
				else{
					$filters_data[$value] = $item;
				}
			}
		}

		return $filters_data;
	}

	/**
	 *
	 */
	public static function top_slider() {
		if ( est_project_setting( 'general_slider' ) == 'rev_slider' ) {
			$rev_slider = est_project_setting( 'general_rev_slider_id' );
			if ( ! empty( $rev_slider ) && shortcode_exists( 'rev_slider' ) ) {
				echo '<div class="top-rev-slider">' . do_shortcode( '[rev_slider ' . $rev_slider . ']' ) . '</div>';
			}
		} else {
			echo self::theme_slider( est_project_setting( 'general_slider_filters' ), est_project_setting( 'general_slider_properties_ids' ),   est_project_setting( 'general_slider_limit' ));
		}
	}

	/**
     * Generate theme slider.
	 * @param array $filters
	 * @param array $ids
	 * @param $limit
	 *
	 * @return string
	 */
	public static function theme_slider( $filters = array(), $ids = array(), $limit = 5 ) {
		$properties_query = null;
		$data_filter = array();
		$query_args = array(
			'post_type'      => 'properties',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => 'post_date',
			'order'          => 'DESC'
		);

		if ( ! empty( $filters ) ) {
			if ( is_array( $filters ) ) {
				foreach ( $filters as $tid ) {
					$term = get_term( $tid );
					if ( $term && strlen( $term->taxonomy ) > 3 ) {
						$data_filter[$term->taxonomy][] = $term->term_id;
					}
				}
			}
			else{
				$term = get_term( $filters );
				if ( $term && strlen( $term->taxonomy ) > 3 ) {
					$data_filter[$term->taxonomy][] = $term->term_id;
				}
			}
		}

		foreach ( $data_filter as $key => $value ) {
			$tax_name = $key;
			if ( taxonomy_exists( $tax_name ) ) {
				if ( ! empty( $value ) ) {
					if ( $tax_name == 'es_labels' ) {
						$value = $value;

						if ( $value ) {
							foreach ( $value as $name ) {
								$term = get_term_by( 'id', $name, $tax_name );
								$query_args['meta_query'][] = array(
									'compare' => '=',
									'key'     => 'es_property_' . $term->slug,
									'value'   => 1
								);
							}
						}
					} else {
						$query_args['tax_query'][] = array(
							'taxonomy' => $tax_name,
							'field'    => 'id',
							'terms'    => $value
						);
					}
				}
			}
		}

		if ( ! empty( $ids ) ) {
			if ( is_array( $ids ) ) {
				$query_args['post__in'] = $ids;
			} else {
				$query_args['post__in'] = array_map( 'trim', explode( ',', $ids ) );
			}
		}

		$properties_query = new WP_Query( $query_args );

		ob_start();

		if ( $properties_query ) {
				$template = ES_PROJECT_TEMPLATES_DIR . 'top-theme-slider.php';
				include( $template );
		}

		return ob_get_clean();
    }

	/**
     * check if mobie browser.
	 * @return int
	 */
    public static function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
}
