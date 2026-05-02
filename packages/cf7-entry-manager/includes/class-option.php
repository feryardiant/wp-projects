<?php
/**
 * Option class.
 *
 * @package feryardiant/cf7-entry-manager
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace CF7_Entry_Manager;

use ArrayAccess;
use WPCF7_ContactForm;
use WPCF7_Submission;

/**
 * Class Option.
 */
final class Option implements ArrayAccess {
	public const SHOULD_RECORD_KEY = 'should_record';

	public const SUBJECT_FIELD_KEY = 'subject_field';

	public const MESSAGE_FIELD_KEY = 'message_field';

	public const STORE_AUTHOR_KEY = 'store_author';

	public const NAME_FIELD_KEY = 'name_field';

	public const EMAIL_FIELD_KEY = 'email_field';

	public const PHONE_FIELD_KEY = 'phone_field';

	/**
	 * Default properties.
	 *
	 * @var array
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
	 * The configured value of {$subject_field}.
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
	 * The configured value of {$message_field}.
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
	 * The configured value of {$name_field}.
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
	 * The configured value of {$email_field}.
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
	 * The configured value of {$phone_field}.
	 *
	 * @var string|null
	 */
	public ?string $phone = null;

	/**
	 * Form data.
	 *
	 * @var array
	 */
	private array $form_data = array();

	/**
	 * Field map.
	 *
	 * @var array
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
	 * @return Option|false
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

		$properties   = \wp_parse_args( $contact_form->prop( 'submissions' ), $this->defaults );
		$boolean_keys = array( self::SHOULD_RECORD_KEY, self::STORE_AUTHOR_KEY );

		foreach ( $properties as $key => $value ) {
			$this->$key = in_array( $key, $boolean_keys, true )
				? ! is_null( $value )
				: $value;
		}
	}

	/**
	 * Get the form data for the submission option form.
	 *
	 * @return array<string, mixed>
	 */
	public function form_data() {
		return $this->form_data;
	}

	/**
	 * Offset to retrieve.
	 *
	 * @param mixed $offset The offset.
	 * @return mixed
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
