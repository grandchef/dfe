<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\NFe\Task;

use DFe\Common\Util;
use DFe\Common\Loader;
use DFe\Task\Retorno;

class RetornoLoader implements Loader
{
    public function __construct(private Retorno $retorno)
    {
    }

    public function getDataRecebimento()
    {
        return Util::toDateTime($this->retorno->getDataRecebimento());
    }

    public function setDataRecebimento($data_recebimento)
    {
        if (!is_null($data_recebimento) && !is_numeric($data_recebimento)) {
            $data_recebimento = strtotime($data_recebimento);
        }
        $this->retorno->setDataRecebimento($data_recebimento);
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $element = (new StatusLoader($this->retorno))->getNode($version, $name);
        $status = $element->getElementsByTagName('cStat')->item(0);
        if (!is_null($this->retorno->getDataRecebimento())) {
            Util::appendNode($element, 'dhRecbto', $this->getDataRecebimento(), $status);
        }
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $retorno = (new StatusLoader($this->retorno))->loadNode($element, $name ?? 'Retorno', $version);
        $this->setDataRecebimento(Util::loadNode($retorno, 'dhRecbto'));
        return $retorno;
    }
}
