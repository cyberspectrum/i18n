<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Exception;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Exception\AbstractDictionaryException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;

#[CoversClass(AbstractDictionaryException::class)]
class AbstractDictionaryExceptionTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testSetsValues(): void
    {
        $previous   = new RuntimeException();
        $dictionary = $this->getMockBuilder(DictionaryInterface::class)->getMock();
        $exception  = $this
            ->getMockBuilder(AbstractDictionaryException::class)
            ->setConstructorArgs([$dictionary, 'message', 23, $previous])
            ->onlyMethods([])
            ->getMock();

        self::assertSame($dictionary, $exception->getDictionary());
        self::assertSame('message', $exception->getMessage());
        self::assertSame(23, $exception->getCode());
        self::assertSame($previous, $exception->getPrevious());
    }
}
