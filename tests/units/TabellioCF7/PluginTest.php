<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7;

use UnitTests\BaseTestCase;
use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

/**
 * Unit tests for the CF7 Entry Manager plugin main file.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class PluginTest extends BaseTestCase
{
    /**
     * Verifies that the plugin correctly defines its constants and registers primary hooks during initialization.
     *
     * @return void
     */
    public function testPluginInitialization()
    {
        // Mock WP functions used in the main file
        Functions\when('register_activation_hook')->justReturn();
        Functions\when('register_deactivation_hook')->justReturn();
        Functions\when('plugin_dir_url')->justReturn('https://example.com/wp-content/plugins/tabellio-cf7/');
        Functions\when('register_post_type')->justReturn();

        if (! defined('WPCF7_VERSION')) {
            define('WPCF7_VERSION', '6.1');
        }

        // Set WP version global if not available
        if (! isset($GLOBALS['wp_version'])) {
            $GLOBALS['wp_version'] = getenv('WP_VERSION') ?: '6.9';
        }

        // Expect hooks to be added
        // Actions\expectAdded( 'admin_notices' )->never();
        // Actions\expectAdded( 'admin_enqueue_scripts' )->once();
        // Actions\expectAdded( 'wpcf7_init' )->once();
        Actions\expectAdded('wpcf7_init')
            ->once()
            ->whenHappen(function ($callback) {
                Filters\expectAdded('user_contactmethods')->once();
                $callback();
            });

        // Load the plugin file
        require static::packageFile('tabellio-cf7/tabellio-cf7.php');

        // Verify constants
        $this->assertTrue(defined('TABELLIO_VERSION'));
        $this->assertEquals('0.1.0', TABELLIO_VERSION);
        $this->assertTrue(defined('TABELLIO__MINIMUM_WP_VERSION'));
        $this->assertTrue(defined('TABELLIO__MINIMUM_PHP_VERSION'));
    }
}
