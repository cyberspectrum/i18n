<?php

declare(strict_types=1);

namespace CyberSpectrum\I18N\Configuration;

use CyberSpectrum\I18N\Configuration\Definition\Definition;
use CyberSpectrum\I18N\Configuration\DefinitionBuilder\DefinitionBuilderInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * This builds job and dictionary definitions from configuration arrays.
 *
 * @psalm-type TDictionaryDefinitionConfigurationArray=array{
 *   type: string
 * }
 * @psalm-type TJobDefinitionConfigurationArray=array{
 *   type: string
 * }
 */
class DefinitionBuilder
{
    /** The services for building definitions. */
    private ServiceLocator $dictionaryBuilders;

    /** The services for building definitions. */
    private ServiceLocator $jobBuilders;

    public function __construct(ServiceLocator $dictionaryBuilders, ServiceLocator $jobBuilders)
    {
        $this->dictionaryBuilders = $dictionaryBuilders;
        $this->jobBuilders        = $jobBuilders;
    }

    /**
     * Build a job.
     *
     * @param Configuration                           $configuration The configuration to populate.
     * @param TDictionaryDefinitionConfigurationArray $data          The configuration values to use.
     *
     * @throws RuntimeException When the dictionary type is not registered.
     */
    public function buildDictionary(Configuration $configuration, array $data): Definition
    {
        try {
            $builder = $this->dictionaryBuilders->get($data['type']);
            if (!$builder instanceof DefinitionBuilderInterface) {
                throw new RuntimeException('Builder for \'' . $data['type'] . '\' is invalid');
            }
        } catch (ServiceNotFoundException $exception) {
            throw new RuntimeException('Unknown dictionary type "' . $data['type'] . '"', 0, $exception);
        }

        return $builder->build($configuration, $data);
    }

    /**
     * Build a job.
     *
     * @param Configuration                    $configuration The configuration to populate.
     * @param TJobDefinitionConfigurationArray $data          The configuration values to use.
     *
     * @throws RuntimeException When the job type is not registered.
     */
    public function buildJob(Configuration $configuration, array $data): Definition
    {
        try {
            $builder = $this->jobBuilders->get($data['type']);
            if (!$builder instanceof DefinitionBuilderInterface) {
                throw new RuntimeException('Builder for \'' . $data['type'] . '\' is invalid');
            }
        } catch (ServiceNotFoundException $exception) {
            throw new RuntimeException('Unknown job type "' . $data['type'] . '"', 0, $exception);
        }

        return $builder->build($configuration, $data);
    }
}
