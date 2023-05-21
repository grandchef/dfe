<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\COFINS;

use DFe\Common\Util;
use DFe\Entity\Imposto;

class Generico extends Imposto
{
    private $valor;

    public function __construct($generico = [])
    {
        parent::__construct($generico);
    }

    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return $this->valor;
        }
        return Util::toCurrency($this->valor);
    }

    public function setValor($valor)
    {
        $this->valor = $valor;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $generico = parent::toArray($recursive);
        $generico['valor'] = $this->getValor();
        return $generico;
    }

    public function fromArray($generico = [])
    {
        if ($generico instanceof Generico) {
            $generico = $generico->toArray();
        } elseif (!is_array($generico)) {
            return $this;
        }
        parent::fromArray($generico);
        if (isset($generico['valor'])) {
            $this->setValor($generico['valor']);
        } else {
            $this->setValor(null);
        }
        $this->setGrupo(self::GRUPO_COFINS);
        if (!isset($generico['tributacao'])) {
            $this->setTributacao('99');
        }
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name) ? 'COFINSOutr' : $name);
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'vCOFINS', $this->getValor(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'COFINSOutr';
        $element = Util::findNode($element, $name);
        $this->setTributacao(
            Util::loadNode(
                $element,
                'CST',
                'Tag "CST" do campo "Tributacao" não encontrada'
            )
        );
        $this->setValor(
            Util::loadNode(
                $element,
                'vCOFINS',
                'Tag "vCOFINS" do campo "Valor" não encontrada'
            )
        );
        return $element;
    }
}
