<?php

/**
 * MIT License
 *
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
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

    public function getNode($name = null)
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

    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'ICMSSN500' : $name;
        $element = parent::loadNode($element, $name);
        $this->setBase(Util::loadNode($element, 'vBCSTRet'));
        $this->setValor(Util::loadNode($element, 'vICMSSTRet'));
        return $element;
    }
}
