<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7;

use UnitTests\BaseTestCase;

/**
 * Base Test Case for CF7 Entry Manager unit tests.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Setup before any test in this class runs.
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $plugin_dir = static::packageFile('tabellio-cf7');
        $plugin_pkg = json_decode(file_get_contents($plugin_dir . '/package.json'));

        defined('TABELLIO_VERSION') || define('TABELLIO_VERSION', $plugin_pkg->version);
        defined('TABELLIO_PLUGIN_DIR') || define('TABELLIO_PLUGIN_DIR', $plugin_dir);
        defined('TABELLIO_PLUGIN_FILE') || define('TABELLIO_PLUGIN_FILE', $plugin_dir . '/tabellio-cf7.php');

        require_once $plugin_dir . '/includes/autoload.php';

        if (! class_exists('WPCF7_HTMLFormatter')) {
            eval(
                'class WPCF7_HTMLFormatter {
				public const placeholder_block = "pb";
				public const placeholder_inline = "pi";
				public const void_elements = ["br", "hr", "input", "img"];
				public const p_parent_elements = ["div"];
				public const p_nonparent_elements = ["p"];
				public const p_child_elements = ["span"];
				public const br_parent_elements = ["div"];
				public function __construct($opt) {}
				public function append_start_tag($t, $a) {}
				public function append_preformatted($c) {}
				public function end_tag($t) {}
				public function append_comment($c) {}
				public function print() { echo "rendered"; }
			}'
            );
        }
    }
}
