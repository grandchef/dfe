<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Common;

interface Loader
{
    public function getNode(string $version = '', ?string $name = null): \DOMElement;
    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement;
}
