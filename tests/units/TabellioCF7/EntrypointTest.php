<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7;

use Brain\Monkey\Actions;
use Brain\Monkey\Filters;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use Tabellio_CF7\Option;
use Tabellio_CF7\Plugin;
use WPCF7_ContactForm;

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

        Filters\expectAdded('tabellio_editor_panel_options')->once()->whenHappen(function ($callback) {
            $this->assertInstanceOf(\Closure::class, $callback);

            $cf7 = mock(WPCF7_ContactForm::class);

            $cf7->shouldReceive('collect_mail_tags')
                ->once()
                ->andReturn([]);

            $options = $callback([], $cf7);

            $this->assertArrayHasKey(Option::SHOULD_RECORD_KEY, $options);
            $this->assertArrayHasKey(Option::SUBJECT_FIELD_KEY, $options);
            $this->assertArrayHasKey(Option::MESSAGE_FIELD_KEY, $options);
            $this->assertArrayHasKey(Option::STORE_AUTHOR_KEY, $options);
            $this->assertArrayHasKey(Option::NAME_FIELD_KEY, $options);
            $this->assertArrayHasKey(Option::EMAIL_FIELD_KEY, $options);
            $this->assertArrayHasKey(Option::PHONE_FIELD_KEY, $options);
        });

        require static::package('entrypoint');

        $this->assertTrue(defined('TABELLIO_VERSION'));
        $this->assertTrue(defined('TABELLIO_PLUGIN_DIR'));
        $this->assertTrue(defined('TABELLIO_PLUGIN_FILE'));
    }

    #[Test]
    #[Group('initialization')]
    public function shouldNotBeInitializedWhenRequirementsNotMet()
    {
        Functions\expect('register_activation_hook')->never();
        Functions\expect('register_deactivation_hook')->never();

        Actions\expectAdded('wpcf7_init')->never();
        Filters\expectAdded('tabellio_editor_panel_options')->never();

        $this->mockStaticMethods(Plugin::class, [
            'check_requirements' => fn ($mock) => $mock->twice(),
            'is_met_requirements' => fn ($mock) => $mock->andReturnFalse(),
        ]);

        require static::package('entrypoint');
    }
}
