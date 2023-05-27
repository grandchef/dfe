<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\PIS;

use DFe\Common\Util;
use DFe\Entity\Imposto;

class Simples extends Imposto
{
    public function fromArray($simples = [])
    {
        if ($simples instanceof Generico) {
            $simples = $simples->toArray();
        } elseif (!is_array($simples)) {
            return $this;
        }
        parent::fromArray($simples);
        $this->setGrupo(self::GRUPO_PIS);
        $this->setTributacao($simples['tributacao'] ?? '49');
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'PISSN');
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $element = Util::findNode($element, $name ?? 'PISSN');
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
