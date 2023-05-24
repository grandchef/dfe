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
        $this->setValor($generico['valor'] ?? null);
        $this->setGrupo(self::GRUPO_PIS);
        if (!isset($generico['tributacao'])) {
            $this->setTributacao('99');
        }
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'PISOutr');
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'vPIS', $this->getValor(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'PISOutr';
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
                'vPIS',
                'Tag "vPIS" do campo "Valor" não encontrada'
            )
        );
        return $element;
    }
}
