<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use PHPUnit\Framework\Attributes\CoversClass;
use Tabellio_CF7\Page_Element;

/**
 * Unit tests for the Page_Element class.
 */
#[CoversClass(Page_Element::class)]
class PageElementTest extends TestCase
{
    /**
     * Verifies that the Page_Element class is loaded and available.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(class_exists(Page_Element::class));
    }
}
