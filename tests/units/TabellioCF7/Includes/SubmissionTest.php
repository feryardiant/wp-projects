<?php

declare(strict_types=1);

namespace UnitTests\TabellioCF7\Includes;

use PHPUnit\Framework\Attributes\CoversClass;
use Tabellio_CF7\Submission;
use UnitTests\TabellioCF7\TestCase;

/**
 * Unit tests for the Submission class.
 */
#[CoversClass(Submission::class)]
class SubmissionTest extends TestCase
{
    /**
     * Verifies that the Submission class is loaded and available.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(class_exists(Submission::class));
    }
}
