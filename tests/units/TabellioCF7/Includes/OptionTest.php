<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use PHPUnit\Framework\Attributes\CoversClass;
use Tabellio_CF7\Option;

/**
 * Unit tests for the Option class.
 */
#[CoversClass(Option::class)]
class OptionTest extends TestCase
{
    /**
     * Verifies that the Option class is loaded and available.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(class_exists(Option::class));
    }
}
