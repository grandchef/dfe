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

use DOMElement;
use DOMDocument;
use DFe\Core\Nota;
use DFe\Common\Util;

/**
 * Protocolo de autorização da nota, é retornado pela autorização, recibo
 * ou situação e anexado à nota
 */
class Protocolo extends Retorno
{
    /**
     * Chaves de acesso da NF-e, compostas por: UF do emitente, AAMM da emissão
     * da NFe, CNPJ do emitente, modelo, série e número da NF-e e código
     * numérico+DV.
     *
     * @var string
     */
    private $chave;

    /**
     * Digest Value da NF-e processada. Utilizado para conferir a integridade
     * da NF-e original.
     *
     * @var string
     */
    private $validacao;

    /**
     * Número do Protocolo de Status da NF-e. 1 posição (1 – Secretaria de
     * Fazenda Estadual 2 – Receita Federal); 2 - códiga da UF - 2 posições
     * ano; 10 seqüencial no ano.
     *
     * @var string
     */
    private $numero;

    /**
     * Mensagem da SEFAZ para o emissor.
     *
     * @var string
     */
    private $mensagem;

    /**
     * Código da Mensagem.
     *
     * @var string
     */
    private $codigo;

    /**
     * Constroi uma instância de Protocolo vazia
     * @param array $protocolo Array contendo dados do Protocolo
     */
    public function __construct($protocolo = [])
    {
        parent::__construct($protocolo);
    }

    /**
     * Chaves de acesso da NF-e, compostas por: UF do emitente, AAMM da emissão
     * da NFe, CNPJ do emitente, modelo, série e número da NF-e e código
     * numérico+DV.
     * @param boolean $normalize informa se a chave deve estar no formato do XML
     * @return string chave of Protocolo
     */
    public function getChave($normalize = false)
    {
        if (!$normalize) {
            return $this->chave;
        }
        return $this->chave;
    }

    /**
     * Altera o valor da Chave para o informado no parâmetro
     * @param mixed|null $chave novo valor para Chave
     * @return self A própria instância da classe
     */
    public function setChave($chave)
    {
        $this->chave = $chave;
        return $this;
    }

    /**
     * Digest Value da NF-e processada. Utilizado para conferir a integridade
     * da NF-e original.
     * @param boolean $normalize informa se a validacao deve estar no formato do XML
     * @return string validacao of Protocolo
     */
    public function getValidacao($normalize = false)
    {
        if (!$normalize) {
            return $this->validacao;
        }
        return $this->validacao;
    }

    /**
     * Altera o valor da Validacao para o informado no parâmetro
     *
     * @param string|null $validacao Novo validacao para Protocolo
     *
     * @return self A própria instância da classe
     */
    public function setValidacao($validacao)
    {
        $this->validacao = $validacao;
        return $this;
    }

    /**
     * Número do Protocolo de Status da NF-e. 1 posição (1 – Secretaria de
     * Fazenda Estadual 2 – Receita Federal); 2 - códiga da UF - 2 posições
     * ano; 10 seqüencial no ano.
     * @param boolean $normalize informa se o numero deve estar no formato do XML
     * @return string numero of Protocolo
     */
    public function getNumero($normalize = false)
    {
        if (!$normalize) {
            return $this->numero;
        }
        return $this->numero;
    }

    /**
     * Altera o valor do Numero para o informado no parâmetro
     *
     * @param string|null $numero Novo numero para Protocolo
     *
     * @return self A própria instância da classe
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
        return $this;
    }

    /**
     * Mensagem da SEFAZ para o emissor.
     * @param boolean $normalize informa se a mensagem deve estar no formato do XML
     * @return string mensagem of Protocolo
     */
    public function getMensagem($normalize = false)
    {
        if (!$normalize) {
            return $this->mensagem;
        }
        return $this->mensagem;
    }

    /**
     * Altera o valor da Mensagem para o informado no parâmetro
     *
     * @param string|null $mensagem Novo mensagem para Protocolo
     *
     * @return self A própria instância da classe
     */
    public function setMensagem($mensagem)
    {
        $this->mensagem = $mensagem;
        return $this;
    }

    /**
     * Código da Mensagem.
     * @param boolean $normalize informa se o codigo deve estar no formato do XML
     * @return string|null codigo of Protocolo
     */
    public function getCodigo($normalize = false)
    {
        if (!$normalize) {
            return $this->codigo;
        }
        return $this->codigo;
    }

