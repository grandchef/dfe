<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\IPI;

use DFe\Common\Util;
use DFe\Entity\Imposto;

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
    public const TRIBUTACAO_CREDITO = 'credito';
    public const TRIBUTACAO_ENTRADA = 'entrada';
    public const TRIBUTACAO_TRIBUTADA = 'tributada';
    public const TRIBUTACAO_SAIDA = 'saida';

    public function __construct($aliquota = [])
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
     * @param boolean $normalize informa se a tributacao deve estar no formato do XML
     * @return mixed tributacao da Aliquota
     */
    public function getTributacao($normalize = false)
    {
        if (!$normalize) {
            return parent::getTributacao($normalize);
        }
        switch (parent::getTributacao()) {
            case self::TRIBUTACAO_CREDITO:
                return '00';
            case self::TRIBUTACAO_ENTRADA:
                return '49';
            case self::TRIBUTACAO_TRIBUTADA:
                return '50';
            case self::TRIBUTACAO_SAIDA:
                return '99';
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
            case '00':
                $tributacao = self::TRIBUTACAO_CREDITO;
                break;
            case '49':
                $tributacao = self::TRIBUTACAO_ENTRADA;
                break;
            case '50':
                $tributacao = self::TRIBUTACAO_TRIBUTADA;
                break;
            case '99':
                $tributacao = self::TRIBUTACAO_SAIDA;
                break;
        }
        return parent::setTributacao($tributacao);
    }

    public function toArray($recursive = false)
    {
        $aliquota = parent::toArray($recursive);
        return $aliquota;
    }

    public function fromArray($aliquota = [])
    {
        if ($aliquota instanceof Aliquota) {
            $aliquota = $aliquota->toArray();
        } elseif (!is_array($aliquota)) {
            return $this;
        }
        parent::fromArray($aliquota);
        if (is_null($this->getTributacao())) {
            $this->setTributacao(self::TRIBUTACAO_TRIBUTADA);
        }
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'IPITrib');
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'vBC', $this->getBase(true));
        Util::appendNode($element, 'pIPI', $this->getAliquota(true));
        Util::appendNode($element, 'vIPI', $this->getValor(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'IPITrib';
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
                'pIPI',
                'Tag "pIPI" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
