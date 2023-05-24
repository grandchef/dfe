<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\COFINS;

use DFe\Common\Util;
use DFe\Entity\Imposto;

class Aliquota extends Imposto
{
    public const TRIBUTACAO_NORMAL = 'normal';
    public const TRIBUTACAO_DIFERENCIADA = 'diferenciada';

    public function __construct($cofins = [])
    {
        parent::__construct($cofins);
        $this->setGrupo(self::GRUPO_COFINS);
    }

    /**
     * Código de Situação Tributária do COFINS.
     * 01 – Operação Tributável -
     * Base de Cálculo = Valor da Operação Alíquota Normal (Cumulativo/Não
     * Cumulativo);
     * 02 - Operação Tributável - Base de Calculo = Valor da
     * Operação (Alíquota Diferenciada);
     * @param boolean $normalize informa se a tributacao deve estar no formato do XML
     * @return mixed tributacao da Aliquota
     */
    public function getTributacao($normalize = false)
    {
        if (!$normalize) {
            return parent::getTributacao();
        }
        switch (parent::getTributacao()) {
            case self::TRIBUTACAO_NORMAL:
                return '01';
            case self::TRIBUTACAO_DIFERENCIADA:
                return '02';
        }
        return parent::getTributacao($normalize);
    }

    /**
     * Altera o valor da Tributacao para o informado no parâmetro
     * @param mixed $tributacao novo valor para Tributacao
     * @return self A própria instância da classe
     */
    public function setTributacao($tributacao)
    {
        switch ($tributacao) {
            case '01':
                $tributacao = self::TRIBUTACAO_NORMAL;
                break;
            case '02':
                $tributacao = self::TRIBUTACAO_DIFERENCIADA;
                break;
        }
        return parent::setTributacao($tributacao);
    }

    public function toArray($recursive = false)
    {
        $cofins = parent::toArray($recursive);
        return $cofins;
    }

    public function fromArray($cofins = [])
    {
        if ($cofins instanceof Aliquota) {
            $cofins = $cofins->toArray();
        } elseif (!is_array($cofins)) {
            return $this;
        }
        parent::fromArray($cofins);
        if (is_null($this->getTributacao())) {
            $this->setTributacao(self::TRIBUTACAO_NORMAL);
        }
        return $this;
    }

    public function getNode(?string $name = null, ?string $version = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'COFINSAliq');
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'vBC', $this->getBase(true));
        Util::appendNode($element, 'pCOFINS', $this->getAliquota(true));
        Util::appendNode($element, 'vCOFINS', $this->getValor(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'COFINSAliq';
        $element = Util::findNode($element, $name);
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
                'pCOFINS',
                'Tag "pCOFINS" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
