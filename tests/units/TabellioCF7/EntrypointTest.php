<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7;

use Brain\Monkey\Actions;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use Tabellio_CF7\Plugin;

/**
 * Unit tests for the CF7 Entry Manager plugin main file.
 *
 * @preserveGlobalState disabled
 */
#[RunClassInSeparateProcess]
class EntrypointTest extends TestCase
{
    #[Test]
    #[Group('initialization')]
    public function shouldBeInitializedWhenRequirementsMet()
    {
        Functions\expect('register_activation_hook')->once()->andReturnUsing(function ($_, $callback) {
            $this->assertIsArray($callback);
            $this->assertIsCallable($callback);
        });

        Functions\expect('register_deactivation_hook')->once()->andReturnUsing(function ($_, $callback) {
            $this->assertIsArray($callback);
            $this->assertIsCallable($callback);
        });

        Actions\expectAdded('wpcf7_init')->once()->whenHappen(function ($callback) {
            $this->assertIsArray($callback);

            $this->assertSame(Plugin::class, $callback[0]);
            $this->assertSame('wpcf7_init', $callback[1]);
        });

        require static::package('entrypoint');

        $this->assertTrue(defined('TABELLIO_VERSION'));
        $this->assertTrue(defined('TABELLIO_PLUGIN_DIR'));
        $this->assertTrue(defined('TABELLIO_PLUGIN_FILE'));
    }
}
