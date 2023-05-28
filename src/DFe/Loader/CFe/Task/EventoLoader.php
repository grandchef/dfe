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
use DFe\Common\Util;
use DFe\Task\Evento;

class EventoLoader implements Loader
{
    public function __construct(private Evento $evento)
    {
    }

    public function getID()
    {
        return 'CFe' . $this->evento->getChave();
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'CFeCanc');

        $info = $dom->createElement('infCFe');
        $dom = $element->ownerDocument;
        $id = $dom->createAttribute('chCanc');
        $id->value = $this->getID();
        $info->appendChild($id);

        $ident = $dom->createElement('ide');
        Util::appendNode($ident, 'CNPJ', $this->evento->getResponsavel()->getCNPJ());
        Util::appendNode($ident, 'signAC', $this->evento->getResponsavel()->getAssinatura());
        Util::appendNode($ident, 'numeroCaixa', $this->evento->getCaixa()->getNumero());
        $info->appendChild($ident);

        $info->appendChild($dom->createElement('emit'));
        $info->appendChild($dom->createElement('dest'));
        $info->appendChild($dom->createElement('total'));

        $element->appendChild($info);
        $dom->appendChild($element);
        return $dom->documentElement;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $element = Util::getNode($element, 'CFeCanc');
        $info = Util::getNode($element, 'infCFe');
        $this->evento->setID(substr($info->getAttribute('Id'), 3));
        $this->evento->setChave(substr($info->getAttribute('chCanc'), 3));
        $this->evento->setDocumento($element->ownerDocument);
        $this->evento->setInformacao(new Evento());
        $this->evento->getInformacao()->setStatus('135');
        $this->evento->getInformacao()->setID($this->evento->getID());
        $this->evento->getInformacao()->setChave($this->evento->getChave());
        $this->evento->getInformacao()->setMotivo('Evento registrado e vinculado a CF-e');
        return $element;
    }
}
