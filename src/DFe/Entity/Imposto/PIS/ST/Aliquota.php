<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity\Imposto\PIS\ST;

use DFe\Common\Util;

/**
 * Este grupo só deve ser informado se o produto for sujeito a PIS por ST,
 * CST = 05, a informação deste grupo não desobriga a informação do grupo
 * PIS.
 */
class Aliquota extends \DFe\Entity\Imposto\PIS\Aliquota
{
    public function __construct($aliquota = [])
    {
        parent::__construct($aliquota);
        $this->setGrupo(self::GRUPO_PISST);
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
        return $this;
    }

    public function getNode(?string $name = null, ?string $version = null): \DOMElement
    {
        $element = parent::getNode($name ?? 'PISST');
        $item = $element->getElementsByTagName('CST')->item(0);
        $element->removeChild($item);
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'PISST';
        $element = Util::findNode($element, $name);
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
