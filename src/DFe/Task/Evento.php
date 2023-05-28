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
use DFe\Common\Loader;
use DFe\Loader\NFe\Task\EventoLoader;
use DFe\Loader\CFe\Task\EventoLoader as CFeEventoLoader;

class Evento extends Retorno
{
    public const TIPO_CANCELAMENTO = '110111';

    private $id;
    private $orgao;
    private $identificador;
    private $chave;
    private $data;
    private $tipo;
    private $sequencia;
    private $descricao;
    private $numero;
    private $justificativa;
    private $email;
    private $modelo;

    private $documento;
    private $informacao;

    public function __construct($evento = [])
    {
        parent::__construct($evento);
    }

    /**
     * Identificador da TAG a ser assinada, a regra de formação do Id é: "ID" +
     * tpEvento +  chave da NF-e + nSeqEvento
     */
    public function getID()
    {
        return $this->id;
    }

    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Código do órgão de recepção do Evento. Utilizar a Tabela do IBGE
     * extendida, utilizar 91 para identificar o Ambiente Nacional
     */
    public function getOrgao()
    {
        return $this->orgao;
    }

    public function setOrgao($orgao)
    {
        $this->orgao = $orgao;
        return $this;
    }

    /**
     * Identificação do  autor do evento
     */
    public function getIdentificador()
    {
        return $this->identificador;
    }

    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;
        return $this;
    }

    /**
     * Chave de Acesso da NF-e vinculada ao evento
     */
    public function getChave()
    {
        return $this->chave;
    }

    public function setChave($chave)
    {
        $this->chave = $chave;
        return $this;
    }

    /**
     * Data e Hora do Evento, formato UTC (AAAA-MM-DDThh:mm:ssTZD, onde TZD =
     * +hh:mm ou -hh:mm)
     */
    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Tipo do Evento
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }

    /**
     * Seqüencial do evento para o mesmo tipo de evento.  Para maioria dos
     * eventos será 1, nos casos em que possa existir mais de um evento, como é
     * o caso da carta de correção, o autor do evento deve numerar de forma
     * seqüencial.
     */
    public function getSequencia()
    {
        return $this->sequencia;
    }

    public function setSequencia($sequencia)
    {
        $this->sequencia = $sequencia;
        return $this;
    }

    /**
     * Descrição do Evento
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }

    /**
     * Número do Protocolo de Status da NF-e. 1 posição (1 – Secretaria de
     * Fazenda Estadual 2 – Receita Federal); 2 posições ano; 10 seqüencial no
     * ano.
     */
    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * Justificativa do cancelamento
     */
    public function getJustificativa()
    {
        return $this->justificativa;
    }

    public function setJustificativa($justificativa)
    {
        $this->justificativa = $justificativa;
        return $this;
    }

    /**
     * email do destinatário
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Código do modelo do Documento Fiscal. 55 = NF-e; 65 = NFC-e.
     */
    public function getModelo()
    {
        return $this->modelo;
    }

    public function setModelo($modelo)
    {
        $this->modelo = $modelo;
        return $this;
    }

    /**
     * Informa o XML do objeto, quando não informado o XML é gerado a partir do
     * objeto
     *
     * @return \DOMDocument
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    public function setDocumento($documento)
    {
        $this->documento = $documento;
        return $this;
    }

    /**
     * Resposta de informação do evento
     *
     * @return Evento
     */
    public function getInformacao()
    {
        return $this->informacao;
    }

    public function setInformacao($informacao)
    {
        $this->informacao = $informacao;
        return $this;
    }

    /**
     * Informa se a identificação é um CNPJ
     */
    public function isCNPJ()
    {
        return strlen($this->getIdentificador() ?? '') == 14;
    }

    /**
     * Informa se o lote já foi processado e já tem um protocolo
     */
    public function isProcessado()
    {
        return $this->getStatus() == '128';
    }

    /**
     * Informa se a nota foi cancelada com sucesso
     */
    public function isCancelado()
    {
        return in_array($this->getStatus(), ['135', '155']);
    }

    public function toArray($recursive = false)
    {
        $evento = parent::toArray($recursive);
        $evento['id'] = $this->getID();
        $evento['orgao'] = $this->getOrgao();
        $evento['identificador'] = $this->getIdentificador();
        $evento['chave'] = $this->getChave();
        $evento['data'] = $this->getData();
        $evento['tipo'] = $this->getTipo();
        $evento['sequencia'] = $this->getSequencia();
        $evento['descricao'] = $this->getDescricao();
        $evento['numero'] = $this->getNumero();
        $evento['justificativa'] = $this->getJustificativa();
        $evento['email'] = $this->getEmail();
        $evento['modelo'] = $this->getModelo();
        $evento['documento'] = $this->getDocumento();
        $evento['informacao'] = $this->getInformacao();
        return $evento;
    }

    public function fromArray($evento = [])
    {
        if ($evento instanceof Evento) {
            $evento = $evento->toArray();
        } elseif (!is_array($evento)) {
            return $this;
        }
        parent::fromArray($evento);
        $this->setID($evento['id'] ?? null);
        $this->setOrgao($evento['orgao'] ?? null);
        $this->setIdentificador($evento['identificador'] ?? null);
        $this->setChave($evento['chave'] ?? null);
        $this->setData($evento['data'] ?? null);
        $this->setTipo($evento['tipo'] ?? self::TIPO_CANCELAMENTO);
        $this->setSequencia($evento['sequencia'] ?? 1);
        $this->setDescricao($evento['descricao'] ?? 'Cancelamento');
        $this->setNumero($evento['numero'] ?? null);
        $this->setJustificativa($evento['justificativa'] ?? null);
        $this->setEmail($evento['email'] ?? null);
        $this->setModelo($evento['modelo'] ?? null);
        $this->setDocumento($evento['documento'] ?? null);
        $this->setInformacao($evento['informacao'] ?? null);
        return $this;
    }

    public function getLoaderVersion(): string
    {
        if ($this->getModelo() === Nota::MODELO_CFE) {
            $version = $this->getVersao();
            return "CFe@{$version}";
        }
        $version = $this->getVersao();
        return "NFe@{$version}";
    }

    public function getLoader(string $version = ''): Loader
    {
        if (strpos($version ?: $this->getLoaderVersion(), 'CFe@') !== false) {
            return new CFeEventoLoader($this);
        }
        return new EventoLoader($this);
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $version = $version ?: $this->getLoaderVersion();
        return $this->getLoader($version)->getNode($version, $name);
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $version = $version ?: $this->getLoaderVersion();
        return $this->getLoader($version)->loadNode($element, $name, $version);
    }

    public function envia(): self
    {
        $envio = new Envio();
        $envio->setServico(Envio::SERVICO_EVENTO);
        $envio->setAmbiente($this->getAmbiente());
        $envio->setModelo($this->getModelo());
        $envio->setEmissao(Nota::EMISSAO_NORMAL);
        $this->setVersao($envio->getVersao());
        $version = $this->getLoaderVersion();
        $loader = $this->getLoader($version);
        $envio->setConteudo($loader->getNode($version)->ownerDocument);
        $domResponse = $envio->envia();
        $loader->loadNode($domResponse->documentElement, '', $version);
        return $this;
    }
}
