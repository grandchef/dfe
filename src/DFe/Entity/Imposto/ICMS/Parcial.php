<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\ICMS;

use DFe\Common\Util;
use DFe\Entity\Imposto\Fundo\Substituido;

/**
 * Tributação pelo ICMS
 * 30 - Isenta ou não tributada e com cobrança do ICMS
 * por substituição tributária, estende de Base
 */
class Parcial extends Base
{
    /**
     * Modalidade de determinação da BC do ICMS ST:
     * 0 – Preço tabelado ou
     * máximo  sugerido;
     * 1 - Lista Negativa (valor);
     * 2 - Lista Positiva
     * (valor);
     * 3 - Lista Neutra (valor);
     * 4 - Margem Valor Agregado (%);
     * 5 -
     * Pauta (valor).
     */
    public const MODALIDADE_TABELADO = 'tabelado';
    public const MODALIDADE_NEGATIVO = 'negativo';
    public const MODALIDADE_POSITIVO = 'positivo';
    public const MODALIDADE_NEUTRO = 'neutro';
    public const MODALIDADE_AGREGADO = 'agregado';
    public const MODALIDADE_PAUTA = 'pauta';

    private $modalidade;
    private $margem;
    private $reducao;

    public function __construct($parcial = [])
    {
        parent::__construct($parcial);
    }

    /**
     * Modalidade de determinação da BC do ICMS ST:
     * 0 – Preço tabelado ou
     * máximo  sugerido;
     * 1 - Lista Negativa (valor);
     * 2 - Lista Positiva
     * (valor);
     * 3 - Lista Neutra (valor);
     * 4 - Margem Valor Agregado (%);
     * 5 -
     * Pauta (valor).
     */
    public function getModalidade($normalize = false)
    {
        if (!$normalize) {
            return $this->modalidade;
        }
        switch ($this->modalidade) {
            case self::MODALIDADE_TABELADO:
                return '0';
            case self::MODALIDADE_NEGATIVO:
                return '1';
            case self::MODALIDADE_POSITIVO:
                return '2';
            case self::MODALIDADE_NEUTRO:
                return '3';
            case self::MODALIDADE_AGREGADO:
                return '4';
            case self::MODALIDADE_PAUTA:
                return '5';
        }
        return $this->modalidade;
    }

    public function setModalidade($modalidade)
    {
        $this->modalidade = $modalidade;
        return $this;
    }

    public function getMargem($normalize = false)
    {
        if (!$normalize) {
            return $this->margem;
        }
        return Util::toFloat($this->margem);
    }

    public function setMargem($margem)
    {
        $this->margem = $margem;
        return $this;
    }

    public function getReducao($normalize = false)
    {
        if (!$normalize) {
            return $this->reducao;
        }
        return Util::toFloat($this->reducao);
    }

    public function setReducao($reducao)
    {
        $this->reducao = $reducao;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $parcial = parent::toArray($recursive);
        $parcial['modalidade'] = $this->getModalidade();
        $parcial['margem'] = $this->getMargem();
        $parcial['reducao'] = $this->getReducao();
        return $parcial;
    }

    public function fromArray($parcial = [])
    {
        if ($parcial instanceof Parcial) {
            $parcial = $parcial->toArray();
        } elseif (!is_array($parcial)) {
            return $this;
        }
        parent::fromArray($parcial);
        $this->setModalidade($parcial['modalidade'] ?? null);
        $this->setMargem($parcial['margem'] ?? null);
        $this->setReducao($parcial['reducao'] ?? null);
        if (!isset($parcial['fundo']) || !($this->getFundo() instanceof Substituido)) {
            $this->setFundo(new Substituido());
        }
        if (!isset($parcial['tributacao'])) {
            $this->setTributacao('30');
        }
        return $this;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'ICMS30');
        Util::appendNode($element, 'orig', $this->getOrigem(true));
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'modBCST', $this->getModalidade(true));
        Util::appendNode($element, 'pMVAST', $this->getMargem(true));
        Util::appendNode($element, 'pRedBCST', $this->getReducao(true));
        Util::appendNode($element, 'vBCST', $this->getBase(true));
        Util::appendNode($element, 'pICMSST', $this->getAliquota(true));
        Util::appendNode($element, 'vICMSST', $this->getValor(true));
        return $this->exportFundo($element);
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'ICMS30';
        $element = Util::findNode($element, $name);
        $this->setOrigem(
            Util::loadNode(
                $element,
                'orig',
                'Tag "orig" do campo "Origem" não encontrada no ICMS Parcial'
            )
        );
        $this->setTributacao(
            Util::loadNode(
                $element,
                'CST',
                'Tag "CST" do campo "Tributacao" não encontrada no ICMS Parcial'
            )
        );
        $this->setModalidade(
            Util::loadNode(
                $element,
                'modBCST',
                'Tag "modBCST" do campo "Modalidade" não encontrada no ICMS Parcial'
            )
        );
        $this->setMargem(
            Util::loadNode(
                $element,
                'pMVAST',
                'Tag "pMVAST" do campo "Margem" não encontrada no ICMS Parcial'
            )
        );
        $this->setReducao(
            Util::loadNode(
                $element,
                'pRedBCST',
                'Tag "pRedBCST" do campo "Reducao" não encontrada no ICMS Parcial'
            )
        );
        $this->setBase(
            Util::loadNode(
                $element,
                'vBCST',
                'Tag "vBCST" do campo "Base" não encontrada no ICMS Parcial'
            )
        );
        $this->setAliquota(
            Util::loadNode(
                $element,
                'pICMSST',
                'Tag "pICMSST" do campo "Aliquota" não encontrada no ICMS Parcial'
            )
        );
        $this->importFundo($element);
        return $element;
    }
}
