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

namespace CyberSpectrum\I18N\Configuration;

/**
 * LoaderInterface is the interface implemented by all loader classes.
 */
interface LoaderInterface
{
    /**
     * Loads a resource.
     *
     * @param mixed       $resource The resource.
     * @param string|null $type     The resource type or null if unknown.
     *
     * @return void
     *
     * @throws \Exception If something went wrong.
     */
    public function load($resource, $type = null);

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed       $resource A resource.
     * @param string|null $type     The resource type or null if unknown.
     *
     * @return bool True if this class supports the given resource, false otherwise.
     */
    public function supports($resource, $type = null);
}
