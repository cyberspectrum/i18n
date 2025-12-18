<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Test\Job;

use CyberSpectrum\I18N\Configuration\Configuration;
use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Job\JobFactoryFactory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

#[CoversClass(JobFactoryFactory::class)]
class JobFactoryFactoryTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testCreate(): void
    {
        $providers     = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $jobBuilders   = $this->getMockBuilder(ServiceLocator::class)->disableOriginalConstructor()->getMock();
        $logger        = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $configuration = new Configuration();
        $factory       = new JobFactoryFactory($providers, $jobBuilders, $logger);

        $configuration->setJob(new Definition('job'));

        $jobBuilder = $factory->create($configuration);

        self::assertSame(['job'], $jobBuilder->getJobNames());
    }
}
