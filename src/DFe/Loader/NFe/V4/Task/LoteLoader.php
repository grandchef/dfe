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
use DFe\Core\SEFAZ;
use DFe\Common\Util;
use DFe\Task\Status;
use DFe\Common\Loader;
use DFe\Exception\ValidationException;

class LoteLoader implements Loader
{
    public function __construct(private \DOMDocument $dom) {}

    public function getNode(?string $name = null): \DOMElement
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $dob = new \DOMDocument('1.0', 'UTF-8');
        $envio = $dob->createElement('enviNFe');
        $envio->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', Nota::PORTAL);
        $versao = $dob->createAttribute('versao');
        $versao->value = Nota::VERSAO;
        $envio->appendChild($versao);
        Util::appendNode($envio, 'idLote', Status::genLote());
        Util::appendNode($envio, 'indSinc', $config->getSincrono(true));
        Util::appendNode($envio, 'NFe', 0);
        $dob->appendChild($envio);
        $xml = $dob->saveXML($dob->documentElement);
        $xml_content = str_replace('<NFe>0</NFe>', $this->dom->saveXML($this->dom->documentElement), $xml);
        $dom_lote = $this->validar($xml_content);
        return $dom_lote->documentElement;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        return $element;
    }

    /**
     * Valida o XML em lote
     */
    public function validar($xml_content)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml_content);
        $xsd_path = dirname(dirname(dirname(dirname(__DIR__)))) . '/Core/schema';
        $xsd_file = $xsd_path . '/NFe/v4.0.0/enviNFe_v4.00.xsd';
        if (!file_exists($xsd_file)) {
            throw new \Exception(sprintf('O arquivo "%s" de esquema XSD não existe!', $xsd_file), 404);
        }
        // Enable user error handling
        $save = libxml_use_internal_errors(true);
        if ($dom->schemaValidate($xsd_file)) {
            libxml_use_internal_errors($save);
            return $dom;
        }
        $msg = [];
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            $msg[] = 'Não foi possível validar o XML: ' . $error->message;
        }
        libxml_clear_errors();
        libxml_use_internal_errors($save);
        throw new ValidationException($msg);
    }
}
