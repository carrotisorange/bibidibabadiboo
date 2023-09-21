<?php
/**
 * @see       https://github.com/zendframework/zend-hydrator for the canonical source repository
 * @copyright Copyright (c) 2010-2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Hydrator;

interface HydratorAwareInterface
{
    /**
     * Set hydrator
     */
    public function setHydrator(HydratorInterface $hydrator) : void;

    /**
     * Retrieve hydrator
     */
    public function getHydrator() : ?HydratorInterface;
}
