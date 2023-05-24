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

/**
 * Grupo de informação do ICMSST devido para a UF de destino, nas operações
 * interestaduais de produtos que tiveram retenção antecipada de ICMS por
 * ST na UF do remetente. Repasse via Substituto Tributário.
 */
class Substituto extends Cobrado
{
    public function __construct($substituto = [])
    {
        parent::__construct($substituto);
    }

    public function toArray($recursive = false)
    {
        $substituto = parent::toArray($recursive);
        return $substituto;
    }

    public function fromArray($substituto = [])
    {
        if ($substituto instanceof Substituto) {
            $substituto = $substituto->toArray();
        } elseif (!is_array($substituto)) {
            return $this;
        }
        parent::fromArray($substituto);
        $this->setNormal(new Cobrado(isset($substituto['normal']) ? $substituto['normal'] : []));
        if (!isset($substituto['tributacao'])) {
            $this->setTributacao('41');
        }
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $element = parent::getNode($version, $name ?? 'ICMSST');
        $dom = $element->ownerDocument;
        Util::appendNode($element, 'vBCSTDest', $this->getNormal()->getBase(true));
        Util::appendNode($element, 'vICMSSTDest', $this->getNormal()->getValor(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'ICMSST';
        $element = parent::loadNode($element, $name, $version);
        $this->getNormal()->setBase(
            Util::loadNode(
                $element,
                'vBCSTDest',
                'Tag "vBCSTDest" do campo "Normal.Base" não encontrada no Substituto'
            )
        );
        $this->getNormal()->setValor(
            Util::loadNode(
                $element,
                'vICMSSTDest',
                'Tag "vICMSSTDest" do campo "Normal.Valor" não encontrada no Substituto'
            )
        );
        return $element;
    }
}
