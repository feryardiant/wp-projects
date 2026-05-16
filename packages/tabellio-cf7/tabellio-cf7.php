<?php
/**
 * Tabellio for Contact Form 7
 *
 * @package feryardiant/tabellio-cf7
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 *
 * @wordpress-plugin
 * Plugin Name: Tabellio for Contact Form 7
 * Description: Never lose a lead again. Save, manage, and convert every Contact Form 7 submission directly in your WordPress dashboard.
 * Text Domain: tabellio-cf7
 * Domain Path: /languages
 * Version: 0.1.0
 * Tested up to: 6.9
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * Author: Fery Wardiyanto
 * Author URI: https://feryardiant.id
 * License: GPLv3 or later
 * Requires Plugins: contact-form-7
 */

use Tabellio_CF7\Item;
use Tabellio_CF7\Option;
use Tabellio_CF7\Submission;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin version.
 *
 * @var string
 */
define( 'TABELLIO_VERSION', '0.1.0' );

/**
 * Debug mode flag.
 *
 * @var bool
 */
define( 'TABELLIO_DEBUG', defined( 'WP_DEBUG' ) && boolval( WP_DEBUG ) );

/**
 * Plugin directory path.
 *
 * @var string
 */
define( 'TABELLIO_PLUGIN_DIR', __DIR__ );

/**
 * Minimum required WordPress version.
 *
 * @var string
 */
define( 'TABELLIO__MINIMUM_WP_VERSION', '6.0' );

/**
 * Minimum required Contact Form 7 version.
 *
 * @var string
 */
define( 'TABELLIO__MINIMUM_WPCF7_VERSION', '6.1' );

/**
 * Minimum required PHP version.
 *
 * @var string
 */
define( 'TABELLIO__MINIMUM_PHP_VERSION', '8.1' );

/**
 * Check the current screen.
 *
 * @internal
 * @return bool
 */
function tabellio_within_scoped_screens(): bool {
	if ( ! $screen = get_current_screen() ) {
		return false;
	}

	$scoped_screens = array(
		'plugins',
		'plugins-network',
		'update-core',
		'update-core-network',
		'contact_page_tabellio-cf7',
	);

	return in_array( $screen->id, $scoped_screens, true )
		|| false !== strpos( $screen->id, 'wpcf7' );
}

/**
 * Check if the version of PHP in use on the site is supported.
 */
if ( version_compare( PHP_VERSION, TABELLIO__MINIMUM_PHP_VERSION, '<' ) ) {
	/**
	 * Display an admin notice if the PHP version is too low.
	 *
	 * @return void
	 */
	add_action(
		'admin_notices',
		static function () {
			if ( ! tabellio_within_scoped_screens() ) {
				return;
			}

			echo '<div class="notice notice-error is-dismissible"><p>';

			// phpcs:disable WordPress.Security.EscapeOutput
			printf(
				/* translators: %s: version of PHP required by Tabellio for Contact Form 7 plugin. */
				__( '<strong>Tabellio for Contact Form 7</strong> requires at least version <strong>%s</strong> of <strong>PHP</strong> and has been paused.', 'tabellio-cf7' ),
				TABELLIO__MINIMUM_PHP_VERSION
			);
			// phpcs:enable

			echo '</p></div>';
		}
	);

	return;
}

/**
 * Check if the version of WordPress in use on the site is supported.
 */
if ( version_compare( $GLOBALS['wp_version'], TABELLIO__MINIMUM_WP_VERSION, '<' ) ) {
	/**
	 * Display an admin notice if the WordPress version is too low.
	 *
	 * @return void
	 */
	add_action(
		'admin_notices',
		static function () {
			if ( ! tabellio_within_scoped_screens() ) {
				return;
			}

			echo '<div class="notice notice-error is-dismissible"><p>';

			// phpcs:disable WordPress.Security.EscapeOutput
			printf(
				/* translators: %s: version of WordPress required by Tabellio for Contact Form 7 plugin. */
				__( '<strong>Tabellio for Contact Form 7</strong> requires at least version <strong>%s</strong> of <strong>WordPress</strong> and has been paused.', 'tabellio-cf7' ),
				TABELLIO__MINIMUM_WP_VERSION
			);
			// phpcs:enable

			echo '</p></div>';
		}
	);

	return;
}

/**
 * Perform actions on plugin activation.
 *
 * @return void
 */
register_activation_hook(
	__FILE__,
	static function () {
		// Doing nothing on activation.
	}
);

