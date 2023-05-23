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
 * 10 - Tributada e com cobrança do ICMS por
 * substituição tributária, estende de Parcial
 */
class Cobranca extends Parcial
{
    private $normal;

    public function __construct($cobranca = [])
    {
        parent::__construct($cobranca);
    }

    public function getNormal()
    {
        return $this->normal;
    }

    public function setNormal($normal)
    {
        $this->normal = $normal;
        return $this;
    }

    /**
     * Calcula o valor do imposto com base na aliquota e valor base
     */
    public function getValor($normalize = false)
    {
        if (!$normalize) {
            return ($this->getBase() * $this->getAliquota()) / 100.0 - $this->getNormal()->getValor();
        }
        $valor = $this->getValor();
        return Util::toCurrency($valor);
    }

    /**
     * Obtém o valor total do imposto
     */
    public function getTotal($normalize = false)
    {
        return $this->getNormal()->getValor($normalize);
    }

    public function toArray($recursive = false)
    {
        $cobranca = parent::toArray($recursive);
        if (!is_null($this->getNormal()) && $recursive) {
            $cobranca['normal'] = $this->getNormal()->toArray($recursive);
        } else {
            $cobranca['normal'] = $this->getNormal();
        }
        return $cobranca;
    }

    public function fromArray($cobranca = [])
    {
        if ($cobranca instanceof Cobranca) {
            $cobranca = $cobranca->toArray();
        } elseif (!is_array($cobranca)) {
            return $this;
        }
        parent::fromArray($cobranca);
        $this->setNormal(new Normal(isset($cobranca['normal']) ? $cobranca['normal'] : []));
        if (!isset($cobranca['tributacao'])) {
            $this->setTributacao('10');
        }
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $element = $this->getNormal()->getNode($name ?? 'ICMS10');
        if (is_null($this->getModalidade())) {
            return $element;
        }
        $dom = $element->ownerDocument;
        $parcial = parent::getNode($name ?? 'ICMS10');
        if (is_null($this->getNormal()->getModalidade())) {
            return $parcial;
        }
        return Util::mergeNodes($element, $parcial);
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'ICMS10';
        $element = Util::findNode($element, $name);
        $normal = $this->getNormal();
        if (is_null($normal)) {
            $normal = new Normal();
        }
        $this->setNormal($normal);
        $_fields = $element->getElementsByTagName('modBCST');
        if ($_fields->length == 0) {
            $normal->loadNode($element, $name);
            return $element;
        }
        $element = parent::loadNode($element, $name);
        $_fields = $element->getElementsByTagName('modBC');
        if ($_fields->length == 0) {
            return $element;
        }
        $normal->loadNode($element, $name);
        return $element;
    }
}
