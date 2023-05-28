<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\NFe;

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

    public function checkCodigos()
    {
        $this->endereco->getMunicipio()->checkCodigos();
        $this->endereco->getMunicipio()->getEstado()->checkCodigos();
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $this->checkCodigos();
        $element = $dom->createElement($name ?? 'enderEmit');
        Util::appendNode($element, 'xLgr', $this->endereco->getLogradouro(true));
        Util::appendNode($element, 'nro', $this->endereco->getNumero(true));
        if (!empty($this->endereco->getComplemento())) {
            Util::appendNode($element, 'xCpl', $this->endereco->getComplemento(true));
        }
        Util::appendNode($element, 'xBairro', $this->endereco->getBairro(true));
        Util::appendNode($element, 'cMun', $this->endereco->getMunicipio()->getCodigo(true));
        Util::appendNode($element, 'xMun', $this->endereco->getMunicipio()->getNome(true));
        Util::appendNode($element, 'UF', $this->endereco->getMunicipio()->getEstado()->getUF(true));
        Util::appendNode($element, 'CEP', $this->endereco->getCEP(true));
        Util::appendNode($element, 'cPais', $this->endereco->getPais()->getCodigo(true));
        Util::appendNode($element, 'xPais', $this->endereco->getPais()->getNome(true));
        // Util::appendNode($element, 'fone', $this->endereco->getTelefone(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'enderEmit';
        $element = Util::findNode($element, $name);
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
        $this->endereco->getMunicipio()->setCodigo(
            Util::loadNode(
                $element,
                'cMun',
                'Tag "cMun" do objeto "Municipio" não encontrada'
            )
        );
        $this->endereco->getMunicipio()->setNome(
            Util::loadNode(
                $element,
                'xMun',
                'Tag "xMun" do objeto "Municipio" não encontrada'
            )
        );
        $this->endereco->getMunicipio()->getEstado()->setUF(
            Util::loadNode(
                $element,
                'UF',
                'Tag "UF" do objeto "Estado" não encontrada'
            )
        );
        $this->endereco->setCEP(
            Util::loadNode(
                $element,
                'CEP',
                'Tag "CEP" do campo "CEP" não encontrada'
            )
        );
        $this->endereco->getPais()->setCodigo(
            Util::loadNode(
                $element,
                'cPais',
                'Tag "cPais" do objeto "Pais" não encontrada'
            )
        );
        $this->endereco->getPais()->setNome(
            Util::loadNode(
                $element,
                'xPais',
                'Tag "xPais" do objeto "Pais" não encontrada'
            )
        );
        return $element;
    }
}
