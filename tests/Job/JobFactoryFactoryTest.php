<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Job;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Job\JobFactoryFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * This tests the job builder factory.
 *
 * @covers \CyberSpectrum\I18N\Job\JobFactoryFactory
 */
class JobFactoryFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $providers     = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $jobBuilders   = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $logger        = $this->getMockForAbstractClass(LoggerInterface::class);
        $configuration = new Configuration();
        $factory       = new JobFactoryFactory($providers, $jobBuilders, $logger);

        $configuration->setJob(new Definition('job'));

        $jobBuilder = $factory->create($configuration);

        self::assertSame(['job'], $jobBuilder->getJobNames());
    }
}
