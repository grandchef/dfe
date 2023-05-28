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

use DOMElement;
use DFe\Core\NFe;
use DFe\Core\SEFAZ;
use DFe\Common\Util;
use DFe\Task\Evento;
use DFe\Common\Loader;
use DFe\Common\Enveloper;
use DFe\Util\AdapterInterface;
use DFe\Util\XmlseclibsAdapter;
use DFe\Exception\ValidationException;

class EventoLoader implements Loader, Enveloper
{
    public const VERSAO = '1.00';

    public const TAG_RETORNO = 'retEvento';
    public const TAG_RETORNO_ENVIO = 'retEnvEvento';

    public function __construct(private Evento $evento)
    {
    }

    public function getID()
    {
        return 'ID' . $this->evento->getID();
    }

    /**
     * Código do órgão de recepção do Evento. Utilizar a Tabela do IBGE
     * extendida, utilizar 91 para identificar o Ambiente Nacional
     */
    public function getOrgao()
    {
        if (is_numeric($this->evento->getOrgao())) {
            return $this->evento->getOrgao();
        }

        $db = SEFAZ::getInstance()->getConfiguracao()->getBanco();
        return $db->getCodigoOrgao($this->evento->getOrgao());
    }

    /**
     * Data e Hora do Evento, formato UTC (AAAA-MM-DDThh:mm:ssTZD, onde TZD =
     * +hh:mm ou -hh:mm)
     */
    public function getData()
    {
        return Util::toDateTime($this->evento->getData());
    }

    public function setData($data)
    {
        if (!is_numeric($data)) {
            $data = strtotime($data ?? '');
        }
        $this->evento->setData($data);
        return $this;
    }

    /**
     * Gera o ID, a regra de formação do Id é: "ID" +
     * tpEvento +  chave da NF-e + nSeqEvento
     */
    public function gerarID()
    {
        $id = sprintf(
            '%s%s%02d',
            $this->evento->getTipo(),
            $this->evento->getChave(),
            $this->evento->getSequencia()
        );
        return $id;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        if ($name === 'infEvento') {
            return $this->getReturnNode($version);
        }
        $this->evento->setID($this->gerarID());

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'evento');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NFe::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = self::VERSAO;
        $element->appendChild($versao);

        $info = $dom->createElement('infEvento');
        $dom = $element->ownerDocument;
        $id = $dom->createAttribute('Id');
        $id->value = $this->getID();
        $info->appendChild($id);

        Util::appendNode($info, 'cOrgao', $this->getOrgao());
        Util::appendNode($info, 'tpAmb', (new StatusLoader($this->evento))->getAmbiente());
        if ($this->evento->isCNPJ()) {
            Util::appendNode($info, 'CNPJ', $this->evento->getIdentificador());
        } else {
            Util::appendNode($info, 'CPF', $this->evento->getIdentificador());
        }
        Util::appendNode($info, 'chNFe', $this->evento->getChave());
        Util::appendNode($info, 'dhEvento', $this->getData());
        Util::appendNode($info, 'tpEvento', $this->evento->getTipo());
        Util::appendNode($info, 'nSeqEvento', $this->evento->getSequencia());
        Util::appendNode($info, 'verEvento', self::VERSAO);

        $detalhes = $dom->createElement('detEvento');
        $versao = $dom->createAttribute('versao');
        $versao->value = self::VERSAO;
        $detalhes->appendChild($versao);

        Util::appendNode($detalhes, 'descEvento', $this->evento->getDescricao());
        Util::appendNode($detalhes, 'nProt', $this->evento->getNumero());
        Util::appendNode($detalhes, 'xJust', $this->evento->getJustificativa());
        $info->appendChild($detalhes);

