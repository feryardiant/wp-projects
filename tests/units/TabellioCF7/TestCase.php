<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7;

use UnitTests\BaseTestCase;

/**
 * Base Test Case for CF7 Entry Manager unit tests.
 */
abstract class TestCase extends BaseTestCase
{
    protected const PACKAGE_NAME = 'tabellio-cf7';

    protected function packageMetadata(): array
    {
        return [
            'Name' => 'Tabellio for Contact Form 7',
            'PluginURI' => '',
            'Description' => 'Never lose a lead again. Save, manage, and convert every Contact Form 7 submission directly in your WordPress dashboard.',
            'Network' => false,
            'UpdateURI' => '',
            'RequiresPlugins' => '',
        ];
    }
}
