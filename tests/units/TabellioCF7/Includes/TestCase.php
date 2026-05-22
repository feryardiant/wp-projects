<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use UnitTests\TabellioCF7\TestCase as BaseTestCase;

/**
 * Base Test Case for CF7 Entry Manager unit tests.
 */
abstract class TestCase extends BaseTestCase
{
    protected static function packageAutoload(string $name, ?string $type, ?string $version)
    {
        parent::packageAutoload($name, $type, $version);

        $plugin_dir = static::packageFile($name);

        defined('TABELLIO_VERSION') || define('TABELLIO_VERSION', $version);
        defined('TABELLIO_PLUGIN_DIR') || define('TABELLIO_PLUGIN_DIR', $plugin_dir);
        defined('TABELLIO_PLUGIN_FILE') || define('TABELLIO_PLUGIN_FILE', "$plugin_dir/$name.php");

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
