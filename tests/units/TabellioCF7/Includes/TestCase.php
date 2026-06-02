<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use Override;
use UnitTests\TabellioCF7\TestCase as BaseTestCase;

/**
 * Base Test Case for CF7 Entry Manager unit tests.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforePackage(): void
    {
        parent::setUpBeforePackage();

        defined('TABELLIO_VERSION') || define('TABELLIO_VERSION', static::package('version'));
        defined('TABELLIO_PLUGIN_DIR') || define('TABELLIO_PLUGIN_DIR', static::package('path'));
        defined('TABELLIO_PLUGIN_FILE') || define('TABELLIO_PLUGIN_FILE', static::package('entrypoint'));
    }

    #[Override]
    protected function preparePackage(string $name, string $path, ?string $url, ?string $version): void
    {
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

        require_once "$path/includes/autoload.php";
    }
}
