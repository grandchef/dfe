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

namespace DFe\Entity\Imposto\ICMS;

use DFe\Common\Util;
use DFe\Entity\Imposto\Fundo\Retido;

/**
 * Tributação pelo ICMS
 * 60 - ICMS cobrado anteriormente por substituição
 * tributária
 */
class Cobrado extends Generico
{
    private $valor;

    public function __construct($cobrado = [])
    {
        parent::__construct($cobrado);
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
            $this->setTributacao('60');
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $element = parent::getNode(is_null($name) ? 'ICMS60' : $name);
        $dom = $element->ownerDocument;
        Util::appendNode($element, 'vBCSTRet', $this->getBase(true));
        Util::appendNode($element, 'pST', $this->getAliquota(true));
        Util::appendNode($element, 'vICMSSTRet', $this->getValor(true));
        return $this->exportFundo($element);
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'ICMS60' : $name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "' . $name . '" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
        $this->setOrigem(
            Util::loadNode(
                $element,
                'orig',
                'Tag "orig" do campo "Origem" não encontrada'
            )
        );
        $this->setTributacao(
            Util::loadNode(
                $element,
                'CST',
                'Tag "CST" do campo "Tributacao" não encontrada'
            )
        );
        $this->setBase(
            Util::loadNode(
                $element,
                'vBCSTRet',
                'Tag "vBCSTRet" do campo "Base" não encontrada'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pST'
            )
        );
        $this->setValor(
            Util::loadNode(
                $element,
                'vICMSSTRet',
                'Tag "vICMSSTRet" do campo "Valor" não encontrada'
            )
        );
        $this->importFundo($element);
        return $element;
    }
}
