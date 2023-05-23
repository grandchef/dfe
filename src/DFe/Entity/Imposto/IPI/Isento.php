<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\IPI;

use DFe\Common\Util;
use DFe\Entity\Imposto;

/**
 * IPI não tributado
 */
class Isento extends Imposto
{
    public function __construct($isento = [])
    {
        parent::__construct($isento);
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
            $this->setTributacao('01');
        }
        $this->setGrupo(self::GRUPO_IPI);
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'IPINT');
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'IPINT';
        $element = Util::findNode($element, $name);
        $this->setTributacao(
            Util::loadNode(
                $element,
                'CST',
                'Tag "CST" do campo "Tributacao" não encontrada'
            )
        );
        return $element;
    }
}
