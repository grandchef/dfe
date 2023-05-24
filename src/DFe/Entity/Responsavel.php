<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Entity;

use DOMElement;
use DFe\Common\Util;
use DFe\Common\Node;

/**
 * Grupo de informações do responsável técnico pelo sistema
 */
class Responsavel extends Pessoa implements Node
{
    /**
     * Informar o nome da pessoa a ser contatada na empresa desenvolvedora do
     * sistema utilizado na emissão do documento fiscal eletrônico.
     */
    private $contato;

    /**
     * Email da software house'
     *
     * @var string
     */
    private $email;

    /**
     * Identificador do CSRT utilizado para montar o hash do CSRT
     */
    private $identificador;

    /**
     * O hashCSRT é o resultado da função hash (SHA-1 – Base64) do CSRT
     * fornecido pelo fisco mais a Chave de Acesso da NFe.
     */
    private $assinatura;

    /**
     * Constroi uma instância de Responsavel vazia
     * @param  array $responsavel Array contendo dados do Responsavel
     */
    public function __construct($responsavel = [])
    {
        $this->fromArray($responsavel);
    }

    /**
     * Informar o nome da pessoa a ser contatada na empresa desenvolvedora do
     * sistema utilizado na emissão do documento fiscal eletrônico.
     * @param boolean $normalize informa se o contato deve estar no formato do XML
     * @return mixed contato do Responsavel
     */
    public function getContato($normalize = false)
    {
        if (!$normalize) {
            return $this->contato;
        }
        return $this->contato;
    }

    /**
     * Altera o valor do Contato para o informado no parâmetro
     * @param mixed $contato novo valor para Contato
     * @return self A própria instância da classe
     */
    public function setContato($contato)
    {
        $this->contato = $contato;
        return $this;
    }

    public function getEmail($normalize = false)
    {
        if (!$normalize) {
            return $this->email;
        }
        return $this->email;
    }

    /**
     * Altera o valor da Email para o informado no parâmetro
     * @param mixed $email novo valor para Email
     * @return self A própria instância da classe
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Identificador do CSRT utilizado para montar o hash do CSRT
     * @param boolean $normalize informa se a identificador deve estar no formato do XML
     * @return mixed identificador do Responsavel
     */
    public function getIdentificador($normalize = false)
    {
        if (!$normalize) {
            return $this->identificador;
        }
        return $this->identificador;
    }

    /**
     * Altera o valor da Identificador para o informado no parâmetro
     * @param mixed $identificador novo valor para Identificador
     * @return self A própria instância da classe
     */
    public function setIdentificador($identificador)
    {
        if (!empty($identificador)) {
            $identificador = intval($identificador);
        }
        $this->identificador = $identificador;
        return $this;
    }

    /**
     * O hashCSRT é o resultado da função hash (SHA-1 – Base64) do CSRT
     * fornecido pelo fisco mais a Chave de Acesso da NFe.
     * @param boolean $normalize informa se a assinatura deve estar no formato do XML
     * @return mixed assinatura do Responsavel
     */
    public function getAssinatura($normalize = false)
    {
        if (!$normalize) {
            return $this->assinatura;
        }
        return $this->assinatura;
    }

    /**
     * Altera o valor da Assinatura para o informado no parâmetro
     * @param mixed $assinatura novo valor para Assinatura
     * @return self A própria instância da classe
     */
    public function setAssinatura($assinatura)
    {
        $this->assinatura = $assinatura;
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $responsavel = [];
        $responsavel['cnpj'] = $this->getCNPJ();
        $responsavel['contato'] = $this->getContato();
        $responsavel['email'] = $this->getEmail();
        $responsavel['telefone'] = $this->getTelefone();
        $responsavel['identificador'] = $this->getIdentificador();
        $responsavel['assinatura'] = $this->getAssinatura();
        return $responsavel;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $responsavel Array ou instância de Responsavel, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($responsavel = [])
    {
        if ($responsavel instanceof Responsavel) {
            $responsavel = $responsavel->toArray();
        } elseif (!is_array($responsavel)) {
            return $this;
        }
        if (!isset($responsavel['cnpj'])) {
            $this->setCNPJ(null);
        } else {
            $this->setCNPJ($responsavel['cnpj']);
        }
        if (!isset($responsavel['contato'])) {
            $this->setContato(null);
        } else {
            $this->setContato($responsavel['contato']);
        }
        if (!isset($responsavel['email'])) {
            $this->setEmail(null);
        } else {
            $this->setEmail($responsavel['email']);
        }
        if (!isset($responsavel['telefone'])) {
            $this->setTelefone(null);
        } else {
            $this->setTelefone($responsavel['telefone']);
        }
        if (!array_key_exists('identificador', $responsavel)) {
            $this->setIdentificador(null);
        } else {
            $this->setIdentificador($responsavel['identificador']);
        }
        if (!array_key_exists('assinatura', $responsavel)) {
            $this->setAssinatura(null);
        } else {
            $this->setAssinatura($responsavel['assinatura']);
        }
        return $this;
    }

    /**
     * Cria um nó XML do responsavel de acordo com o leiaute da NFe
     * @param  string $name Nome do nó que será criado
     * @return DOMElement   Nó que contém todos os campos da classe
     */
    public function getNode(?string $name = null, ?string $version = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'infRespTec');
        Util::appendNode($element, 'CNPJ', $this->getCNPJ(true));
        Util::appendNode($element, 'xContato', $this->getContato(true));
        Util::appendNode($element, 'email', $this->getEmail(true));
        Util::appendNode($element, 'fone', $this->getTelefone(true));
        if (!is_null($this->getIdentificador())) {
            Util::appendNode($element, 'idCSRT', $this->getIdentificador(true));
        }
        if (!is_null($this->getAssinatura())) {
            Util::appendNode($element, 'hashCSRT', $this->getAssinatura(true));
        }
        return $element;
    }

    /**
     * Carrega as informações do nó e preenche a instância da classe
     * @param  DOMElement $element Nó do xml com todos as tags dos campos
     * @param  string $name        Nome do nó que será carregado
     * @return DOMElement          Instância do nó que foi carregado
     */
    public function loadNode(\DOMElement $element, ?string $name = null, ?string $version = null): \DOMElement
    {
        $name ??= 'infRespTec';
        $element = Util::findNode($element, $name);
        $this->setCNPJ(Util::loadNode($element, 'CNPJ', 'Tag "CNPJ" não encontrada no Responsavel'));
        $this->setContato(Util::loadNode($element, 'xContato', 'Tag "xContato" não encontrada no Responsavel'));
        $this->setEmail(Util::loadNode($element, 'email', 'Tag "email" não encontrada no Responsavel'));
        $this->setTelefone(Util::loadNode($element, 'fone', 'Tag "fone" não encontrada no Responsavel'));
        $this->setIdentificador(Util::loadNode($element, 'idCSRT'));
        $this->setAssinatura(Util::loadNode($element, 'hashCSRT'));
        return $element;
    }
}
