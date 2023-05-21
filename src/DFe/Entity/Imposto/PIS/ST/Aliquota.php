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

    public function getNode($name = null)
    {
        $element = parent::getNode(is_null($name) ? 'PISST' : $name);
        $item = $element->getElementsByTagName('CST')->item(0);
        $element->removeChild($item);
        return $element;
    }

    public function loadNode($element, $name = null)
    {
        $name = is_null($name) ? 'PISST' : $name;
        if ($element->nodeName != $name) {
            $_fields = $element->getElementsByTagName($name);
            if ($_fields->length == 0) {
                throw new \Exception('Tag "' . $name . '" não encontrada', 404);
            }
            $element = $_fields->item(0);
        }
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
