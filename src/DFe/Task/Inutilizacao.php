<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Task;

use DFe\Core\Nota;
use DFe\Core\SEFAZ;
use DFe\Common\Util;
use DFe\Core\NFe;
use DFe\Util\AdapterInterface;
use DFe\Util\XmlseclibsAdapter;
use DFe\Exception\ValidationException;

class Inutilizacao extends Retorno
{
    private $id;
    private $ano;
    private $cnpj;
    private $modelo;
    private $serie;
    private $inicio;
    private $final;
    private $justificativa;
    private $numero;

    public function __construct($inutilizacao = [])
    {
        parent::__construct($inutilizacao);
    }

    /**
     * Formado por:
     * ID = Literal
     * 43 = Código Estado
     * 15 = Ano
     *
     * 00000000000000 = CNPJ
     * 55 = Modelo
     * 001 = Série
     * 000000411 =
     * Número Inicial
     * 000000411 = Número Final
     */
    public function getID($normalize = false)
    {
        if (!$normalize) {
            return $this->id;
        }
        return 'ID' . $this->id;
    }

    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getAno($normalize = false)
    {
        if (!$normalize) {
            return $this->ano;
        }
        return $this->ano % 100;
    }

    public function setAno($ano)
    {
        $this->ano = $ano;
        return $this;
    }

    public function getCNPJ($normalize = false)
    {
        if (!$normalize) {
            return $this->cnpj;
        }
        return $this->cnpj;
    }

    public function setCNPJ($cnpj)
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 65 = NFC-e.
     * @param boolean $normalize informa se o modelo deve estar no formato do XML
     * @return mixed modelo do Envio
     */
    public function getModelo($normalize = false)
    {
        if (!$normalize) {
            return $this->modelo;
        }
        switch ($this->modelo) {
            case Nota::MODELO_NFE:
                return '55';
            case Nota::MODELO_NFCE:
                return '65';
        }
        return $this->modelo;
    }

    /**
     * Altera o valor do Modelo para o informado no parâmetro
     * @param mixed $modelo novo valor para Modelo
     * @return self A própria instância da classe
     */
    public function setModelo($modelo)
    {
        switch ($modelo) {
            case '55':
                $modelo = Nota::MODELO_NFE;
                break;
            case '65':
                $modelo = Nota::MODELO_NFCE;
                break;
        }
        $this->modelo = $modelo;
        return $this;
    }

    public function getSerie($normalize = false)
    {
        if (!$normalize) {
            return $this->serie;
        }
        return $this->serie;
    }

    public function setSerie($serie)
    {
        $this->serie = $serie;
        return $this;
    }