/**
 * Perform actions on plugin deactivation.
 *
 * @return void
 */
register_deactivation_hook(
	__FILE__,
	static function () {
		// Doing nothing on deactivation.
	}
);

/**
 * Enqueue admin scripts and styles.
 *
 * @param string $suffix The current admin page suffix.
 * @return void
 */
add_action(
	'admin_enqueue_scripts',
	static function ( string $suffix ): void {
		if ( ! in_array( $suffix, array( 'toplevel_page_wpcf7', 'contact_page_tabellio-cf7' ), true ) ) {
			return;
		}

		wp_enqueue_style( 'tabellio-style', plugin_dir_url( __FILE__ ) . 'assets/style.css', array(), TABELLIO_VERSION );
	},
	10,
	1
);

/**
 * Initialize the plugin when Contact Form 7 is ready.
 *
 * @return void
 */
add_action(
	'wpcf7_init',
	static function (): void {
		/**
		 * Check if the version of Contact Form 7 in use on the site is supported by Tabellio for Contact Form 7.
		 */
		if ( version_compare( WPCF7_VERSION, TABELLIO__MINIMUM_WPCF7_VERSION, '<' ) ) {
			/**
			 * Display an admin notice if the Contact Form 7 version is too low.
			 *
			 * @return void
			 */
			add_action(
				'admin_notices',
				static function () {
					if ( ! tabellio_within_scoped_screens() ) {
						return;
					}

					echo '<div class="notice notice-error is-dismissible"><p>';

					// phpcs:disable WordPress.Security.EscapeOutput
					printf(
						/* translators: %s: version of Contact Form 7 required by Tabellio for Contact Form 7 plugin. */
						__( '<strong>Tabellio for Contact Form 7</strong> requires at least version <strong>%s</strong> of <strong>Contact Form 7</strong> and has been paused.', 'tabellio-cf7' ),
						TABELLIO__MINIMUM_WPCF7_VERSION
					);
					// phpcs:enable

					echo '</p></div>';
				}
			);

			return;
		}

		if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			require_once __DIR__ . '/vendor/autoload.php';
		}

		require_once __DIR__ . '/includes/class-item.php';
		require_once __DIR__ . '/includes/class-page-element.php';
		require_once __DIR__ . '/includes/class-list-table.php';
		require_once __DIR__ . '/includes/class-option.php';
		require_once __DIR__ . '/includes/class-submission.php';

		Submission::register();

		/**
		 * Override user contact meta properties.
		 *
		 * @param array $methods The user contact methods.
		 * @return array
		 */
		add_filter(
			'user_contactmethods',
			static fn ( array $methods ) => array_merge(
				array( Submission::USER_PHONE_META_KEY => __( 'Phone Number', 'tabellio-cf7' ) ),
				$methods
			),
			10,
			1
		);

		/**
		 * Register the submissions admin menu.
		 *
		 * @return void
		 */
		\add_action(
			'admin_menu',
			static function (): void {
				$post_type_object = Submission::get_post_type_object();

				$submissions = \add_submenu_page(
					'wpcf7',
					$post_type_object->labels->items_list,
					$post_type_object->labels->menu_name,
					$post_type_object->cap->read_private_posts,
					Submission::MENU_SLUG,
					array( Submission::class, 'admin_management_page' ),
					2,
				);

				\add_action(
					'load-' . $submissions,
					array( Submission::class, 'admin_load_page' ),
					10,
					0
				);
			},
			9,
			0
		);

		/**
		 * Register new contact form option properties.
		 *
		 * @param array $properties The existing contact form properties.
		 * @return array
		 */
		\add_filter(
			'wpcf7_pre_construct_contact_form_properties',
			static fn ( array $properties ): array => array_merge(
				$properties,
				array( Option::FORM_PROP_KEY => array() )
			),
			10,
			1
		);

		/**
		 * Add a submissions panel to the contact form editor.
		 *
		 * @param array $panels The existing editor panels.
		 * @return array
		 */
		\add_filter(
			'wpcf7_editor_panels',
			static function ( array $panels ): array {
				$post_type_object = Submission::get_post_type_object();

				$panels[ Option::FORM_PROP_KEY ] = array(
					'title'    => $post_type_object->label,
					'callback' => array( Submission::class, 'admin_editor_panel' ),
				);

				return $panels;
			},
			10,
			1
		);

		/**
		 * Capture the contact form submission and store it to database before sending it.
		 *
		 * @param WPCF7_ContactForm $contact_form The contact form object.
		 * @return void
		 */
		\add_action(
			'wpcf7_before_send_mail',
			static function ( WPCF7_ContactForm $contact_form ): void {
				$option = Option::get( $contact_form );

				if ( ! $option ) {
					return;
				}

				$form_data = $option->form_data();

				/**
				 * Action hook before saving the submission.
				 *
				 * @param array $form_data The form submission data.
				 */
				\do_action( 'tabellio_before_save', $form_data );

				$returned_id = Item::store( $contact_form, $option );

				/**
				 * Action hook after saving the submission.
				 *
				 * @param array         $form_data   The form submission data.
				 * @param int|\WP_Error $returned_id The ID of the saved submission or error.
				 */
				\do_action( 'tabellio_after_save', $form_data, $returned_id );
			},
			10,
			1
		);

		/**
		 * Prepare to store option properties values.
		 *
		 * @param WPCF7_ContactForm $contact_form The contact form object.
		 * @param array             $data         The form data being saved.
		 * @return void
		 */
		\add_action(
			'wpcf7_save_contact_form',
			static function ( WPCF7_ContactForm $contact_form, array $data ): void {
				$submissions = \wp_parse_args( $data[ Submission::MENU_SLUG ] ?? array(), array() );

				$contact_form->set_properties( array( Option::FORM_PROP_KEY => $submissions ) );
			},
			10,
			2
		);
	}
);

