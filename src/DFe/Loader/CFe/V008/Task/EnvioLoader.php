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
use DFe\Task\Envio as TaskEnvio;

/**
 * Envia requisições para os servidores da SEFAZ
 */
class EnvioLoader implements Loader
{
    public function __construct(private TaskEnvio $envio) {}

    /**
     * Cria um nó XML do envio de acordo com o leiaute da NFe
     *
     * @param  string $name Nome do nó que será criado
     */
    public function getNode(?string $name = null): \DOMElement
    {
        if ($this->envio->getConteudo() instanceof \DOMDocument) {
            $dom = $this->envio->getConteudo();
        } else {
            $xml = $this->envio->getConteudo();
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->loadXML($xml);
        }
        return $dom->documentElement;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        return $element;
    }
}
