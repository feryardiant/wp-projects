<?php
/**
 * Admin_Notices class.
 *
 * @package feryardiant/tabellio-cf7
 * @copyright Copyright (c) 2026 Fery Wardiyanto <https://feryardiant.id>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher
 */

namespace Tabellio_CF7;

defined( 'ABSPATH' ) || exit;

/**
 * Class Admin_Notices.
 *
 * @internal
 */
final class Admin_Notices {
	private const SCREEN_IDS = array(
		'plugins',
		'plugins-network',
		'update-core',
		'update-core-network',
		'contact_page_tabellio-cf7',
	);

	/**
	 * Display an admin notice if the PHP version is too low.
	 *
	 * @return void
	 */
	public static function unmet_php_requirements(): void {
		if ( ! Plugin::is_within_screens( self::SCREEN_IDS ) ) {
			return;
		}

		echo '<div class="notice notice-error is-dismissible"><p>';

		echo \wp_kses(
			sprintf(
				/* translators: %s: version of PHP required by Tabellio for Contact Form 7 plugin. */
				\__( '<strong>Tabellio for Contact Form 7</strong> requires at least version <strong>%s</strong> of <strong>PHP</strong> and has been paused.', 'tabellio-cf7' ),
				Plugin::MINIMUM_PHP_VERSION
			),
			array( 'strong' => array() )
		);

		echo '</p></div>';
	}

	/**
	 * Display an admin notice if the WordPress version is too low.
	 *
	 * @return void
	 */
	public static function unmet_wp_requirements(): void {
		if ( ! Plugin::is_within_screens( self::SCREEN_IDS ) ) {
			return;
		}

		echo '<div class="notice notice-error is-dismissible"><p>';

		echo \wp_kses(
			sprintf(
				/* translators: %s: version of WordPress required by Tabellio for Contact Form 7 plugin. */
				\__( '<strong>Tabellio for Contact Form 7</strong> requires at least version <strong>%s</strong> of <strong>WordPress</strong> and has been paused.', 'tabellio-cf7' ),
				Plugin::MINIMUM_WP_VERSION
			),
			array( 'strong' => array() )
		);

		echo '</p></div>';
	}

	/**
	 * Display an admin notice if the Contact Form 7 version is too low.
	 *
	 * @return void
	 */
	public static function unmet_cf7_requirements(): void {
		if ( ! Plugin::is_within_screens( self::SCREEN_IDS ) ) {
			return;
		}

		echo '<div class="notice notice-error is-dismissible"><p>';

		echo \wp_kses(
			sprintf(
				/* translators: %s: version of Contact Form 7 required by Tabellio for Contact Form 7 plugin. */
				\__( '<strong>Tabellio for Contact Form 7</strong> requires at least version <strong>%s</strong> of <strong>Contact Form 7</strong> and has been paused.', 'tabellio-cf7' ),
				Plugin::MINIMUM_WPCF7_VERSION
			),
			array( 'strong' => array() )
		);

		echo '</p></div>';
	}
}
