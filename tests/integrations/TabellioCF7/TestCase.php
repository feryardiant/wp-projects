<?php

declare(strict_types=1);

namespace IntegrationTests\TabellioCF7;

use IntegrationTests\BaseTestCase;
use Override;

/**
 * Base Test Case for CF7 Entry Manager integration tests.
 */
abstract class TestCase extends BaseTestCase
{
    protected const PACKAGE_NAME = 'tabellio-cf7';

    #[Override]
    protected function preparePackage(string $name, string $path, ?string $url, ?string $version): void
    {
        $this->activatePlugin('contact-form-7');
    }
}
