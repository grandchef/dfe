<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\CFe\V008\Task;

use DFe\Common\Loader;

class LoteLoader implements Loader
{
    public function __construct(private \DOMDocument $dom) {}

    public function getNode(?string $name = null): \DOMElement
    {
        return $this->dom->documentElement;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        return $element;
    }
}
