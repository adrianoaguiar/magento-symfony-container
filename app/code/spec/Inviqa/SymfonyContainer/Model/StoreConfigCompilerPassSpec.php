<?php

namespace spec;

use Bridge\MageApp;
use Bridge\MageStore;
use Inviqa_SymfonyContainer_Model_StoreConfigCompilerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class Inviqa_SymfonyContainer_Model_StoreConfigCompilerPassSpec extends ObjectBehavior
{
    function let(MageApp $app, MageStore $mageStore)
    {
        $mageStore->getConfig('store/config/key')->willReturn('some_value');

        $app->getStore()->willReturn($mageStore);
        $services = [
            'app' => $app
        ];

        $this->beConstructedWith($services);
    }

    function it_does_not_add_an_argument_to_service_def_if_tag_does_not_exist(ContainerBuilder $container)
    {
        $container->findTaggedServiceIds(Inviqa_SymfonyContainer_Model_StoreConfigCompilerPass::TAG_NAME)->willReturn([]);

        $container->findDefinition(Argument::any())->shouldNotBeCalled();

        $this->process($container);
    }

    function it_does_not_add_an_argument_to_service_def_if_attribute_does_not_exist(ContainerBuilder $container, Definition $definition)
    {
        $container->findTaggedServiceIds('mage.config')->willReturn([
            'my.service' => [
                Inviqa_SymfonyContainer_Model_StoreConfigCompilerPass::TAG_NAME => null
            ]
        ]);

        $container->findDefinition('my.service')->willReturn($definition);
        $definition->addArgument(Argument::any())->shouldNotBeCalled();

        $this->process($container);
    }

    function it_add_a_null_argument_to_service_def_if_tag_has_no_value(MageStore $mageStore, ContainerBuilder $container, Definition $definition)
    {
        $container->findTaggedServiceIds('mage.config')->willReturn([
            'my.service' => [
                Inviqa_SymfonyContainer_Model_StoreConfigCompilerPass::TAG_NAME => ['key' => '']
            ]
        ]);

        $mageStore->getConfig('')->shouldBeCalled();

        $container->findDefinition('my.service')->willReturn($definition);
        $definition->addArgument(null)->shouldBeCalled();

        $this->process($container);
    }

    function it_adds_an_argument_to_service_def_if_tag_has_value(ContainerBuilder $container, Definition $definition)
    {
        $container->findTaggedServiceIds('mage.config')->willReturn([
            'my.service' => [
                Inviqa_SymfonyContainer_Model_StoreConfigCompilerPass::TAG_NAME => ['key' => 'store/config/key']
            ]
        ]);

        $container->findDefinition('my.service')->willReturn($definition);
        $definition->addArgument('some_value')->shouldBeCalled();

        $this->process($container);
    }
}
