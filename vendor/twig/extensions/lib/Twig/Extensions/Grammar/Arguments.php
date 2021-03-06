<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please Views the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @deprecated since version 1.5
 */
class Twig_Extensions_Grammar_Arguments extends Twig_Extensions_Grammar
{
    public function __toString()
    {
        return sprintf('<%s:arguments>', $this->name);
    }

    public function parse(Twig_Token $token)
    {
        return $this->parser->getExpressionParser()->parseArguments();
    }
}
