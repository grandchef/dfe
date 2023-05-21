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
use DFe\Entity\Imposto\Fundo\Base as Fundo;

/**
 * Classe base do ICMS normal, estende de ICMS\Base
 */
class Normal extends Base
{
    public const MODALIDADE_AGREGADO = 'agregado';
    public const MODALIDADE_PAUTA = 'pauta';
    public const MODALIDADE_TABELADO = 'tabelado';
    public const MODALIDADE_OPERACAO = 'operacao';

    private $modalidade;

    public function __construct($normal = [])
    {
        parent::__construct($normal);
    }

    public function getModalidade($normalize = false)
    {
        if (!$normalize) {
            return $this->modalidade;
        }
        switch ($this->modalidade) {
            case self::MODALIDADE_AGREGADO:
                return '0';
            case self::MODALIDADE_PAUTA:
                return '1';
            case self::MODALIDADE_TABELADO:
                return '2';
            case self::MODALIDADE_OPERACAO:
                return '3';
        }
        return $this->modalidade;
    }

    public function setModalidade($modalidade)
    {
        $this->modalidade = $modalidade;
        return $this;
    }

    public function toArray($recursive = false)
    {
        $normal = parent::toArray($recursive);
        $normal['modalidade'] = $this->getModalidade();
        return $normal;
    }

    public function fromArray($normal = [])
    {
        if ($normal instanceof Normal) {
            $normal = $normal->toArray();
        } elseif (!is_array($normal)) {
            return $this;
        }
        parent::fromArray($normal);
        if (isset($normal['modalidade'])) {
            $this->setModalidade($normal['modalidade']);
        } else {
            $this->setModalidade(null);
        }
        if (!isset($normal['fundo']) || !($this->getFundo() instanceof Fundo)) {
            $this->setFundo(new Fundo());
        }
        return $this;
    }

    public function getNode($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement(is_null($name) ? 'ICMS' : $name);
        Util::appendNode($element, 'orig', $this->getOrigem(true));
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'modBC', $this->getModalidade(true));
        Util::appendNode($element, 'vBC', $this->getBase(true));
        Util::appendNode($element, 'pICMS', $this->getAliquota(true));
        Util::appendNode($element, 'vICMS', $this->getValor(true));
        return $this->exportFundo($element);
    }


    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'ICMS' : $name;
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
        $this->setModalidade(
            Util::loadNode(
                $element,
                'modBC',
                'Tag "modBC" do campo "Modalidade" não encontrada'
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
                'pICMS',
                'Tag "pICMS" do campo "Aliquota" não encontrada'
            )
        );
        $this->importFundo($element);
        return $element;
    }
}
