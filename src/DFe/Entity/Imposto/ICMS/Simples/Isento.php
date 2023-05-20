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

/**
 * Tributada pelo Simples Nacional sem permissão de crédito
 */
class Isento extends Generico
{
    public function __construct($isento = [])
    {
        parent::__construct($isento);
    }

    /**
     * Valor base para cálculo do imposto
     */
    public function getBase($normalize = false)
    {
        if (!$normalize) {
            return 0.00; // sempre zero
        }
        return Util::toCurrency($this->getBase());
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
            $this->setTributacao('102');
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $element = parent::getNode(is_null($name) ? 'ICMSSN102' : $name);
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'ICMSSN102' : $name;
        $element = parent::loadNode($element, $name);
        return $element;
    }
}
