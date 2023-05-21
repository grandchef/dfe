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

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name) ? 'vTotTrib' : $name, $this->getTotal(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'vTotTrib' : $name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "' . $name . '" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setBase($element->nodeValue);
        $this->setAliquota(100);
        return $element;
    }
}
