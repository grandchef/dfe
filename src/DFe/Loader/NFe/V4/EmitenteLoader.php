<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\NFe\V4;

use DFe\Common\Util;
use DFe\Common\Loader;
use DFe\Entity\Emitente;

/**
 * Empresa que irá emitir as notas fiscais
 */
class EmitenteLoader implements Loader
{
    public function __construct(private Emitente $emitente)
    {
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'emit');
        Util::appendNode($element, 'CNPJ', $this->emitente->getCNPJ(true));
        Util::appendNode($element, 'xNome', $this->emitente->getRazaoSocial(true));
        if (!is_null($this->emitente->getFantasia())) {
            Util::appendNode($element, 'xFant', $this->emitente->getFantasia(true));
        }
        $endereco = $this->emitente->getEndereco()->getNode($version, 'enderEmit');
        $endereco = $dom->importNode($endereco, true);
        if (!is_null($this->emitente->getTelefone())) {
            Util::appendNode($endereco, 'fone', $this->emitente->getTelefone(true));
        }
        $element->appendChild($endereco);
        Util::appendNode($element, 'IE', $this->emitente->getIE(true));
        if (!is_null($this->emitente->getIM())) {
            Util::appendNode($element, 'IM', $this->emitente->getIM(true));
        }
        Util::appendNode($element, 'CRT', $this->emitente->getRegime(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $loader = new PessoaLoader($this->emitente);
        $element = $loader->loadNode($element, $name ?? 'emit', $version);
        $this->emitente->setFantasia(Util::loadNode($element, 'xFant'));
        $this->emitente->setRegime(
            Util::loadNode(
                $element,
                'CRT',
                'Tag "CRT" do campo "Regime" não encontrada'
            )
        );
        return $element;
    }
}
