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

class Isento extends Imposto
{
    /**
     * Código de Situação Tributária do COFINS:
     * 04 - Operação Tributável -
     * Tributação Monofásica - (Alíquota Zero);
     * 05 - Operação Tributável
     * (ST);
     * 06 - Operação Tributável - Alíquota Zero;
     * 07 - Operação Isenta da
     * contribuição;
     * 08 - Operação Sem Incidência da contribuição;
     * 09 -
     * Operação com suspensão da contribuição;
     */
    public const TRIBUTACAO_MONOFASICA = 'monofasica';
    public const TRIBUTACAO_ST = 'st';
    public const TRIBUTACAO_ZERO = 'zero';
    public const TRIBUTACAO_ISENTA = 'isenta';
    public const TRIBUTACAO_INCIDENCIA = 'incidencia';
    public const TRIBUTACAO_SUSPENSAO = 'suspensao';

    public function __construct($cofins = [])
    {
        parent::__construct($cofins);
        $this->setGrupo(self::GRUPO_COFINS);
        $this->setBase(0.00);
        $this->setAliquota(0.0);
    }

    /**
     * Código de Situação Tributária do COFINS:
     * 04 - Operação Tributável -
     * Tributação Monofásica - (Alíquota Zero);
     * 05 - Operação Tributável
     * (ST);
     * 06 - Operação Tributável - Alíquota Zero;
     * 07 - Operação Isenta da
     * contribuição;
     * 08 - Operação Sem Incidência da contribuição;
     * 09 -
     * Operação com suspensão da contribuição;
     * @param boolean $normalize informa se a tributacao deve estar no formato do XML
     * @return mixed tributacao do Isento
     */
    public function getTributacao($normalize = false)
    {
        if (!$normalize) {
            return parent::getTributacao();
        }
        switch (parent::getTributacao()) {
            case self::TRIBUTACAO_MONOFASICA:
                return '04';
            case self::TRIBUTACAO_ST:
                return '05';
            case self::TRIBUTACAO_ZERO:
                return '06';
            case self::TRIBUTACAO_ISENTA:
                return '07';
            case self::TRIBUTACAO_INCIDENCIA:
                return '08';
            case self::TRIBUTACAO_SUSPENSAO:
                return '09';
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
            case '04':
                $tributacao = self::TRIBUTACAO_MONOFASICA;
                break;
            case '05':
                $tributacao = self::TRIBUTACAO_ST;
                break;
            case '06':
                $tributacao = self::TRIBUTACAO_ZERO;
                break;
            case '07':
                $tributacao = self::TRIBUTACAO_ISENTA;
                break;
            case '08':
                $tributacao = self::TRIBUTACAO_INCIDENCIA;
                break;
            case '09':
                $tributacao = self::TRIBUTACAO_SUSPENSAO;
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
        if ($cofins instanceof Isento) {
            $cofins = $cofins->toArray();
        } elseif (!is_array($cofins)) {
            return $this;
        }
        parent::fromArray($cofins);
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name) ? 'COFINSNT' : $name);
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'COFINSNT';
        $element = Util::findNode($element, $name);
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
