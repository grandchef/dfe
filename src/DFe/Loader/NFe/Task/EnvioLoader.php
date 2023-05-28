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

use DFe\Core\NFe;
use DFe\Task\Envio;
use DFe\Common\Util;
use DFe\Common\Loader;

/**
 * Envia requisições para os servidores da SEFAZ
 */
class EnvioLoader implements Loader
{
    public function __construct(private Envio $envio)
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
            return NFe::PORTAL . '/wsdl/' . $url['servico'];
        }
        throw new \Exception('A ação do serviço "' . $this->envio->getServico() . '" não foi configurada', 404);
    }

    /**
     * Cria um nó XML do envio de acordo com o leiaute da NFe
     *
     * @param  string $name Nome do nó que será criado
     */
    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'nfeDadosMsg');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', $this->getServico());
        Util::appendNode($element, 'Conteudo', 0);

        $dom->appendChild($element);

        if ($this->envio->getConteudo() instanceof \DOMDocument) {
            $xml = $this->envio->getConteudo()->saveXML($this->envio->getConteudo()->documentElement);
        } else {
            $xml = $this->envio->getConteudo();
        }
        $xml = str_replace('<Conteudo>0</Conteudo>', $xml, $dom->saveXML($dom->documentElement));
        $dom->loadXML($xml);
        return $dom->documentElement;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        return $element;
    }
}
