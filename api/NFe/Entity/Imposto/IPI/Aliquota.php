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

class Aliquota extends Imposto
{

    /**
     * Código da Situação Tributária do IPI:
     * 00-Entrada com recuperação de
     * crédito
     * 49 - Outras entradas
     * 50-Saída tributada
     * 99-Outras saídas
     */
    const TRIBUTACAO_CREDITO = 'credito';
    const TRIBUTACAO_ENTRADA = 'entrada';
    const TRIBUTACAO_TRIBUTADA = 'tributada';
    const TRIBUTACAO_SAIDA = 'saida';

    private $tributacao;

    public function __construct($aliquota = array())
    {
        parent::__construct($aliquota);
        $this->setGrupo(self::GRUPO_IPI);
    }

    /**
     * Código da Situação Tributária do IPI:
     * 00-Entrada com recuperação de
     * crédito
     * 49 - Outras entradas
     * 50-Saída tributada
     * 99-Outras saídas
     */
    public function getTributacao($normalize = false)
    {
        if (!$normalize) {
            return $this->tributacao;
        }
        switch ($this->tributacao) {
            case self::TRIBUTACAO_CREDITO:
                return '00';
            case self::TRIBUTACAO_ENTRADA:
                return '49';
            case self::TRIBUTACAO_TRIBUTADA:
                return '50';
            case self::TRIBUTACAO_SAIDA:
                return '99';
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
        $aliquota = parent::toArray();
        $aliquota['tributacao'] = $this->getTributacao();
        return $aliquota;
    }

    public function fromArray($aliquota = array())
    {
        if ($aliquota instanceof Aliquota) {
            $aliquota = $aliquota->toArray();
        } elseif (!is_array($aliquota)) {
            return $this;
        }
        parent::fromArray($aliquota);
        if (!isset($aliquota['tributacao']) || is_null($aliquota['tributacao'])) {
            $this->setTributacao(self::TRIBUTACAO_TRIBUTADA);
        } else {
            $this->setTributacao($aliquota['tributacao']);
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name)?'IPITrib':$name);
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'vBC', $this->getBase(true));
        Util::appendNode($element, 'pIPI', $this->getAliquota(true));
        Util::appendNode($element, 'vIPI', $this->getValor(true));
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name)?'IPITrib':$name;
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
        $this->setBase(
            Util::loadNode(
                $element,
                'vBC',
                'Tag "vBC" do campo "Base" não encontrada'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pIPI',
                'Tag "pIPI" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
