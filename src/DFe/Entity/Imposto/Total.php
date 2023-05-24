<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto;

use DFe\Common\Util;
use DFe\Entity\Imposto;

class Total extends Imposto
{
    public function __construct($total = [])
    {
        parent::__construct($total);
    }

    public function toArray($recursive = false)
    {
        $total = parent::toArray($recursive);
        return $total;
    }

    public function fromArray($total = [])
    {
        if ($total instanceof Total) {
            $total = $total->toArray();
        } elseif (!is_array($total)) {
            return $this;
        }
        parent::fromArray($total);
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'vTotTrib', $this->getTotal(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'vTotTrib';
        $element = Util::findNode($element, $name);
        $this->setBase($element->nodeValue);
        $this->setAliquota(100);
        return $element;
    }
}
