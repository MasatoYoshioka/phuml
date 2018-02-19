<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace PhUml\Parser\Raw;

use PhpParser\NodeTraverser;
use PhUml\Code\Codebase;
use PhUml\Parser\Raw\Builders\RawClassBuilder;
use PhUml\Parser\Raw\Builders\RawInterfaceBuilder;
use PhUml\Parser\Raw\Visitors\ClassVisitor;
use PhUml\Parser\Raw\Visitors\InterfaceVisitor;

class Php5Traverser extends PhpTraverser
{
    public function __construct(RawClassBuilder $rawClassBuilder, RawInterfaceBuilder $rawInterfaceBuilder)
    {
        $this->codebase = new Codebase();
        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new ClassVisitor($rawClassBuilder, $this->codebase));
        $this->traverser->addVisitor(new InterfaceVisitor($rawInterfaceBuilder, $this->codebase));
    }
}