    /**
     * Altera o valor do Codigo para o informado no parâmetro
     *
     * @param string|null $codigo Novo codigo para Protocolo
     *
     * @return self A própria instância da classe
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }

    /**
     * Converte a instância da classe para um array de campos com valores
     * @return array Array contendo todos os campos e valores da instância
     */
    public function toArray($recursive = false)
    {
        $protocolo = parent::toArray($recursive);
        $protocolo['chave'] = $this->getChave();
        $protocolo['validacao'] = $this->getValidacao();
        $protocolo['numero'] = $this->getNumero();
        $protocolo['mensagem'] = $this->getMensagem();
        $protocolo['codigo'] = $this->getCodigo();
        return $protocolo;
    }

    /**
     * Atribui os valores do array para a instância atual
     * @param mixed $protocolo Array ou instância de Protocolo, para copiar os valores
     * @return self A própria instância da classe
     */
    public function fromArray($protocolo = [])
    {
        if ($protocolo instanceof Protocolo) {
            $protocolo = $protocolo->toArray();
        } elseif (!is_array($protocolo)) {
            return $this;
        }
        parent::fromArray($protocolo);
        if (!isset($protocolo['chave'])) {
            $this->setChave(null);
        } else {
            $this->setChave($protocolo['chave']);
        }
        if (!array_key_exists('validacao', $protocolo)) {
            $this->setValidacao(null);
        } else {
            $this->setValidacao($protocolo['validacao']);
        }
        if (!array_key_exists('numero', $protocolo)) {
            $this->setNumero(null);
        } else {
            $this->setNumero($protocolo['numero']);
        }
        if (!array_key_exists('mensagem', $protocolo)) {
            $this->setMensagem(null);
        } else {
            $this->setMensagem($protocolo['mensagem']);
        }
        if (!array_key_exists('codigo', $protocolo)) {
            $this->setCodigo(null);
        } else {
            $this->setCodigo($protocolo['codigo']);
        }
        return $this;
    }

    /**
     * Carrega as informações do nó e preenche a instância da classe
     */
    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $name ??= 'infProt';
        $element = parent::loadNode($element, $name);
        $this->setChave(
            Util::loadNode(
                $element,
                'chNFe',
                'Tag "chNFe" não encontrada no Protocolo'
            )
        );
        $this->setValidacao(Util::loadNode($element, 'digVal'));
        $this->setNumero(Util::loadNode($element, 'nProt'));
        $this->setMensagem(Util::loadNode($element, 'xMsg'));
        $this->setCodigo(Util::loadNode($element, 'cMsg'));
        return $element;
    }

    /**
     * Cria um nó XML do protocolo de acordo com o leiaute da NFe
     * @param string $name Nome do nó que será criado
     * @return DOMElement Nó que contém todos os campos da classe
     */
    public function getNode(?string $name = null): \DOMElement
    {
        $old_uf = $this->getUF();
        $this->setUF(null);
        $info = parent::getNode('infProt');
        $this->setUF($old_uf);
        $dom = $info->ownerDocument;
        $element = $dom->createElement($name ?? 'protNFe');
        $versao = $dom->createAttribute('versao');
        $versao->value = Nota::VERSAO;
        $element->appendChild($versao);

        $id = $dom->createAttribute('Id');
        $id->value = 'ID' . $this->getNumero(true);
        $info->appendChild($id);

        $status = $info->getElementsByTagName('cStat')->item(0);
        Util::appendNode($info, 'nProt', $this->getNumero(true), $status);
        Util::appendNode($info, 'digVal', $this->getValidacao(true), $status);
        $nodes = $info->getElementsByTagName('dhRecbto');
        if ($nodes->length > 0) {
            $recebimento = $nodes->item(0);
        } else {
            $recebimento = $status;
        }
        Util::appendNode($info, 'chNFe', $this->getChave(true), $recebimento);
        if (! is_null($this->getCodigo())) {
            Util::appendNode($info, 'cMsg', $this->getCodigo(true));
        }
        if (! empty($this->getMensagem())) {
            Util::appendNode($info, 'xMsg', $this->getMensagem(true));
        }
        $element->appendChild($info);
        return $element;
    }
}
