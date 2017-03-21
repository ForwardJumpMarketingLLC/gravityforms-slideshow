<?php
/**
 * Adds options page
 */
namespace ForwardJump\GravityFormsSlideshow\OptionsPage;

/**
 * CMB2 Theme Options
 *
 * @version 0.1.0
 */
class Admin_Options {

	/**
	 * Prefix
	 *
	 * @var string
	 */
	private $prefix = 'gf_slideshow';

	/**
	 * Option key, and option page slug
	 *
	 * @var string
	 */
	private $key = 'gf_slideshow_options';
	/**
	 * Options page metabox id
	 *
	 * @var string
	 */
	private $metabox_id = 'gf_slideshow_option_metabox';
	/**
	 * Options Page title
	 *
	 * @var string
	 */
	protected $title = 'Gravity Forms Slideshow';

	/**
	 * Options Page title
	 *
	 * @var string
	 */
	protected $description = '<p>This plugin allows users to contribute to an image slideshow by uploading pictures using a Gravity Forms form.  User uploaded pictures are added to the Media Library and can be used just like any other media file.</p><p>The first step is to create a form that has a "File Upload" field.  Next, complete the form below in order to tell the plugin which forms and fields to use for the file uploads.  Then copy the slideshow shortcode and paste it wherever you want the user-based slideshow to appear.</p><p>Image uploads will not be visible in the slideshow until they are marked as approved.  To approve images, go to the image/attachment edit screen and select "Yes" in the "Approve user upload" metabox.  Note, if you don\'t see the metabox, click on "Edit more details" link to bring you to the image edit screen.  <p>You can also include image edit links in the Gravity Forms notification by adding the tag <code>{image_edit_links}</code> to the form notification message.</p>';

	/**
	 * Options Page hook
	 *
	 * @var string
	 */
	protected $options_page = '';
	/**
	 * Holds an instance of the object
	 *
	 * @var Admin_Options
	 **/
	private static $instance = null;

	/**
	 * Constructor
	 *
	 * @since 0.1.0
	 */
	private function __construct() {
		// Set our title
		$this->menu_title = __( 'GF Slideshow', 'gf-slideshow' );
		$this->title      = __( 'Gravity Forms Slideshow', 'gf-slideshow' );
	}

	/**
	 * Returns the running object
	 *
	 * @return Admin_Options
	 **/
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->hooks();
		}

		return self::$instance;
	}

	/**
	 * Initiate our hooks
	 *
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
		add_action( 'current_screen', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Register our setting to WP
	 *
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 *
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$this->options_page = add_options_page( $this->title, $this->menu_title, 'manage_options', $this->prefix, array(
			$this,
			'admin_page_display'
		) );
		// Include CMB CSS in the head to avoid FOUC
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Enqueues admin assets only on the necessary page
	 */
	public function enqueue_admin_assets() {

		if ( "settings_page_{$this->prefix}" !== get_current_screen()->id ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueues styles
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'gf-slideshow-admin-styles', FJ_GRAVITYFORMS_SLIDESHOW_URL . 'assets/css/gf-slideshow-admin-styles.min.css', array(), FJ_GRAVITYFORMS_SLIDESHOW_VERSION );
	}

	/**
	 * Enqueues scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'gf-slideshow-admin-scripts', FJ_GRAVITYFORMS_SLIDESHOW_URL . 'assets/js/gf-slideshow-admin-scripts.min.js', array( 'jquery' ), FJ_GRAVITYFORMS_SLIDESHOW_VERSION, true );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 *
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
        <div class="wrap cmb2-options-page <?php echo $this->key; ?>">
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
            <p><?php echo wpautop( $this->description ); ?></p>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
        </div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 *
	 * @since  0.1.0
	 */
	function add_options_page_metabox() {
		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		/**
		 * Repeatable Field Groups
		 */
		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		$group_id = $cmb->add_field( array(
			'id'          => '_gf_image_upload_form',
			'type'        => 'group',
			'description' => '',
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => __( 'Form {#}', 'gf-slideshow' ),
				'add_button'    => __( 'Add Another Form', 'gf-slideshow' ),
				'remove_button' => __( 'Remove Form', 'gf-slideshow' ),
				'sortable'      => false
			)
		) );

		$cmb->add_group_field( $group_id, array(
			'id'      => 'form_id',
			'name'    => __( 'Choose a form', 'gf-slideshow' ),
			'type'    => __( 'select', 'gf-slideshow' ),
			'classes' => __( 'form-id-select', 'gf-slideshow' ),
//			'desc'    => 'Enter the ID of the Gravity Form.',
			'default' => 'null',
			'options' => $this->get_forms()
		) );

		$cmb->add_group_field( $group_id, array(
			'id'         => 'image_fields',
			'name'       => __( 'Choose the file upload field', 'gf-slideshow' ),
//			'desc'       => 'Choose the "File Upload" field.',
			'type'       => 'select',
			'classes'    => 'image-id-select',
			'options_cb' => '\ForwardJump\GravityFormsSlideshow\Admin\get_form_fields'
		) );

		$cmb->add_group_field( $group_id, array(
			'id'         => 'caption_field',
			'name'       => __( 'Choose the caption field', 'gf-slideshow' ),
//			'desc'       => 'Choose the "Caption" field.',
			'type'       => 'select',
			'classes'    => 'caption-id-select',
			'options_cb' => '\ForwardJump\GravityFormsSlideshow\Admin\get_form_fields'
		) );

		$cmb->add_group_field( $group_id, array(
			'id'         => 'slideshow_shortcode',
			'name'       => __( 'Slideshow shortcode', 'gf-slideshow' ),
			'type'       => 'text',
			'classes'    => 'slideshow-shortcode',
			'default'    => '[gf-slideshow gform_id="{form_id}"]',
			'attributes' => array(
				'readonly' => 'readonly',
			),
		) );
	}

	/**
	 * Register settings notices for display
	 *
	 * @since  0.1.0
	 *
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 *
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->key || empty( $updated ) ) {
			return;
		}
		add_settings_error( $this->key . '-notices', '', __( 'Settings updated.', 'gf-slideshow' ), 'updated' );
		settings_errors( $this->key . '-notices' );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 *
	 * @since  0.1.0
	 *
	 * @param  string $field Field to retrieve
	 *
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}
		throw new Exception( 'Invalid property: ' . $field );
	}

	/**
	 * Get a list of Gravity Forms
	 *
	 * @return array
	 */
	private function get_forms() {
		$forms_array = \GFFormsModel::get_forms();

		$forms_list = [ 'null' => 'None selected' ];
		foreach ( $forms_array as $forms ) {
			$forms_list[ $forms->id ] = $forms->title;
		}

		return $forms_list;
	}
}
