<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use Brain\Monkey\Functions;
use Override;
use UnitTests\TabellioCF7\TestCase as BaseTestCase;
use WP_Post_Type;

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
        $mockPostType = mock(WP_Post_Type::class);

        Functions\when('get_post_type_object')->alias(static fn () => $mockPostType);

        require_once "$path/includes/autoload.php";
    }
}
