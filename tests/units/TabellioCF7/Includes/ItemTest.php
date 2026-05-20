<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use PHPUnit\Framework\Attributes\CoversClass;
use Tabellio_CF7\Item;
use UnitTests\TabellioCF7\TestCase;

/**
 * Unit tests for the Item class.
 */
#[CoversClass(Item::class)]
class ItemTest extends TestCase
{
    /**
     * Verifies that the Item class is loaded and available.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(class_exists(Item::class));
    }
}