        $element->appendChild($info);
        $dom->appendChild($element);
        if (is_null($name)) {
            // geração de XML para envio
            $dom = $this->assinar($dom);
            $dom = $this->validar($dom);
            $this->evento->setDocumento($dom);
            $dom = $this->envelope($dom);
        }
        return $dom->documentElement;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        if ($name === self::TAG_RETORNO) {
            return $this->loadReturnNode($element, self::TAG_RETORNO);
        }
        if ($name === '') {
            $resultElement = (new RetornoLoader($this->evento))->loadNode($element, self::TAG_RETORNO_ENVIO, $version);
            $this->evento->setOrgao(
                Util::loadNode(
                    $resultElement,
                    'cOrgao',
                    'Tag "cOrgao" não encontrada no Evento'
                )
            );
            if (!$this->evento->isProcessado()) {
                throw new \Exception($this->evento->getMotivo(), $this->evento->getStatus());
            }
        }
        if ($name === '') {
            $informacao = new Evento();
            $informacao->loadNode($element, self::TAG_RETORNO, $version);
            $this->evento->setInformacao($informacao);
            $this->evento->setDocumento($this->addInformacao($this->evento->getDocumento(), $version));
            return $element;
        }
        $root = $element;
        $element = Util::findNode($element, 'evento');
        $element = Util::findNode($element, $name ?? 'infEvento');
        $this->evento->setOrgao(
            Util::loadNode(
                $element,
                'cOrgao',
                'Tag "cOrgao" não encontrada no Evento'
            )
        );
        (new StatusLoader($this->evento))->setAmbiente(
            Util::loadNode(
                $element,
                'tpAmb',
                'Tag "tpAmb" não encontrada no Evento'
            )
        );
        if (Util::nodeExists($element, 'CNPJ')) {
            $this->evento->setIdentificador(
                Util::loadNode(
                    $element,
                    'CNPJ',
                    'Tag "CNPJ" não encontrada no Evento'
                )
            );
        } else {
            $this->evento->setIdentificador(
                Util::loadNode(
                    $element,
                    'CPF',
                    'Tag "CPF" não encontrada no Evento'
                )
            );
        }
        $this->evento->setChave(
            Util::loadNode(
                $element,
                'chNFe',
                'Tag "chNFe" não encontrada no Evento'
            )
        );
        $this->setData(
            Util::loadNode(
                $element,
                'dhEvento',
                'Tag "dhEvento" não encontrada no Evento'
            )
        );
        $this->evento->setTipo(
            Util::loadNode(
                $element,
                'tpEvento',
                'Tag "tpEvento" não encontrada no Evento'
            )
        );
        $this->evento->setSequencia(
            Util::loadNode(
                $element,
                'nSeqEvento',
                'Tag "nSeqEvento" não encontrada no Evento'
            )
        );

