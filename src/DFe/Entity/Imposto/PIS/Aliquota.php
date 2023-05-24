<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\PIS;

use DFe\Common\Util;
use DFe\Entity\Imposto;

class Aliquota extends Imposto
{
    public const TRIBUTACAO_NORMAL = 'normal';
    public const TRIBUTACAO_DIFERENCIADA = 'diferenciada';

    public function __construct($pis = [])
    {
        parent::__construct($pis);
        $this->setGrupo(self::GRUPO_PIS);
    }

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

    public function toArray($recursive = false)
    {
        $pis = parent::toArray($recursive);
        return $pis;
    }

    public function fromArray($pis = [])
    {
        if ($pis instanceof Aliquota) {
            $pis = $pis->toArray();
        } elseif (!is_array($pis)) {
            return $this;
        }
        parent::fromArray($pis);
        if (is_null($this->getTributacao())) {
            $this->setTributacao(self::TRIBUTACAO_NORMAL);
        }
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'PISAliq');
        Util::appendNode($element, 'CST', $this->getTributacao(true));
        Util::appendNode($element, 'vBC', $this->getBase(true));
        Util::appendNode($element, 'pPIS', $this->getAliquota(true));
        Util::appendNode($element, 'vPIS', $this->getValor(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'PISAliq';
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
                'pPIS',
                'Tag "pPIS" do campo "Aliquota" não encontrada'
            )
        );
        return $element;
    }
}
