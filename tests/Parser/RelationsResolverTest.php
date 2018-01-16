<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */
namespace PhUml\Parser;

use PHPUnit\Framework\TestCase;
use PhUml\Parser\Raw\RawDefinition;
use PhUml\Parser\Raw\RawDefinitions;

class RelationsResolverTest extends TestCase
{
    /** @test */
    function it_does_not_change_the_definitions_if_no_relations_are_declared()
    {
        $definitions = new RawDefinitions();
        $resolver = new RelationsResolver();

        $definitions->addExternalClass('AClass');
        $definitions->addExternalClass('AnotherClass');
        $definitions->addExternalInterface('AnInterface');
        $definitions->addExternalInterface('AnotherInterface');

        $resolver->resolve($definitions);

        $this->assertCount(4, $definitions->all());
    }

    /** @test */
    function it_adds_missing_interfaces()
    {
        $definitions = new RawDefinitions();
        $resolver = new RelationsResolver();

        $definitions->add(RawDefinition::class(['class' => 'AClass', 'implements' => [
            'AnExternalInterface', 'AnExistingInterface',
        ]]));
        $definitions->add(RawDefinition::interface(['interface' => 'AnInterface', 'extends' => 'AnotherExternalInterface']));
        $definitions->add(RawDefinition::interface(['interface' => 'AnExistingInterface']));

        $resolver->resolve($definitions);

        $this->assertCount(5, $definitions->all());
        $this->assertArrayHasKey('AnExternalInterface', $definitions->all());
        $this->assertArrayHasKey('AnotherExternalInterface', $definitions->all());
    }

    /** @test */
    function it_adds_missing_classes()
    {
        $definitions = new RawDefinitions();
        $resolver = new RelationsResolver();

        $definitions->add(RawDefinition::class(['class' => 'AClass', 'extends' => 'AnExternalClass', 'implements' => []]));
        $definitions->add(RawDefinition::class(['class' => 'AnotherClass', 'extends' => 'AnotherExternalClass', 'implements' => []]));

        $resolver->resolve($definitions);

        $this->assertCount(4, $definitions->all());
        $this->assertArrayHasKey('AnExternalClass', $definitions->all());
        $this->assertArrayHasKey('AnotherExternalClass', $definitions->all());
    }
}
