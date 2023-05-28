<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\CFe;

use DFe\Common\Util;
use DFe\Common\Loader;
use DFe\Entity\Endereco;

/**
 * Informação de endereço que será informado nos clientes e no emitente
 */
class EnderecoLoader implements Loader
{
    public function __construct(private Endereco $endereco)
    {
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'entrega');
        Util::appendNode($element, 'xLgr', $this->endereco->getLogradouro(true));
        Util::appendNode($element, 'nro', $this->endereco->getNumero(true));
        if (!empty($this->endereco->getComplemento())) {
            Util::appendNode($element, 'xCpl', $this->endereco->getComplemento(true));
        }
        Util::appendNode($element, 'xBairro', $this->endereco->getBairro(true));
        Util::appendNode($element, 'xMun', $this->endereco->getMunicipio()->getNome(true));
        Util::appendNode($element, 'UF', $this->endereco->getMunicipio()->getEstado()->getUF(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $element = Util::findNode($element, $name ?? 'enderEmit');
        $this->endereco->setLogradouro(
            Util::loadNode(
                $element,
                'xLgr',
                'Tag "xLgr" do campo "Logradouro" não encontrada'
            )
        );
        $this->endereco->setNumero(
            Util::loadNode(
                $element,
                'nro',
                'Tag "nro" do campo "Numero" não encontrada'
            )
        );
        $this->endereco->setComplemento(Util::loadNode($element, 'xCpl'));
        $this->endereco->setBairro(
            Util::loadNode(
                $element,
                'xBairro',
                'Tag "xBairro" do campo "Bairro" não encontrada'
            )
        );
        $this->endereco->getMunicipio()->setNome(
            Util::loadNode(
                $element,
                'xMun',
                'Tag "xMun" do objeto "Municipio" não encontrada'
            )
        );
        $this->endereco->setCEP(
            Util::loadNode(
                $element,
                'CEP',
                'Tag "CEP" do campo "CEP" não encontrada'
            )
        );
        return $element;
    }
}
