<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use Closure;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tabellio_CF7\Admin\Submission_Page;
use Tabellio_CF7\Plugin;
use Tabellio_CF7\Submission;
use WP_Filesystem_Direct;

/**
 * Unit tests for the Plugin class.
 */
class PluginTest extends TestCase
{
    #[Test]
    #[Group('positive-value')]
    #[Group('requirement')]
    public function shouldDidNothingWhenRequirementsMet()
    {
        $status = Plugin::is_met_requirements();

        Actions\expectAdded('admin_notices')->never();

        Plugin::check_requirements('Requirement', '0.0.1', '0.0.1');

        $this->assertSame($status, Plugin::is_met_requirements());
    }

    #[Test]
    #[Group('negative-value')]
    #[Group('requirement')]
    public function shouldAddAdminNoticeWhenRequirementsNotMet()
    {
        Actions\expectAdded('admin_notices')->once()->whenHappen(function ($callback) {
            $this->assertInstanceOf(Closure::class, $callback);
        });

        Plugin::check_requirements('Requirement', '0.0.0', '0.0.1');
    }

    #[Test]
    #[Group('requirement')]
    public function shouldNotShowRequirementNoticeOnUndesiredScreens()
    {
        Functions\expect('get_current_screen')->once()->andReturnNull();
        Functions\expect('wp_kses')->never();

        Actions\expectAdded('admin_notices')->once()->whenHappen(function ($callback) {
            $callback();
        });

        Plugin::check_requirements('Requirement', '0.0.0', '0.0.1');
    }

    #[Test]
    #[Group('requirement')]
    public function shouldPrintNoticeOnSpecificScreens()
    {
        $this->expectOutputString(implode('', [
            '<div class="notice notice-error is-dismissible"><p>',
            'The <strong>Tabellio for Contact Form 7</strong> plugin requires at least version <strong>0.0.1</strong>',
            ' of <strong>Requirement</strong>, currently you have <strong>0.0.0</strong>.',
            '</p></div>',
        ]));

        Functions\expect('get_current_screen')->once()->andReturn((object) ['id' => 'plugins']);
        Functions\expect('wp_kses')->andReturnFirstArg();

        Actions\expectAdded('admin_notices')->once()->whenHappen(function ($callback) {
            $callback();
        });

        Plugin::check_requirements('Requirement', '0.0.0', '0.0.1');
    }

    #[Test]
    #[Group('initialization')]
    #[RunInSeparateProcess]
    public function shouldBeInitialized()
    {
        Actions\expectAdded('wp_enqueue_scripts')->once()->whenHappen(function ($callback) {
            $this->assertIsArray($callback);

            $this->assertInstanceOf(Plugin::class, $callback[0]);
            $this->assertSame('enqueue_scripts', $callback[1]);
        });

        Actions\expectAdded('admin_menu')->once()->whenHappen(function ($callback) {
            $this->assertIsArray($callback);

            $this->assertInstanceOf(Submission_Page::class, $callback[0]);
            $this->assertSame('menu', $callback[1]);
        });

        defined('WPCF7_VERSION') || define('WPCF7_VERSION', '6.1');

        $this->mockStaticMethods(Submission::class, [
            'register' => fn ($mock) => $mock->once()
        ]);

        Plugin::wpcf7_init();
    }

    #[Test]
    #[Group('activation')]
    public function shouldDoingActionsOnActivation()
    {
        Actions\expectDone('tabellio_activate')->once();

        Plugin::activate();
    }

    #[Test]
    #[Group('deactivation')]
    public function shouldDoingActionsOnDeactivation()
    {
        Actions\doing('tabellio_deactivate');

        Plugin::deactivate();
    }

    #[Test]
    #[Group('metadata')]
    public function shouldThrowExceptionWhenAccessingInvalidDataKey()
    {
        $plugin = new Plugin(TABELLIO_PLUGIN_FILE);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown plugin metadata: invalid_key');

        Functions\expect('wp_kses')->once()->andReturnFirstArg();

        $plugin->get('invalid_key');
    }

    #[Test]
    #[Group('cached-value')]
    #[Group('static-asset')]
    public function shouldAbleToRetrieveAnAssetMetadataArrayFromCache()
    {
        $plugin = new Plugin(TABELLIO_PLUGIN_FILE);

        // First call
        $plugin->get_asset_url('style.css');

        Functions\expect('plugin_dir_url')->never();

        // Second call
        $asset = $plugin->get_asset_url('style.css');

        $this->assertIsArray($asset);
        $this->assertSame(static::packagePath('assets/style.css'), $asset['dir']);
        $this->assertSame('http://example.com/wp-content/plugins/tabellio-cf7/assets/style.css', $asset['url']);
        $this->assertSame(TABELLIO_VERSION, $asset['version']);
    }

    #[Test]
    #[Group('static-asset')]
    public function shouldAbleToRetrieveAnAssetMetadataBasedOnKey()
    {
        $asset_version = Plugin::instance()->get_asset_url('style.css', 'version');

        $this->assertSame(TABELLIO_VERSION, $asset_version);
    }

    #[Test]
    #[Group('static-asset')]
    public function shouldAppendAssetFileTimeOnDebugMode()
    {
        $plugin = new Plugin(TABELLIO_PLUGIN_FILE);

        defined('SCRIPT_DEBUG') || define('SCRIPT_DEBUG', true);

        $asset_version = $plugin->get_asset_url('style.css', 'version');
        $filetime = (string) filemtime($plugin->get_path('assets/style.css'));

        $this->assertSame(TABELLIO_VERSION . '-' . $filetime, $asset_version);
    }

    #[Test]
    #[Group('negative-value')]
    #[Group('static-asset')]
    public function shouldThrowInvalidArgumentExceptionForInvalidKey()
    {
        Functions\expect('wp_kses')->once()->andReturnFirstArg();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid key: invalid_key, expected 'dir', 'url' or 'version'");

        Plugin::instance()->get_asset_url('style.css', 'invalid_key');
    }

    #[Test]
    #[Group('negative-value')]
    #[Group('static-asset')]
    public function shouldAbleToChangeAssetDirAndThrowExceptionIfNotExists()
    {
        $plugin = new Plugin(TABELLIO_PLUGIN_FILE);

        $plugin->set_asset_dir('elsewhere');

        Functions\expect('wp_kses')->once()->andReturnFirstArg();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Path not found: elsewhere/style.css");

        $plugin->get_asset_url('style.css');
    }

    #[Test]
    #[Group('static-asset')]
    public function shouldCacheFilesystemInstance()
    {
        $plugin = new Plugin(TABELLIO_PLUGIN_FILE);
        $filesystem = (new ReflectionClass(Plugin::class))->getProperty('filesystem');

        Plugin::instance()->get_file_contents('assets/style.css');

        $this->assertInstanceOf(WP_Filesystem_Direct::class, $filesystem->getValue($plugin));
    }
}
