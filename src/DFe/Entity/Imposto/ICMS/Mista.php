<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\ICMS;

use DFe\Common\Util;

/**
 * Tributação pelo ICMS
 * 70 - Com redução de base de cálculo e cobrança do
 * ICMS por substituição tributária, estende de Cobranca
 */
class Mista extends Cobranca
{
    public function __construct($mista = [])
    {
        parent::__construct($mista);
    }

    public function toArray($recursive = false)
    {
        $mista = parent::toArray($recursive);
        return $mista;
    }

    public function fromArray($mista = [])
    {
        if ($mista instanceof Mista) {
            $mista = $mista->toArray();
        } elseif (!is_array($mista)) {
            return $this;
        }
        parent::fromArray($mista);
        $this->setNormal(new Reducao(isset($mista['normal']) ? $mista['normal'] : []));
        if (!isset($mista['tributacao'])) {
            $this->setTributacao('70');
        }
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $element = parent::getNode(is_null($name) ? 'ICMS70' : $name);
        $dom = $element->ownerDocument;
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $normal = new Reducao();
        $this->setNormal($normal);
        $name ??= 'ICMS70';
        $element = parent::loadNode($element, $name);
        if (is_null($this->getNormal()->getReducao())) {
            $this->getNormal()->setReducao($this->getReducao());
        }
        if (is_null($this->getNormal()->getAliquota())) {
            $this->getNormal()->setAliquota($this->getAliquota());
        }
        if (!is_null($this->getNormal()->getBase())) {
            return $element;
        }
        $valor = floatval(
            Util::loadNode(
                $element,
                'vICMSST',
                'Tag "vICMSST" do campo "Normal.Valor" não encontrada na Mista'
            )
        );
        $diferenca = $this->getValor() - $valor;
        $base = $diferenca * 100.0 / $this->getNormal()->getAliquota();
        $this->getNormal()->setBase($base);
        return $element;
    }
}
