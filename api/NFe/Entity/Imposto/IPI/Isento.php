<?php
/**
 * MIT License
 *
 * Copyright (c) 2016 MZ Desenvolvimento de Sistemas LTDA
 *
 * @author Francimar Alves <mazinsw@gmail.com>
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
namespace NFe\Entity\Imposto\IPI;

use NFe\Common\Util;
use NFe\Entity\Imposto;

/**
 * IPI não tributado
 */
class Isento extends Imposto
{

    private $tributacao;

    public function __construct($isento = array())
    {
        parent::__construct($isento);
        $this->setGrupo(self::GRUPO_IPI);
    }

    /**
     * Código da Situação Tributária do IPI:
     * 01-Entrada tributada com alíquota
     * zero
     * 02-Entrada isenta
     * 03-Entrada não-tributada
     * 04-Entrada
     * imune
     * 05-Entrada com suspensão
     * 51-Saída tributada com alíquota
     * zero
     * 52-Saída isenta
     * 53-Saída não-tributada
     * 54-Saída imune
     * 55-Saída com
     * suspensão
     */
    public function getTributacao($normalize = false)
    {
        if (!$normalize) {
            return $this->tributacao;
        }
        return $this->tributacao;
    }

    public function setTributacao($tributacao)
    {
        $this->tributacao = $tributacao;
        return $this;
    }

    public function toArray()
    {
        $isento = parent::toArray();
        $isento['tributacao'] = $this->getTributacao();
        return $isento;
    }

    public function fromArray($isento = array())
    {
        if ($isento instanceof Isento) {
            $isento = $isento->toArray();
        } elseif (!is_array($isento)) {
            return $this;
        }
        parent::fromArray($isento);
        if (!isset($isento['tributacao']) || is_null($isento['tributacao'])) {
            $this->setTributacao('01');
        } else {
            $this->setTributacao($isento['tributacao']);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'IPINT':$name);
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'IPINT':$name;
        if ($element->tagName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "'.$name.'" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
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
