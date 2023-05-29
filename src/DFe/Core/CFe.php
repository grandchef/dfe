<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Core;

use Exception;
use DOMElement;
use DFe\Common\Util;

/**
 * Classe para validação da nota fiscal eletrônica do consumidor
 */
class CFe extends Nota
{
    /**
     * Versão da nota fiscal
     */
    public const VERSAO = '0.08';

    /**
     * Dados para gerar o QR-Code da CFe
     */
    private $qrcode_data;

    /**
     * Constroi uma instância de CFe vazia
     * @param  array $nfce Array contendo dados do CFe
     */
    public function __construct($nfce = [])
    {
        parent::__construct($nfce);
        $this->setModelo(self::MODELO_CFE);
        $this->setFormato(self::FORMATO_CONSUMIDOR);
    }

    /**
     * Texto com o QR-Code impresso no DANFE NFC-e
     * @param boolean $normalize informa se a qrcode_data deve estar no formato do XML
     * @return mixed qrcode_data do CFe
     */
    public function getQRCodeData($normalize = false)
    {
        if (!$normalize) {
            return $this->qrcode_data;
        }
        return $this->qrcode_data;
    }

    /**
     * Altera o valor da QrcodeURL para o informado no parâmetro
     * @param mixed $qrcode_data novo valor para QrcodeURL
     * @return self A própria instância da classe
     */
    public function setQRCodeData($qrcode_data)
    {
        $this->qrcode_data = $qrcode_data;
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $nfce = parent::toArray($recursive);
        $nfce['qrcode_data'] = $this->getQRCodeData();
        return $nfce;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $nfce Array ou instância de CFe, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($nfce = [])
    {
        if ($nfce instanceof CFe) {
            $nfce = $nfce->toArray();
        } elseif (!is_array($nfce)) {
            return $this;
        }
        parent::fromArray($nfce);
        $this->setQRCodeData($nfce['qrcode_data'] ?? null);
        return $this;
    }

    public function gerarID(): string
    {
        throw new Exception('A chave da CF-e é gerada pelo equipamento SAT');
    }

    public function getLoaderVersion(): string
    {
        $version = $this->getVersao() ?: self::VERSAO;
        return "CFe@{$version}";
    }

    /**
     * Carrega as informações do nó e preenche a instância da classe
     * @param  DOMElement $element Nó do xml com todos as tags dos campos
     * @param  string $name        Nome do nó que será carregado
     * @return DOMElement          Instância do nó que foi carregado
     */
    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $element = parent::loadNode($element, $name, $version);
        $ident = Util::findNode($element, 'ide');
        $qrcode_data = Util::loadNode($ident, 'assinaturaQRCODE');
        if (Util::nodeExists($element, 'Signature') && is_null($qrcode_data)) {
            throw new \Exception('Tag "assinaturaQRCODE" não encontrada no CFe', 404);
        }
        $this->setQRCodeData($qrcode_data);
        return $element;
    }

    public function assinar($dom = null)
    {
        return $dom;
    }

    public function validar($dom)
    {
        return $dom;
    }

    public function addProtocolo($dom)
    {
        return $dom;
    }
}
