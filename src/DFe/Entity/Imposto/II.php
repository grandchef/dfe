<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto;

use DFe\Common\Util;
use DFe\Entity\Imposto;

/**
 * Funcionalidade para gerar as informações do II do item de produto da
 * NF-e. Este grupo só precisa ser informado em uma operação de importação
 * que tenha incidência de II.
 */
class II extends Imposto
{
    private $despesas;
    private $valor;
    private $iof;

    public function __construct($ii = [])
    {
        parent::__construct($ii);
        $this->setGrupo(self::GRUPO_II);
    }

    /**
     * Informar o valor das despesas aduaneiras
     */
    public function getDespesas($normalize = false)
    {
        if (!$normalize) {
            return $this->despesas;
        }
        return Util::toCurrency($this->despesas);
    }

    public function setDespesas($despesas)
    {
        $this->despesas = $despesas;
        return $this;
    }

    /**
     * Informar a o valor do Imposto de Importação
     */
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

    /**
     * Informar o Valor do IOF - Imposto sobre Operações Financeiras
     */
    public function getIOF($normalize = false)
    {
        if (!$normalize) {
            return $this->iof;
        }
        return $this->iof;
    }

    public function setIOF($iof)
    {
        $this->iof = $iof;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $ii = parent::toArray($recursive);
        $ii['despesas'] = $this->getDespesas();
        $ii['valor'] = $this->getValor();
        $ii['iof'] = $this->getIOF();
        return $ii;
    }

    public function fromArray($ii = [])
    {
        if ($ii instanceof II) {
            $ii = $ii->toArray();
        } elseif (!is_array($ii)) {
            return $this;
        }
        parent::fromArray($ii);
        $this->setDespesas($ii['despesas'] ?? null);
        $this->setValor($ii['valor'] ?? null);
        $this->setIOF($ii['iof'] ?? null);
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'II');
        Util::appendNode($element, 'vBC', $this->getBase(true));
        Util::appendNode($element, 'vDespAdu', $this->getDespesas(true));
        Util::appendNode($element, 'vII', $this->getValor(true));
        Util::appendNode($element, 'vIOF', $this->getIOF(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'II';
        $element = Util::findNode($element, $name);
        $this->setBase(
            Util::loadNode(
                $element,
                'vBC',
                'Tag "vBC" do campo "Base" não encontrada'
            )
        );
        $this->setDespesas(
            Util::loadNode(
                $element,
                'vDespAdu',
                'Tag "vDespAdu" do campo "Despesas" não encontrada'
            )
        );
        $this->setValor(
            Util::loadNode(
                $element,
                'vII',
                'Tag "vII" do campo "Valor" não encontrada'
            )
        );
        $this->setIOF(
            Util::loadNode(
                $element,
                'vIOF',
                'Tag "vIOF" do campo "Iof" não encontrada'
            )
        );
        return $element;
    }
}
