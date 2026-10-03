<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use PHPUnit\Framework\Attributes\CoversClass;
use Tabellio_CF7\Html_Element;

/**
 * Unit tests for the Html_Element class.
 */
#[CoversClass(Html_Element::class)]
class HtmlElementTest extends TestCase
{
    /**
     * Verifies that the Html_Element class is loaded and available.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(class_exists(Html_Element::class));
    }
}
