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
use DFe\Entity\Imposto\Fundo\Retido;

/**
 * ICMS cobrado anteriormente por substituição tributária (substituído) ou
 * por antecipação
 */
class Cobrado extends Generico
{
    private $valor;

    public function __construct($cobrado = [])
    {
        parent::__construct($cobrado);
    }

    /**
     * Valor base para cálculo do imposto
     */
    public function getBase($normalize = false)
    {
        if (!$normalize) {
            return is_null($this->getValor()) ? 0.00 : parent::getBase($normalize);
        }
        return Util::toCurrency($this->getBase());
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
        $cobrado = parent::toArray($recursive);
        $cobrado['valor'] = $this->getValor();
        return $cobrado;
    }

    public function fromArray($cobrado = [])
    {
        if ($cobrado instanceof Cobrado) {
            $cobrado = $cobrado->toArray();
        } elseif (!is_array($cobrado)) {
            return $this;
        }
        parent::fromArray($cobrado);
        if (isset($cobrado['valor'])) {
            $this->setValor($cobrado['valor']);
        } else {
            $this->setValor(null);
        }
        if (!isset($cobrado['fundo']) || !($this->getFundo() instanceof Retido)) {
            $this->setFundo(new Retido());
        }
        if (!isset($cobrado['tributacao'])) {
            $this->setTributacao('500');
        }
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $element = parent::getNode(is_null($name) ? 'ICMSSN500' : $name);
        $dom = $element->ownerDocument;
        if (is_null($this->getValor())) {
            return $element;
        }
        Util::appendNode($element, 'vBCSTRet', $this->getBase(true));
        Util::appendNode($element, 'vICMSSTRet', $this->getValor(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'ICMSSN500';
        $element = parent::loadNode($element, $name);
        $this->setBase(Util::loadNode($element, 'vBCSTRet'));
        $this->setValor(Util::loadNode($element, 'vICMSSTRet'));
        return $element;
    }
}
