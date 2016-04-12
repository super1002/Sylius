<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Registry;

use Webmozart\Assert\Assert;
use Zend\Stdlib\PriorityQueue;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class PrioritizedServiceRegistry implements PrioritizedServiceRegistryInterface
{
    /**
     * @var PriorityQueue
     */
    protected $services;

    /**
     * Interface which is required by all services.
     *
     * @var string
     */
    protected $interface;

    /**
     * @param $interface
     */
    public function __construct($interface)
    {
        $this->interface = $interface;
        $this->services = new PriorityQueue();
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->services;
    }

    /**
     * {@inheritdoc}
     */
    public function register($service, $priority = 0)
    {
        $this->assertServiceHaveType($service);
        $this->services->insert($service, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function unregister($service)
    {
        if (!$this->has($service)) {
            throw new NonExistingServiceException(gettype($service));
        }

        $this->services->remove($service);
    }

    /**
     * {@inheritdoc}
     */
    public function has($service)
    {
        $this->assertServiceHaveType($service);

        return $this->services->contains($service);
    }

    /**
     * @param object $service
     */
    private function assertServiceHaveType($service)
    {
        Assert::isInstanceOf(
            $service,
            $this->interface,
            'Service for this registry needs to implement "%2$s", "%s" given.'
        );
    }
}
