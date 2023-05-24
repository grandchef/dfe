<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\NFe\V4\Task;

use DFe\Core\Nota;
use DFe\Common\Util;
use DFe\Common\Loader;
use DFe\Task\Envio as TaskEnvio;

/**
 * Envia requisições para os servidores da SEFAZ
 */
class EnvioLoader implements Loader
{
    public function __construct(private TaskEnvio $envio)
    {
    }

    /**
     * Tipo de serviço a ser executado
     *
     * @return mixed servico do Envio
     */
    public function getServico()
    {
        $url = $this->envio->getServiceInfo();
        if (is_array($url) && isset($url['servico'])) {
            return Nota::PORTAL . '/wsdl/' . $url['servico'];
        }
        throw new \Exception('A ação do serviço "' . $this->envio->getServico() . '" não foi configurada', 404);
    }

    /**
     * Cria um nó XML do envio de acordo com o leiaute da NFe
     *
     * @param  string $name Nome do nó que será criado
     */
    public function getNode(?string $name = null, ?string $version = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'nfeDadosMsg');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', $this->getServico());
        // Corrige xmlns:default
        // $data = $dom->importNode($this->getConteudo()->documentElement, true);
        // $element->appendChild($data);
        Util::appendNode($element, 'Conteudo', 0);

        $dom->appendChild($element);

        // Corrige xmlns:default
        // return $dom;
        if ($this->envio->getConteudo() instanceof \DOMDocument) {
            $xml = $this->envio->getConteudo()->saveXML($this->envio->getConteudo()->documentElement);
        } else {
            $xml = $this->envio->getConteudo();
        }
        $xml = str_replace('<Conteudo>0</Conteudo>', $xml, $dom->saveXML($dom->documentElement));
        $dom->loadXML($xml);
        return $dom->documentElement;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        return $element;
    }
}
