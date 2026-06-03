<?php

declare(strict_types=1);

namespace IntegrationTests\TabellioCF7;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tabellio_CF7\Plugin;

/**
 * Integration tests for the Plugin class.
 *
 * These tests verify the plugin's integration with WordPress hooks,
 * lifecycle events, and environment requirements.
 */
class PluginTest extends TestCase
{
    #[Test]
    #[Group('initialization')]
    public function shouldRegisterCoreHooksOnInit()
    {
        // Act: Manually trigger the init method
        Plugin::wpcf7_init();

        // Assert: Verify that essential filters and actions are registered.
        // We use has_filter as has_action is an alias for it.

        // Check if admin menu registration is registered.
        // Since add_admin_page calls add_action('admin_menu', ...), we verify that.
        $this->assertNotFalse(
            \has_action('admin_menu'),
            'Admin menu registration should be hooked to admin_menu.'
        );
    }
}
