<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use Tabellio_CF7\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use UnitTests\TabellioCF7\TestCase;

/**
 * Unit tests for the Item class.
 */
#[CoversClass(Item::class)]
class ItemTest extends TestCase
{
    /**
     * Setup before any test in this class runs.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once static::packageFile('tabellio-cf7/includes/class-item.php');
    }

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