    public function getInicio($normalize = false)
    {
        if (!$normalize) {
            return $this->inicio;
        }
        return $this->inicio;
    }

    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
        return $this;
    }

    public function getFinal($normalize = false)
    {
        if (!$normalize) {
            return $this->final;
        }
        return $this->final;
    }

    public function setFinal($final)
    {
        $this->final = $final;
        return $this;
    }

    public function getJustificativa($normalize = false)
    {
        if (!$normalize) {
            return $this->justificativa;
        }
        return $this->justificativa;
    }

    public function setJustificativa($justificativa)
    {
        $this->justificativa = $justificativa;
        return $this;
    }

    public function getNumero($normalize = false)
    {
        if (!$normalize) {
            return $this->numero;
        }
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * Informa se os números foram inutilizados
     */
    public function isInutilizado()
    {
        return in_array($this->getStatus(), ['102', '563']);
    }

    public function toArray($recursive = false)
    {
        $inutilizacao = parent::toArray($recursive);
        $inutilizacao['id'] = $this->getID();
        $inutilizacao['ano'] = $this->getAno();
        $inutilizacao['cnpj'] = $this->getCNPJ();
        $inutilizacao['modelo'] = $this->getModelo();
        $inutilizacao['serie'] = $this->getSerie();
        $inutilizacao['inicio'] = $this->getInicio();
        $inutilizacao['final'] = $this->getFinal();
        $inutilizacao['justificativa'] = $this->getJustificativa();
        $inutilizacao['numero'] = $this->getNumero();
        return $inutilizacao;
    }

    public function fromArray($inutilizacao = [])
    {
        if ($inutilizacao instanceof Inutilizacao) {
            $inutilizacao = $inutilizacao->toArray();
        } elseif (!is_array($inutilizacao)) {
            return $this;
        }
        parent::fromArray($inutilizacao);
        $this->setID($inutilizacao['id'] ?? null);
        $this->setAno($inutilizacao['ano'] ?? null);
        $this->setCNPJ($inutilizacao['cnpj'] ?? null);
        $this->setModelo($inutilizacao['modelo'] ?? null);
        $this->setSerie($inutilizacao['serie'] ?? null);
        $this->setInicio($inutilizacao['inicio'] ?? null);
        $this->setFinal($inutilizacao['final'] ?? null);
        $this->setJustificativa($inutilizacao['justificativa'] ?? null);
        $this->setNumero($inutilizacao['numero'] ?? null);
        return $this;
    }

    public function gerarID()
    {
        $id = sprintf(
            '%02d%02d%s%02d%03d%09d%09d',
            $this->getUF(true),
            $this->getAno(true), // 2 dígitos
            $this->getCNPJ(true),
            $this->getModelo(true),
            $this->getSerie(true),
            $this->getInicio(true),
            $this->getFinal(true)
        );
        return $id;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $this->setID($this->gerarID());

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'inutNFe');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', NFe::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = NFe::VERSAO;
        $element->appendChild($versao);

        $info = $dom->createElement('infInut');
        $id = $dom->createAttribute('Id');
        $id->value = $this->getID(true);
        $info->appendChild($id);

        Util::appendNode($info, 'tpAmb', $this->getAmbiente(true));
        Util::appendNode($info, 'xServ', 'INUTILIZAR');
        Util::appendNode($info, 'cUF', $this->getUF(true));
        Util::appendNode($info, 'ano', $this->getAno(true));
        Util::appendNode($info, 'CNPJ', $this->getCNPJ(true));
        Util::appendNode($info, 'mod', $this->getModelo(true));
        Util::appendNode($info, 'serie', $this->getSerie(true));
        Util::appendNode($info, 'nNFIni', $this->getInicio(true));
        Util::appendNode($info, 'nNFFin', $this->getFinal(true));
        Util::appendNode($info, 'xJust', $this->getJustificativa(true));
        $element->appendChild($info);
        $dom->appendChild($element);
        return $element;
    }

    public function getReturnNode()
    {
        $outros = parent::getNode('', 'infInut');
        $element = $this->getNode('', 'retInutNFe');
        $dom = $element->ownerDocument;
        /** @var \DOMElement */
        $info = $dom->getElementsByTagName('infInut')->item(0);
        $info->removeAttribute('Id');
        $remove_tags = ['tpAmb', 'xServ', 'xJust'];
        foreach ($remove_tags as $key) {
            $node = $info->getElementsByTagName($key)->item(0);
            $info->removeChild($node);
        }
        $uf = $info->getElementsByTagName('cUF')->item(0);
        foreach ($outros->childNodes as $node) {
            $node = $dom->importNode($node, true);
            $list = $info->getElementsByTagName($node->nodeName);
            if ($list->length == 1) {
                continue;
            }
            switch ($node->nodeName) {
                case 'dhRecbto':
                    $info->appendChild($node);
                    break;
                default:
                    $info->insertBefore($node, $uf);
            }
        }
        Util::appendNode($info, 'nProt', $this->getNumero(true));
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'infInut';
        $element = parent::loadNode($element, $name, $version);
        if (!$this->isInutilizado()) {
            return $element;
        }
        $this->setAno(Util::loadNode($element, 'ano'));
        $this->setCNPJ(Util::loadNode($element, 'CNPJ'));
        $this->setModelo(Util::loadNode($element, 'mod'));
        $this->setSerie(Util::loadNode($element, 'serie'));
        $this->setInicio(Util::loadNode($element, 'nNFIni'));
        $this->setFinal(Util::loadNode($element, 'nNFFin'));
        $this->setNumero(Util::loadNode($element, 'nProt'));
        return $element;
    }

    public function envia($dom)
    {
        $envio = new Envio();
        $envio->setServico(Envio::SERVICO_INUTILIZACAO);
        $envio->setAmbiente($this->getAmbiente());
        $envio->setModelo($this->getModelo());
        $envio->setEmissao(Nota::EMISSAO_NORMAL);
        $this->setVersao($envio->getVersao());
        $dom = $this->validar($dom);
        $envio->setConteudo($dom);
        $resp = $envio->envia();
        $this->loadNode($resp->documentElement);
        if (!$this->isInutilizado()) {
            throw new \Exception($this->getMotivo(), $this->getStatus());
        }
        return $this->getReturnNode()->ownerDocument;
    }

    /**
     * Assina o XML com a assinatura eletrônica do tipo A1
     */
    public function assinar($dom = null)
    {
        if (is_null($dom)) {
            $xml = $this->getNode();
            $dom = $xml->ownerDocument;
        }
        $config = SEFAZ::getInstance()->getConfiguracao();
        $config->verificaValidadeCertificado();

        $adapter = new XmlseclibsAdapter();
        $adapter->setPrivateKey($config->getCertificado()->getChavePrivada());
        $adapter->setPublicKey($config->getCertificado()->getChavePublica());
        $adapter->addTransform(AdapterInterface::ENVELOPED);
        $adapter->addTransform(AdapterInterface::XML_C14N);
        $adapter->sign($dom, 'infInut');
        return $dom;
    }

    /**
     * Valida o documento após assinar
     */
    public function validar($dom)
    {
        $dom->loadXML($dom->saveXML());
        $xsd_path = dirname(__DIR__) . '/Core/schema';
        $xsd_file = $xsd_path . '/NFe/v4.0.0/inutNFe_v' . $this->getVersao() . '.xsd';
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
