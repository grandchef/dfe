<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\CFe\Task;

use DFe\Common\Loader;
use DFe\Task\Evento;

class EventoLoader implements Loader
{
    public function __construct(private Evento $evento)
    {
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        return $this->evento->getDocumento()->documentElement;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $this->evento->setDocumento($element->ownerDocument);
        $this->evento->setInformacao(new Evento());
        $this->evento->getInformacao()->setStatus('135');
        $this->evento->getInformacao()->setMotivo('Evento registrado e vinculado a CF-e');
        return $element;
    }
}
