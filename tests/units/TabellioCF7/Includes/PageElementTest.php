<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use Tabellio_CF7\Page_Element;
use PHPUnit\Framework\Attributes\CoversClass;
use UnitTests\TabellioCF7\TestCase;

/**
 * Unit tests for the Page_Element class.
 */
#[CoversClass(Page_Element::class)]
class PageElementTest extends TestCase
{
    /**
     * Setup before any test in this class runs.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        require_once static::packageFile('tabellio-cf7/includes/class-page-element.php');
    }

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
