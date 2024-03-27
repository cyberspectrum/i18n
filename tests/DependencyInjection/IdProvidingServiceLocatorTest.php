<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\DependencyInjection;

use CyberSpectrum\I18N\DependencyInjection\IdProvidingServiceLocator;
use PHPUnit\Framework\TestCase;

/** @covers \CyberSpectrum\I18N\DependencyInjection\IdProvidingServiceLocator */
class IdProvidingServiceLocatorTest extends TestCase
{
    public function testReturnsIds(): void
    {
        $locator = new IdProvidingServiceLocator([
            'test1' => \Closure::fromCallable(function () {
            }),
            'test2' => \Closure::fromCallable(function () {
            }),
            'test3' => \Closure::fromCallable(function () {
            }),
        ]);

        self::assertSame(['test1', 'test2', 'test3'], $locator->ids());
    }
}
