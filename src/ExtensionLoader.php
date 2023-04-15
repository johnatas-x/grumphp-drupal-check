<?php

namespace GrumphpDrupalCheck;

use GrumPHP\Extension\ExtensionInterface;
use _HumbugBox1bcd60ea4a04\Symfony\Component\DependencyInjection\ContainerBuilder;
use _HumbugBox1bcd60ea4a04\Symfony\Component\DependencyInjection\Reference;

/**
 * Load extensions for grumphp.
 */
class ExtensionLoader implements ExtensionInterface
{
    /**
     * @param \_HumbugBox1bcd60ea4a04\Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return \_HumbugBox1bcd60ea4a04\Symfony\Component\DependencyInjection\Definition
     *
     * @throws \_HumbugBox1bcd60ea4a04\Exception
     * @throws \_HumbugBox1bcd60ea4a04\Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws \_HumbugBox1bcd60ea4a04\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \_HumbugBox1bcd60ea4a04\Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \_HumbugBox1bcd60ea4a04\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function load(ContainerBuilder $container) : void
    {
        $container->register('task.drupalcheck', DrupalCheck::class)
            ->addArgument(new Reference('process_builder'))
            ->addArgument(new Reference('formatter.raw_process'))
            ->addTag('grumphp.task', ['task' => 'drupalcheck']);
    }

}
