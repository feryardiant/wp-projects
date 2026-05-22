<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use PHPUnit\Framework\Attributes\CoversClass;
use Tabellio_CF7\List_Table;

/**
 * Unit tests for the List_Table class.
 */
#[CoversClass(List_Table::class)]
class ListTableTest extends TestCase
{
    /**
     * Verifies that the List_Table class is loaded and available.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(class_exists(List_Table::class));
    }
}
