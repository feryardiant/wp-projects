<?php
/**
 * Contact_Form7 Integration class.
 *
 * @package feryardiant/tabellio-cf7
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

declare( strict_types = 1 );

namespace Tabellio_CF7\Integrations;

use Tabellio_CF7\Html_Element;
use Tabellio_CF7\Item;
use Tabellio_CF7\Option;
use Tabellio_CF7\Submission;
use WP_Post_Type;
use WPCF7_ContactForm;

defined( 'ABSPATH' ) || exit;

/**
 * Class Contact_Form7 Integration.
 *
 * @internal
 */
final class Contact_Form7 {
	/**
	 * The submission custom post type object.
	 *
	 * @var null|WP_Post_Type
	 */
	public readonly ?WP_Post_Type $submission;

	/**
	 * Constructs the Contact_Form7 integration instance.
	 */
	public function __construct() {
		$this->submission = \get_post_type_object( Submission::POST_TYPE );

		/**
		 * Register new contact form option properties.
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
		 */
		\add_filter( 'wpcf7_editor_panels', array( $this, 'editor_panels' ) );

		/**
		 * Capture the contact form submission and store it to database before sending it.
		 */
		\add_action( 'wpcf7_before_send_mail', array( $this, 'before_send_mail' ) );

		/**
		 * Prepare to store option properties values.
		 */
		\add_action( 'wpcf7_save_contact_form', array( $this, 'save_contact_form' ), 10, 2 );
	}

	/**
	 * Add a submissions panel to the contact form editor.
	 *
	 * @param array $panels The existing editor panels.
	 * @return array
	 */
	public function editor_panels( array $panels ): array {

		$panels[ Option::FORM_PROP_KEY ] = array(
			'title'    => $this->submission->label,
			'callback' => array( $this, 'render_editor_panel' ),
		);

		return $panels;
	}

	/**
	 * Render the submissions panel for the contact form editor.
	 *
	 * @param WPCF7_ContactForm $contact_form The contact form.
	 * @internal
	 * @return void
	 */
	public function render_editor_panel( WPCF7_ContactForm $contact_form ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		$panel_title = $this->submission->label;

		Html_Element::div(
			child: static fn ( $elm ) => $elm
			->h2( child: $panel_title )

			->call(
				static function ( $elm ) use ( $contact_form ) {
					require_once TABELLIO_PLUGIN_DIR . '/views/editor-panel.php';

					return $elm;
				},
			)
		);
	}

	/**
	 * Capture the contact form submission and store it to database before sending it.
	 *
	 * @param WPCF7_ContactForm $contact_form The contact form object.
	 */
	public function before_send_mail( WPCF7_ContactForm $contact_form ): void {
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
	}

	/**
	 * Prepare to store option properties values.
	 *
	 * @param WPCF7_ContactForm $contact_form The contact form object.
	 * @param array             $data         The form data being saved.
	 * @return void
	 */
	public function save_contact_form( WPCF7_ContactForm $contact_form, array $data ): void {
		$submissions = \wp_parse_args( $data[ Submission::MENU_SLUG ] ?? array(), array() );

		$contact_form->set_properties( array( Option::FORM_PROP_KEY => $submissions ) );
	}
}
