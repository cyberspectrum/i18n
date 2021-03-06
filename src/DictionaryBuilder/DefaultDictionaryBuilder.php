<?php

/**
 * This file is part of cyberspectrum/i18n.
 *
 * (c) 2018 CyberSpectrum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    cyberspectrum/i18n
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  2018 CyberSpectrum.
 * @license    https://github.com/cyberspectrum/i18n/blob/master/LICENSE MIT
 * @filesource
 */

declare(strict_types = 1);

namespace CyberSpectrum\I18N\DictionaryBuilder;

use CyberSpectrum\I18N\Dictionary\DictionaryInterface;
use CyberSpectrum\I18N\Dictionary\DictionaryProviderInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryInterface;
use CyberSpectrum\I18N\Dictionary\WritableDictionaryProviderInterface;
use CyberSpectrum\I18N\Exception\DictionaryNotFoundException;
use CyberSpectrum\I18N\Configuration\Definition\DictionaryDefinition;
use CyberSpectrum\I18N\Job\JobFactory;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * This builds dictionaries from the configured providers.
 */
class DefaultDictionaryBuilder implements DictionaryBuilderInterface
{
    /**
     * The service locator.
     *
     * @var ServiceLocator
     */
    private $providers;

    /**
     * Create a new instance.
     *
     * @param ServiceLocator $providers The dictionary providers.
     */
    public function __construct(ServiceLocator $providers)
    {
        $this->providers = $providers;
    }

    /**
     * Build a dictionary from the passed definition.
     *
     * @param JobFactory           $factory    The job builder for recursive calls.
     * @param DictionaryDefinition $definition The definition.
     *
     * @return DictionaryInterface
     *
     * @throws \RuntimeException When the defined provider can not create readable dictionaries.
     */
    public function build(JobFactory $factory, DictionaryDefinition $definition): DictionaryInterface
    {
        $provider = $this->getProvider($definition);
        if (!($provider instanceof DictionaryProviderInterface)) {
            throw new \RuntimeException(
                'Dictionary provider "' . $definition->getProvider() . '" can not create readable dictionaries.'
            );
        }

        /** @var DictionaryProviderInterface $provider */
        return $provider->getDictionary(
            $definition->getDictionary(),
            $definition->getSourceLanguage(),
            $definition->getTargetLanguage(),
            $definition->getData()
        );
    }

    /**
     * Build a writable dictionary from the passed definition.
     *
     * @param JobFactory           $factory    The job builder for recursive calls.
     * @param DictionaryDefinition $definition The definition.
     *
     * @return WritableDictionaryInterface
     *
     * @throws \RuntimeException When the defined provider can not create writable dictionaries.
     */
    public function buildWritable(JobFactory $factory, DictionaryDefinition $definition): WritableDictionaryInterface
    {
        $provider = $this->getProvider($definition);
        if (!($provider instanceof WritableDictionaryProviderInterface)) {
            throw new \RuntimeException(
                'Dictionary provider "' . $definition->getProvider() . '" can not create writable dictionaries.'
            );
        }

        /** @var WritableDictionaryProviderInterface $provider */
        try {
            return $provider->getDictionaryForWrite(
                $definition->getDictionary(),
                $definition->getSourceLanguage(),
                $definition->getTargetLanguage(),
                $definition->getData()
            );
        } catch (DictionaryNotFoundException $exception) {
            return $provider->createDictionary(
                $definition->getDictionary(),
                $definition->getSourceLanguage(),
                $definition->getTargetLanguage(),
                $definition->getData()
            );
        }
    }

    /**
     * Get the provider.
     *
     * @param DictionaryDefinition $definition The definition.
     *
     * @return DictionaryProviderInterface|WritableDictionaryProviderInterface
     *
     * @throws \UnexpectedValueException When no provider with the defined name can be obtained.
     */
    private function getProvider(DictionaryDefinition $definition)
    {
        if (!$this->providers->has($providerName = $definition->getProvider())) {
            throw new \UnexpectedValueException('No provider named "' . $providerName . '" registered.');
        }

        return $this->providers->get($definition->getProvider());
    }
}
