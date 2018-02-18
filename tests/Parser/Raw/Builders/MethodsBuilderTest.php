<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace PhUml\Parser\Raw\Builders;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPUnit\Framework\TestCase;
use PhUml\Parser\Raw\Builders\Filters\PrivateMembersFilter;
use PhUml\Parser\Raw\Builders\Filters\ProtectedMembersFilter;

class MethodsBuilderTest extends TestCase
{
    /** @test */
    function it_filters_private_methods()
    {
        $methodsBuilder = new MethodsBuilder([new PrivateMembersFilter()]);

        $methods = $methodsBuilder->build($this->methods);

        $this->assertCount(4, $methods);
        $this->assertTrue($methods[1]->isPublic());
        $this->assertTrue($methods[2]->isPublic());
        $this->assertTrue($methods[4]->isProtected());
        $this->assertTrue($methods[5]->isProtected());
    }

    /** @test */
    function it_excludes_protected_methods()
    {
        $builder = new MethodsBuilder([new ProtectedMembersFilter()]);

        $methods = $builder->build($this->methods);

        $this->assertCount(5, $methods);
        $this->assertTrue($methods[0]->isPrivate());
        $this->assertTrue($methods[1]->isPublic());
        $this->assertTrue($methods[2]->isPublic());
        $this->assertTrue($methods[3]->isPrivate());
        $this->assertTrue($methods[6]->isPrivate());
    }

    /** @test */
    function it_excludes_both_protected_and_private_methods()
    {
        $builder = new MethodsBuilder([new PrivateMembersFilter(), new ProtectedMembersFilter()]);

        $methods = $builder->build($this->methods);

        $this->assertCount(2, $methods);
        $this->assertTrue($methods[1]->isPublic());
        $this->assertTrue($methods[2]->isPublic());
    }

    /** @before */
    function createMethods()
    {
        $this->methods = [
            new ClassMethod('privateMethodA', ['type' => Class_::MODIFIER_PRIVATE]),
            new ClassMethod('publicMethodA', ['type' => Class_::MODIFIER_PUBLIC]),
            new ClassMethod('publicMethodA', ['type' => Class_::MODIFIER_PUBLIC]),
            new ClassMethod('privateMethodB', ['type' => Class_::MODIFIER_PRIVATE]),
            new ClassMethod('protectedMethodA', ['type' => Class_::MODIFIER_PROTECTED]),
            new ClassMethod('protectedMethodB', ['type' => Class_::MODIFIER_PROTECTED]),
            new ClassMethod('privateMethodC', ['type' => Class_::MODIFIER_PRIVATE]),
        ];
    }

    /** @var ClassMethod[] */
    private $methods;
}
