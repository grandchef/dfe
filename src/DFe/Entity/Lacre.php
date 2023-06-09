<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity;

use DFe\Common\Node;
use DFe\Common\Util;

/**
 * Lacre do volume
 */
class Lacre implements Node
{
    private $numero;

    public function __construct($lacre = [])
    {
        $this->fromArray($lacre);
    }

    /**
     * Número do lacre
     */
    public function getNumero($normalize = false)
    {
        if (!$normalize) {
            return $this->numero;
        }
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $numero = intval($numero);
        $this->numero = $numero;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $lacre = [];
        $lacre['numero'] = $this->getNumero();
        return $lacre;
    }

    public function fromArray($lacre = [])
    {
        if ($lacre instanceof Lacre) {
            $lacre = $lacre->toArray();
        } elseif (!is_array($lacre)) {
            return $this;
        }
        $this->setNumero($lacre['numero'] ?? null);
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'lacres');
        Util::appendNode($element, 'nLacre', $this->getNumero(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'lacres';
        $element = Util::findNode($element, $name);
        $this->setNumero(
            Util::loadNode(
                $element,
                'nLacre',
                'Tag "nLacre" do campo "Numero" não encontrada'
            )
        );
        return $element;
    }
}
