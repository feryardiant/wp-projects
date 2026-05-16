<?php
/**
 * Option class.
 *
 * @package feryardiant/tabellio-cf7
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace Tabellio_CF7;

use ArrayAccess;
use WPCF7_ContactForm;
use WPCF7_Submission;

defined( 'ABSPATH' ) || exit;

/**
 * Class Option.
 */
final class Option implements ArrayAccess {
	/**
	 * Meta key for whether to record submissions.
	 *
	 * @var string
	 */
	public const SHOULD_RECORD_KEY = 'should_record';

	/**
	 * Meta key for the subject field name.
	 *
	 * @var string
	 */
	public const SUBJECT_FIELD_KEY = 'subject_field';

	/**
	 * Meta key for the message field name.
	 *
	 * @var string
	 */
	public const MESSAGE_FIELD_KEY = 'message_field';

	/**
	 * Meta key for whether to store the author.
	 *
	 * @var string
	 */
	public const STORE_AUTHOR_KEY = 'store_author';

	/**
	 * Meta key for the author name field name.
	 *
	 * @var string
	 */
	public const NAME_FIELD_KEY = 'name_field';

	/**
	 * Meta key for the author email field name.
	 *
	 * @var string
	 */
	public const EMAIL_FIELD_KEY = 'email_field';

	/**
	 * Meta key for the author phone field name.
	 *
	 * @var string
	 */
	public const PHONE_FIELD_KEY = 'phone_field';

	/**
	 * Meta key for the form property name.
	 *
	 * @var string
	 */
	public const FORM_PROP_KEY = 'submissions';

	/**
	 * Default properties.
	 *
	 * @var array<string, mixed>
	 */
	public readonly array $defaults;

	/**
	 * Whether to record the submissions to the database.
	 *
	 * @var bool
	 */
	public bool $should_record;

	/**
	 * The field key that is identified as a submission subject.
	 *
	 * @var string
	 */
	public string $subject_field;

	/**
	 * The configured value of the subject field.
	 *
	 * @var string|null
	 */
	public ?string $subject = null;

	/**
	 * The field key that is identified as a submission message.
	 *
	 * @var string
	 */
	public string $message_field;

	/**
	 * The configured value of the message field.
	 *
	 * @var string|null
	 */
	public ?string $message = null;

	/**
	 * Whether to store submission author as a subscriber.
	 *
	 * @var bool
	 */
	public bool $store_author;

	/**
	 * The field key that is identified as a submission name.
	 *
	 * @var string
	 */
	public string $name_field;

	/**
	 * The configured value of the name field.
	 *
	 * @var string|null
	 */
	public ?string $name = null;

	/**
	 * The field key that is identified as a submission email.
	 *
	 * @var string
	 */
	public string $email_field;

	/**
	 * The configured value of the email field.
	 *
	 * @var string|null
	 */
	public ?string $email = null;

	/**
	 * The field key that is identified as a submission phone.
	 *
	 * @var string
	 */
	public string $phone_field;

	/**
	 * The configured value of the phone field.
	 *
	 * @var string|null
	 */
	public ?string $phone = null;

	/**
	 * Form data.
	 *
	 * @var array<string, string>
	 */
	private array $form_data = array();

	/**
	 * Field map linking internal properties to field keys.
	 *
	 * @var array<string, string>
	 */
	private array $field_map = array(
		'subject' => self::SUBJECT_FIELD_KEY,
		'message' => self::MESSAGE_FIELD_KEY,
		'name'    => self::NAME_FIELD_KEY,
		'email'   => self::EMAIL_FIELD_KEY,
		'phone'   => self::PHONE_FIELD_KEY,
	);

	/**
	 * Get all available options for the given $contact_form.
	 *
	 * @param WPCF7_ContactForm $contact_form The contact form.
	 * @return Option|false The Option instance or false if recording is disabled or submission is missing.
	 */
	public static function get( WPCF7_ContactForm $contact_form ): Option|false {
		$option     = new self( $contact_form );
		$submission = WPCF7_Submission::get_instance();

		if ( ! $submission || ! $option->should_record ) {
			return false;
		}

		foreach ( $contact_form->scan_form_tags() as $tag ) {
			/**
			 * Form tag object.
			 *
			 * @var \WPCF7_FormTag $tag
			 */
			if ( 'submit' === $tag->basetype ) {
				continue;
			}

			$option->form_data[ $tag->name ] = $submission->get_posted_string( $tag->name );
		}

		foreach ( $option->field_map as $key => $field ) {
			$option[ $key ] = $option->form_data[ $option[ $field ] ] ?? null;
		}

		return $option;
	}

	/**
	 * Constructor.
	 *
	 * @param WPCF7_ContactForm $contact_form The contact form.
	 */
	public function __construct(
		private WPCF7_ContactForm $contact_form
	) {
		$this->defaults = array(
			self::SHOULD_RECORD_KEY => null,
			self::SUBJECT_FIELD_KEY => '',
			self::MESSAGE_FIELD_KEY => '',
			self::STORE_AUTHOR_KEY  => null,
			self::NAME_FIELD_KEY    => '',
			self::EMAIL_FIELD_KEY   => '',
			self::PHONE_FIELD_KEY   => '',
		);

		$properties   = \wp_parse_args( $contact_form->prop( self::FORM_PROP_KEY ), $this->defaults );
		$boolean_keys = array( self::SHOULD_RECORD_KEY, self::STORE_AUTHOR_KEY );

		foreach ( $properties as $key => $value ) {
			$this->$key = in_array( $key, $boolean_keys, true )
				? ! is_null( $value )
				: $value;
		}
	}

	/**
	 * Get the form data for the submission.
	 *
	 * @return array<string, string>
	 */
	public function form_data() {
		return $this->form_data;
	}

	/**
	 * Offset to retrieve.
	 *
	 * @param mixed $offset The offset.
	 * @return mixed The value or null if not found.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return $this->$offset ?? null;
	}

	/**
	 * Offset to set.
	 *
	 * @param mixed $offset The offset.
	 * @param mixed $value  The value.
	 * @return void
	 */
	public function offsetSet( $offset, $value ): void {
		if ( array_key_exists( $offset, $this->field_map ) ) {
			$this->$offset = $value;
		}
	}

	/**
	 * Offset to unset.
	 *
	 * @param mixed $offset The offset.
	 * @return void
	 */
	public function offsetUnset( $offset ): void {
		// Doing nothing.
	}

	/**
	 * Offset to check if exists.
	 *
	 * @param mixed $offset The offset.
	 * @return bool
	 */
	public function offsetExists( $offset ): bool {
		return property_exists( $this, $offset );
	}
}
