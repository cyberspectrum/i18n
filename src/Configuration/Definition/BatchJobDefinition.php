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

namespace CyberSpectrum\I18N\Configuration\Definition;

/**
 * This describes a batch job.
 */
class BatchJobDefinition extends Definition
{
    /**
     * The job list.
     *
     * @var Definition[]
     */
    private $jobs;

    /**
     * Create a new instance.
     *
     * @param string       $name The name.
     * @param Definition[] $jobs The job definitions.
     * @param array        $data The additional data.
     */
    public function __construct(string $name, array $jobs, array $data = [])
    {
        parent::__construct($name, $data);
        $this->jobs = $jobs;
    }

    /**
     * Retrieve jobs.
     *
     * @return Definition[]
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }
}