/**
 * Filter the editor panel options for the submissions tab.
 *
 * @param array             $options      The existing panel options.
 * @param WPCF7_ContactForm $contact_form The contact form object.
 * @return array
 */
\add_filter(
	'tabellio_editor_panel_options',
	static function ( array $options, WPCF7_ContactForm $contact_form ) {
		$mail_tags = $contact_form->collect_mail_tags();

		$options[ Option::SHOULD_RECORD_KEY ] = array(
			'label' => \__( 'Record', 'tabellio-cf7' ),
			'hint'  => \__(
				'Whether to record the submissions to the database',
				'tabellio-cf7'
			),
			'atts'  => array( 'type' => 'checkbox' ),
		);

		$options[ Option::SUBJECT_FIELD_KEY ] = array(
			'label'   => \__( 'Subject', 'tabellio-cf7' ),
			'hint'    => \__(
				'Choose which field is identified as a submission subject',
				'tabellio-cf7'
			),
			'type'    => 'select',
			'atts'    => array( 'class' => 'large-text code' ),
			'options' => $mail_tags,
		);

		$options[ Option::MESSAGE_FIELD_KEY ] = array(
			'label'   => \__( 'Message', 'tabellio-cf7' ),
			'hint'    => \__(
				'Choose which field is identified as a submission message',
				'tabellio-cf7'
			),
			'type'    => 'select',
			'atts'    => array( 'class' => 'large-text code' ),
			'options' => $mail_tags,
		);

		$options['sep-1'] = array( 'type' => 'separator' );

		$options[ Option::STORE_AUTHOR_KEY ] = array(
			'label' => \__( 'Author', 'tabellio-cf7' ),
			'hint'  => \__(
				'Whether the submission author will be registered as subscriber',
				'tabellio-cf7'
			),
			'atts'  => array( 'type' => 'checkbox' ),
		);

		$options[ Option::NAME_FIELD_KEY ] = array(
			'label'   => \__( 'Author Name', 'tabellio-cf7' ),
			'hint'    => \__(
				'Choose which field is identified as the submitter\'s name',
				'tabellio-cf7'
			),
			'type'    => 'select',
			'atts'    => array( 'class' => 'large-text code' ),
			'options' => $mail_tags,
		);

		$options[ Option::EMAIL_FIELD_KEY ] = array(
			'label'   => \__( 'Author Email', 'tabellio-cf7' ),
			'hint'    => \__(
				'Choose which field is identified as the submitter\'s email',
				'tabellio-cf7'
			),
			'type'    => 'select',
			'atts'    => array( 'class' => 'large-text code' ),
			'options' => $mail_tags,
		);

		$options[ Option::PHONE_FIELD_KEY ] = array(
			'label'   => \__( 'Author Phone', 'tabellio-cf7' ),
			'hint'    => \__(
				'Choose which field is identified as the submitter\'s phone number',
				'tabellio-cf7'
			),
			'type'    => 'select',
			'atts'    => array( 'class' => 'large-text code' ),
			'options' => $mail_tags,
		);

		return $options;
	},
	10,
	2
);
