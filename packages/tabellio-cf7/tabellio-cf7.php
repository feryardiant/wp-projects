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

use Tabellio_CF7\Admin_Notices;
use Tabellio_CF7\Option;
use Tabellio_CF7\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin version.
 *
 * @var string
 */
define( 'TABELLIO_VERSION', '0.1.0' );

/**
 * Plugin directory path.
 *
 * @var string
 */
define( 'TABELLIO_PLUGIN_DIR', __DIR__ );

/**
 * Plugin file path.
 *
 * @var string
 */
define( 'TABELLIO_PLUGIN_FILE', __FILE__ );

require_once TABELLIO_PLUGIN_DIR . '/includes/autoload.php';

/**
 * Check if the version of PHP in use on the site is supported.
 */
if ( Plugin::is_unmet_php_requirements() ) {
	/**
	 * Display an admin notice if the PHP version is too low.
	 */
	add_action( 'admin_notices', array( Admin_Notices::class, 'unmet_php_requirements' ) );

	return;
}

/**
 * Check if the version of WordPress in use on the site is supported.
 */
if ( Plugin::is_unmet_wp_requirements() ) {
	/**
	 * Display an admin notice if the WordPress version is too low.
	 */
	add_action( 'admin_notices', array( Admin_Notices::class, 'unmet_wp_requirements' ) );

	return;
}

/**
 * Perform actions on plugin activation.
 */
register_activation_hook( __FILE__, array( Plugin::class, 'activate' ) );

/**
 * Perform actions on plugin deactivation.
 */
register_deactivation_hook( __FILE__, array( Plugin::class, 'deactivate' ) );

/**
 * Initialize the plugin when Contact Form 7 is ready.
 */
add_action( 'wpcf7_init', array( Plugin::class, 'wpcf7_init' ) );

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
