<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\ICMS\Simples;

use DFe\Common\Util;

/**
 * Tributada pelo Simples Nacional sem permissão de crédito
 */
class Isento extends Generico
{
    public function __construct($isento = [])
    {
        parent::__construct($isento);
    }

    /**
     * Valor base para cálculo do imposto
     */
    public function getBase($normalize = false)
    {
        if (!$normalize) {
            return 0.00; // sempre zero
        }
        return Util::toCurrency($this->getBase());
    }

    public function toArray($recursive = false)
    {
        $isento = parent::toArray($recursive);
        return $isento;
    }

    public function fromArray($isento = [])
    {
        if ($isento instanceof Isento) {
            $isento = $isento->toArray();
        } elseif (!is_array($isento)) {
            return $this;
        }
        parent::fromArray($isento);
        if (!isset($isento['tributacao'])) {
            $this->setTributacao('102');
        }
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $element = parent::getNode($version, $name ?? 'ICMSSN102');
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'ICMSSN102';
        $element = parent::loadNode($element, $name);
        return $element;
    }
}
