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
use DFe\Task\Autorizacao;

class AutorizacaoLoader implements Loader
{
    public function __construct(private Autorizacao $autorizacao)
    {
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        return $this->autorizacao->getDocument()->documentElement;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $this->autorizacao->setDocument($element->ownerDocument);
        $this->autorizacao->setStatus('100');
        $this->autorizacao->setMotivo('Autorizado o uso da CF-e');
        $this->autorizacao->getNota()->loadNode($element);
        return $element;
    }
}
