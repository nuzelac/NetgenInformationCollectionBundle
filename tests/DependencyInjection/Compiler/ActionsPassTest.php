<?php

namespace Netgen\Bundle\InformationCollectionBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\InformationCollectionBundle\DependencyInjection\Compiler\ActionsPass;
use Netgen\Bundle\InformationCollectionBundle\Priority;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ActionsPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ActionsPass());
    }

    public function testCompilerPassCollectsValidServices()
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', ['alias' => 'custom_action', 'priority' => 100]);
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            [
                'custom_action',
                new Reference('my_action'),
                100
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\LogicException
     * @expectedExceptionMessage 'netgen_information_collection.action' service tag needs an 'alias' attribute to identify the action. None given.
     */
    public function testCompilerPassMustThrowExceptionIfActionServiceHasntGotAlias()
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);

        $action = new Definition();
        $action->addTag('netgen_information_collection.action');
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            [
                new Reference('my_action'),
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\LogicException
     * @expectedExceptionMessage Service my_action uses priority less than allowed. Priority must be greater than or equal to -255.
     */
    public function testCompilerWithServicePriorityLessThanAllowed()
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);
        $priority = Priority::MIN_PRIORITY - 1;

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', ['alias' => 'custom_action', 'priority' => $priority]);
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            [
                'custom_action',
                new Reference('my_action'),
                $priority
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\LogicException
     * @expectedExceptionMessage Service my_action uses priority greater than allowed. Priority must be lower than or equal to 255.
     */
    public function testCompilerWithServicePriorityGreaterThanAllowed()
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);
        $priority = Priority::MAX_PRIORITY + 1;

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', ['alias' => 'custom_action', 'priority' => $priority]);
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            [
                'custom_action',
                new Reference('my_action'),
                $priority
            ]
        );
    }

    public function testCompilerWithServiceThatIsMissingPriority()
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', ['alias' => 'custom_action']);
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            [
                'custom_action',
                new Reference('my_action'),
                Priority::DEFAULT_PRIORITY
            ]
        );
    }

    public function testCompilerWithDatabasePriority()
    {
        $actionsRegistry = new Definition();
        $this->setDefinition('netgen_information_collection.action.registry', $actionsRegistry);

        $action = new Definition();
        $action->addTag('netgen_information_collection.action', ['alias' => 'database', 'priority' => 300]);
        $this->setDefinition('my_action', $action);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_information_collection.action.registry',
            'addAction',
            [
                'database',
                new Reference('my_action'),
                300
            ]
        );
    }
}