        $detalhes = Util::findNode($element, 'detEvento');
        $this->evento->setDescricao(
            Util::loadNode(
                $detalhes,
                'descEvento',
                'Tag "descEvento" não encontrada no Evento'
            )
        );
        $this->evento->setNumero(
            Util::loadNode(
                $detalhes,
                'nProt',
                'Tag "nProt" não encontrada no Evento'
            )
        );
        $this->evento->setJustificativa(
            Util::loadNode(
                $detalhes,
                'xJust',
                'Tag "xJust" não encontrada no Evento'
            )
        );
        $informacao = null;
        if (Util::nodeExists($root, 'procEventoNFe')) {
            $informacao = new Evento();
            $informacao->loadNode($root, self::TAG_RETORNO, $version);
        }
        $this->evento->setInformacao($informacao);
        if (!is_null($informacao)) {
            $this->getNode($version);
            $this->addInformacao($this->evento->getDocumento(), $version);
        }
        return $element;
    }

    private function getReturnNode(string $version)
    {
        $outros = (new RetornoLoader($this->evento))->getNode($version, 'infEvento');
        $element = $this->evento->getNode($version, self::TAG_RETORNO);
        $dom = $element->ownerDocument;
        $element->removeAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns');
        /** @var DOMElement */
        $info = $dom->getElementsByTagName('infEvento')->item(0);
        $info->removeAttribute('Id');
        $remove_tags = ['detEvento', 'verEvento', 'dhEvento', 'CNPJ', 'CPF', 'cOrgao'];
        foreach ($remove_tags as $key) {
            $_fields = $info->getElementsByTagName($key);
            if ($_fields->length == 0) {
                continue;
            }
            $node = $_fields->item(0);
            $info->removeChild($node);
        }
        $chave = $info->getElementsByTagName('chNFe')->item(0);
        foreach ($outros->childNodes as $node) {
            $node = $dom->importNode($node, true);
            $list = $info->getElementsByTagName($node->nodeName);
            if ($list->length == 1) {
                continue;
            }
            $info->insertBefore($node, $chave);
        }
        $status = $info->getElementsByTagName('cStat')->item(0);
        Util::appendNode($info, 'cOrgao', $this->getOrgao(), $status);
        $sequencia = $info->getElementsByTagName('nSeqEvento')->item(0);
        Util::appendNode($info, 'xEvento', $this->evento->getDescricao(), $sequencia);
        if (!is_null($this->evento->getIdentificador())) {
            if ($this->evento->isCNPJ()) {
                Util::appendNode($info, 'CNPJDest', $this->evento->getIdentificador());
            } else {
                Util::appendNode($info, 'CPFDest', $this->evento->getIdentificador());
            }
        }
        if (!is_null($this->evento->getEmail())) {
            Util::appendNode($info, 'emailDest', $this->evento->getEmail());
        }
        Util::appendNode($info, 'dhRegEvento', $this->getData());
        Util::appendNode($info, 'nProt', $this->evento->getNumero());
        return $element;
    }

    private function loadReturnNode(\DOMElement $element, $name = null, string $version = '')
    {
        $element = Util::findNode($element, self::TAG_RETORNO);
        $element = (new RetornoLoader($this->evento))->loadNode($element, $name ?? 'infEvento', $version);
        $this->evento->setOrgao(
            Util::loadNode(
                $element,
                'cOrgao',
                'Tag "cOrgao" do campo "Orgao" não encontrada'
            )
        );
        $this->evento->setChave(Util::loadNode($element, 'chNFe'));
        $this->evento->setTipo(Util::loadNode($element, 'tpEvento'));
        $this->evento->setDescricao(Util::loadNode($element, 'xEvento'));
        $this->evento->setSequencia(Util::loadNode($element, 'nSeqEvento'));
        if ($element->getElementsByTagName('CNPJDest')->length > 0) {
            $this->evento->setIdentificador(Util::loadNode($element, 'CNPJDest'));
        } else {
            $this->evento->setIdentificador(Util::loadNode($element, 'CPFDest'));
        }
        $this->evento->setEmail(Util::loadNode($element, 'emailDest'));
        $this->setData(
            Util::loadNode(
                $element,
                'dhRegEvento',
                'Tag "dhRegEvento" do campo "Data" não encontrada'
            )
        );
        $this->evento->setNumero(Util::loadNode($element, 'nProt'));
        return $element;
    }

    public function envelope(\DOMDocument $dom): \DOMDocument
    {
        $dob = new \DOMDocument('1.0', 'UTF-8');
        $envio = $dob->createElement('envEvento');
        $envio->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NFe::PORTAL);
        $versao = $dob->createAttribute('versao');
        $versao->value = self::VERSAO;
        $envio->appendChild($versao);
        Util::appendNode($envio, 'idLote', StatusLoader::genLote());
        Util::appendNode($envio, 'evento', 0);
        $dob->appendChild($envio);
        $xml = $dob->saveXML($dob->documentElement);
        $xml = str_replace('<evento>0</evento>', $dom->saveXML($dom->documentElement), $xml);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml);
        return $dom;
    }

    /**
     * Adiciona a informação no XML do evento
     */
    private function addInformacao($dom, string $version): \DOMDocument
    {
        if (is_null($this->evento->getInformacao())) {
            throw new \Exception('A informação não foi informado no evento "' . $this->evento->getID() . '"', 404);
        }
        $evento = $dom->getElementsByTagName('evento')->item(0);
        // Corrige xmlns:default
        $evento_xml = $dom->saveXML($evento);

        $element = $dom->createElement('procEventoNFe');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NFe::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = self::VERSAO;
        $element->appendChild($versao);
        $dom->removeChild($evento);
        // Corrige xmlns:default
        $evento = $dom->createElement('evento', 0);

        $element->appendChild($evento);
        $info = $this->evento->getInformacao()->getNode($version, 'infEvento');
        $info = $dom->importNode($info, true);
        $element->appendChild($info);
        $dom->appendChild($element);
        // Corrige xmlns:default
        $xml = $dom->saveXML();
        $xml = str_replace('<evento>0</evento>', $evento_xml, $xml);
        $dom->loadXML($xml);

        return $dom;
    }

    /**
     * Assina o XML com a assinatura eletrônica do tipo A1
     */
    private function assinar(\DOMDocument $dom)
    {
        $config = SEFAZ::getInstance()->getConfiguracao();
        $config->verificaValidadeCertificado();

        $adapter = new XmlseclibsAdapter();
        $adapter->setPrivateKey($config->getCertificado()->getChavePrivada());
        $adapter->setPublicKey($config->getCertificado()->getChavePublica());
        $adapter->addTransform(AdapterInterface::ENVELOPED);
        $adapter->addTransform(AdapterInterface::XML_C14N);
        $adapter->sign($dom, 'infEvento');
        return $dom;
    }

    /**
     * Valida o documento após assinar
     */
    private function validar($dom)
    {
        $dom->loadXML($dom->saveXML());
        $xsd_path = dirname(dirname(dirname(__DIR__))) . '/Core/schema';
        $xsd_file = $xsd_path . '/NFe/Cancelamento/v1.0.0/eventoCancNFe_v1.00.xsd';
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
