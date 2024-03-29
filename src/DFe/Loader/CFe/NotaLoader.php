<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\CFe;

use DFe\Core\CFe;
use DFe\Core\Nota;
use DFe\Common\Util;
use DFe\Entity\Total;
use DFe\Common\Loader;
use DFe\Entity\Produto;
use DFe\Entity\Emitente;
use DFe\Entity\Pagamento;
use DFe\Entity\Responsavel;
use DFe\Entity\Destinatario;

/**
 * Classe para carregamento e geração do XML
 */
class NotaLoader implements Loader
{
    /**
     * Portal da nota fiscal
     */
    public const PORTAL = 'http://www.portalfiscal.inf.br/nfe';

    public function __construct(private Nota $nota)
    {
    }

    /**
     * Chave da nota fiscal
     *
     * @return string id da Nota
     */
    public function getID()
    {
        return 'CFe' . $this->nota->getID();
    }

    public function getDataMovimentacao()
    {
        if (is_null($this->nota->getDataMovimentacao())) {
            return null;
        }
        return Util::toDateTime($this->nota->getDataMovimentacao());
    }

    public function setDataMovimentacao($data_movimentacao)
    {
        if (!is_null($data_movimentacao) && !is_numeric($data_movimentacao)) {
            $data_movimentacao = strtotime($data_movimentacao);
        }
        $this->nota->setDataMovimentacao($data_movimentacao);
        return $this;
    }

    public function getDataContingencia()
    {
        if (is_null($this->nota->getDataContingencia())) {
            return null;
        }
        return Util::toDateTime($this->nota->getDataContingencia());
    }

    public function setDataContingencia($data_contingencia)
    {
        if (!is_null($data_contingencia) && !is_numeric($data_contingencia)) {
            $data_contingencia = strtotime($data_contingencia);
        }
        $this->nota->setDataContingencia($data_contingencia);
        return $this;
    }

    public function getModelo()
    {
        switch ($this->nota->getModelo()) {
            case Nota::MODELO_NFE:
                return '55';
            case Nota::MODELO_NFCE:
                return '65';
        }
        return $this->nota->getModelo();
    }

    public function setModelo($modelo)
    {
        switch ($modelo) {
            case '55':
                $modelo = Nota::MODELO_NFE;
                break;
            case '59':
                $modelo = Nota::MODELO_CFE;
                break;
            case '65':
                $modelo = Nota::MODELO_NFCE;
                break;
        }
        $this->nota->setModelo($modelo);
        return $this;
    }

    public function getTipo()
    {
        switch ($this->nota->getTipo()) {
            case Nota::TIPO_ENTRADA:
                return '0';
            case Nota::TIPO_SAIDA:
                return '1';
        }
        return $this->nota->getTipo();
    }

    public function setTipo($tipo)
    {
        switch ($tipo) {
            case '0':
                $tipo = Nota::TIPO_ENTRADA;
                break;
            case '1':
                $tipo = Nota::TIPO_SAIDA;
                break;
        }
        $this->nota->setTipo($tipo);
        return $this;
    }

    public function getDestino()
    {
        switch ($this->nota->getDestino()) {
            case Nota::DESTINO_INTERNA:
                return '1';
            case Nota::DESTINO_INTERESTADUAL:
                return '2';
            case Nota::DESTINO_EXTERIOR:
                return '3';
        }
        return $this->nota->getDestino();
    }

    public function setDestino($destino)
    {
        switch ($destino) {
            case '1':
                $destino = Nota::DESTINO_INTERNA;
                break;
            case '2':
                $destino = Nota::DESTINO_INTERESTADUAL;
                break;
            case '3':
                $destino = Nota::DESTINO_EXTERIOR;
                break;
        }
        $this->nota->setDestino($destino);
        return $this;
    }

    public function getCodigo()
    {
        return Util::padDigit(strval($this->nota->getCodigo() % 100000000), 8);
    }

    public function getDataEmissao()
    {
        return Util::toDateTime($this->nota->getDataEmissao());
    }

    public function setDataEmissao($data_emissao)
    {
        if (!is_int($data_emissao) && !is_null($data_emissao)) {
            $data_emissao = preg_replace(
                '/(\d{4})(\d{2})(\d{2})(\d{2})?(\d{2})?(\d{2})?/',
                "$1-$2-$3 $4:$5:$6",
                $data_emissao
            );
            $data_emissao = strtotime($data_emissao);
        }
        $this->nota->setDataEmissao($data_emissao);
        return $this;
    }

    public function getFormato()
    {
        switch ($this->nota->getFormato()) {
            case Nota::FORMATO_NENHUMA:
                return '0';
            case Nota::FORMATO_RETRATO:
                return '1';
            case Nota::FORMATO_PAISAGEM:
                return '2';
            case Nota::FORMATO_SIMPLIFICADO:
                return '3';
            case Nota::FORMATO_CONSUMIDOR:
                return '4';
            case Nota::FORMATO_MENSAGEM:
                return '5';
        }
        return $this->nota->getFormato();
    }

    public function setFormato($formato)
    {
        switch ($formato) {
            case '0':
                $formato = Nota::FORMATO_NENHUMA;
                break;
            case '1':
                $formato = Nota::FORMATO_RETRATO;
                break;
            case '2':
                $formato = Nota::FORMATO_PAISAGEM;
                break;
            case '3':
                $formato = Nota::FORMATO_SIMPLIFICADO;
                break;
            case '4':
                $formato = Nota::FORMATO_CONSUMIDOR;
                break;
            case '5':
                $formato = Nota::FORMATO_MENSAGEM;
                break;
        }
        $this->nota->setFormato($formato);
        return $this;
    }

    public function getEmissao()
    {
        switch ($this->nota->getEmissao()) {
            case Nota::EMISSAO_NORMAL:
                return '1';
            case Nota::EMISSAO_CONTINGENCIA:
                return '9';
        }
        return $this->nota->getEmissao();
    }

    public function setEmissao($emissao)
    {
        switch ($emissao) {
            case '1':
                $emissao = Nota::EMISSAO_NORMAL;
                break;
            case '9':
                $emissao = Nota::EMISSAO_CONTINGENCIA;
                break;
        }
        $this->nota->setEmissao($emissao);
        return $this;
    }

    public function getAmbiente()
    {
        switch ($this->nota->getAmbiente()) {
            case Nota::AMBIENTE_PRODUCAO:
                return '1';
            case Nota::AMBIENTE_HOMOLOGACAO:
                return '2';
        }
        return $this->nota->getAmbiente();
    }

    public function setAmbiente($ambiente)
    {
        switch ($ambiente) {
            case '1':
                $ambiente = Nota::AMBIENTE_PRODUCAO;
                break;
            case '2':
                $ambiente = Nota::AMBIENTE_HOMOLOGACAO;
                break;
        }
        $this->nota->setAmbiente($ambiente);
        return $this;
    }

    public function getFinalidade()
    {
        switch ($this->nota->getFinalidade()) {
            case Nota::FINALIDADE_NORMAL:
                return '1';
            case Nota::FINALIDADE_COMPLEMENTAR:
                return '2';
            case Nota::FINALIDADE_AJUSTE:
                return '3';
            case Nota::FINALIDADE_RETORNO:
                return '4';
        }
        return $this->nota->getFinalidade();
    }

    public function setFinalidade($finalidade)
    {
        switch ($finalidade) {
            case '1':
                $finalidade = Nota::FINALIDADE_NORMAL;
                break;
            case '2':
                $finalidade = Nota::FINALIDADE_COMPLEMENTAR;
                break;
            case '3':
                $finalidade = Nota::FINALIDADE_AJUSTE;
                break;
            case '4':
                $finalidade = Nota::FINALIDADE_RETORNO;
                break;
        }
        $this->nota->setFinalidade($finalidade);
        return $this;
    }

    public function getConsumidorFinal()
    {
        switch ($this->nota->getConsumidorFinal()) {
            case 'N':
                return '0';
            case 'Y':
                return '1';
        }
        return $this->nota->getConsumidorFinal();
    }

    public function setConsumidorFinal($consumidor_final)
    {
        if (is_bool($consumidor_final)) {
            $consumidor_final = $consumidor_final ? 'Y' : 'N';
        }
        $this->nota->setConsumidorFinal($consumidor_final);
        return $this;
    }

    public function getPresenca()
    {
        switch ($this->nota->getPresenca()) {
            case Nota::PRESENCA_NENHUM:
                return '0';
            case Nota::PRESENCA_PRESENCIAL:
                return '1';
            case Nota::PRESENCA_INTERNET:
                return '2';
            case Nota::PRESENCA_TELEATENDIMENTO:
                return '3';
            case Nota::PRESENCA_ENTREGA:
                return '4';
            case Nota::PRESENCA_AMBULANTE:
                return '5';
            case Nota::PRESENCA_OUTROS:
                return '9';
        }
        return $this->nota->getPresenca();
    }

    public function setPresenca($presenca)
    {
        switch ($presenca) {
            case '0':
                $presenca = Nota::PRESENCA_NENHUM;
                break;
            case '1':
                $presenca = Nota::PRESENCA_PRESENCIAL;
                break;
            case '2':
                $presenca = Nota::PRESENCA_INTERNET;
                break;
            case '3':
                $presenca = Nota::PRESENCA_TELEATENDIMENTO;
                break;
            case '4':
                $presenca = Nota::PRESENCA_ENTREGA;
                break;
            case '5':
                $presenca = Nota::PRESENCA_AMBULANTE;
                break;
            case '9':
                $presenca = Nota::PRESENCA_OUTROS;
                break;
        }
        $this->nota->setPresenca($presenca);
        return $this;
    }

    public function getIntermediacao()
    {
        switch ($this->nota->getIntermediacao()) {
            case Nota::INTERMEDIACAO_NENHUM:
                return '0';
            case Nota::INTERMEDIACAO_TERCEIROS:
                return '1';
        }
        return $this->nota->getIntermediacao();
    }

    /**
     * Altera o valor da Intermediacao para o informado no parâmetro
     *
     * @param string|null $intermediacao Novo intermediacao para Nota
     *
     * @return self
     */
    public function setIntermediacao($intermediacao)
    {
        switch ($intermediacao) {
            case '0':
                $intermediacao = Nota::INTERMEDIACAO_NENHUM;
                break;
            case '1':
                $intermediacao = Nota::INTERMEDIACAO_TERCEIROS;
                break;
        }
        $this->nota->setIntermediacao($intermediacao);
        return $this;
    }

    public function gerarID()
    {
        $estado = $this->nota->getEmitente()->getEndereco()->getMunicipio()->getEstado();
        $estado->checkCodigos();
        $id = sprintf(
            '%02d%02d%02d%s%02d%03d%09d%01d%08d',
            $estado->getCodigo(),
            date('y', $this->nota->getDataEmissao()), // Ano 2 dígitos
            date('m', $this->nota->getDataEmissao()), // Mês 2 dígitos
            $this->nota->getEmitente()->getCNPJ(),
            $this->getModelo(),
            $this->nota->getSerie(),
            $this->nota->getNumero(),
            $this->getEmissao(),
            $this->getCodigo()
        );
        return $id . Util::getDAC($id, 11);
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $this->nota->setID($this->gerarID());
        $this->nota->setDigitoVerificador(substr($this->getID(), -1, 1));

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'CFe');

        $info = $dom->createElement('infCFe');
        $versao = $dom->createAttribute('versaoDadosEnt');
        $versao->value = CFe::VERSAO;
        $info->appendChild($versao);

        $ident = $dom->createElement('ide');
        Util::appendNode($ident, 'CNPJ', $this->nota->getResponsavel()->getCNPJ());
        Util::appendNode($ident, 'signAC', $this->nota->getResponsavel()->getAssinatura());
        Util::appendNode($ident, 'numeroCaixa', Util::padDigit($this->nota->getCaixa()->getNumero(), 3));
        $info->appendChild($ident);

        $emitente = $this->nota->getEmitente()->getNode($version);
        $emitente = $dom->importNode($emitente, true);
        $info->appendChild($emitente);
        if (!is_null($this->nota->getDestinatario())) {
            $destinatario = $this->nota->getDestinatario()->getNode($version);
            $destinatario = $dom->importNode($destinatario, true);
            $info->appendChild($destinatario);
        } else {
            Util::appendNode($info, 'dest', '');
        }
        if (!is_null($this->nota->getDestinatario()) && !is_null($this->nota->getDestinatario()->getEndereco())) {
            $endereco = $this->nota->getDestinatario()->getEndereco()->getNode($version);
            $endereco = $dom->importNode($endereco, true);
            $info->appendChild($endereco);
        }
        $item = 0;
        $tributos = [];
        $_produtos = $this->nota->getProdutos();
        foreach ($_produtos as $_produto) {
            if (is_null($_produto->getItem())) {
                $item += 1;
                $_produto->setItem($item);
            } else {
                $item = $_produto->getItem();
            }
            $produto = $_produto->getNode($version);
            $produto = $dom->importNode($produto, true);
            $info->appendChild($produto);
            // Soma os tributos aproximados dos produtos
            $imposto_info = $_produto->getImpostoInfo();
            $tributos['info'] = $imposto_info['info'];
            foreach ($imposto_info as $key => $value) {
                if (!is_numeric($value)) {
                    continue;
                }
                if (!isset($tributos[$key])) {
                    $tributos[$key] = 0.00;
                }
                $tributos[$key] += $value;
            }
        }
        Util::appendNode($info, 'total', '');
        $pag = $dom->createElement('pgto');
        $_pagamentos = $this->nota->getPagamentos();
        foreach ($_pagamentos as $_pagamento) {
            if ($_pagamento->getValor() < 0) {
                // troco calculado automaticamente pelo SAT
                continue;
            }
            $pagamento = $_pagamento->getNode($version);
            $pagamento = $dom->importNode($pagamento, true);
            $pag->appendChild($pagamento);
        }
        $info->appendChild($pag);
        $info_adic = $dom->createElement('infAdic');
        if (!is_null($this->nota->getAdicionais())) {
            Util::appendNode($info_adic, 'infAdFisco', $this->nota->getAdicionais());
        }
        // TODO: adicionar informações adicionais somente na NFC-e?
        $_complemento = Produto::addNodeInformacoes($tributos, $info_adic, 'infCpl');
        $this->nota->getTotal()->setComplemento($_complemento);
        if (!is_null($this->nota->getInformacoes())) {
            $_informacoes = $this->nota->getInformacoes();
            foreach ($_informacoes as $_informacao) {
                $informacoes = $dom->createElement('obsFisco');
                Util::addAttribute($informacoes, 'xCampo', $_informacao['campo']);
                Util::appendNode($informacoes, 'xTexto', $_informacao['valor']);
                $info_adic->appendChild($informacoes);
            }
        }
        $info->appendChild($info_adic);
        $element->appendChild($info);
        $dom->appendChild($element);
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $tagName = $name ?? 'CFe';
        $element = Util::findNode($element, $tagName);
        $info = Util::findNode($element, 'infCFe');
        $id = $info->getAttribute('Id');
        if (strlen($id) != 47) {
            throw new \Exception('Atributo "Id" inválido, encontrado: "' . $id . '"', 500);
        }
        $this->nota->setID(substr($id, 3));
        $this->nota->setVersao($info->getAttribute('versao'));
        $ident = Util::findNode($info, 'ide');
        $emitente = new Emitente();
        $emitente->getEndereco()->getMunicipio()->getEstado()->setCodigo(
            Util::loadNode(
                $ident,
                'cUF',
                'Tag "cUF" do campo "Codigo IBGE da UF" não encontrada'
            )
        );
        $this->nota->setCodigo(
            Util::loadNode(
                $ident,
                'cNF',
                'Tag "cNF" do campo "Codigo" não encontrada'
            )
        );
        $this->setModelo(
            Util::loadNode(
                $ident,
                'mod',
                'Tag "mod" do campo "Modelo" não encontrada'
            )
        );
        $this->nota->setSerie(
            Util::loadNode(
                $ident,
                'nserieSAT',
                'Tag "nserieSAT" do campo "Serie" não encontrada'
            )
        );
        $this->nota->setNumero(
            Util::loadNode(
                $ident,
                'nCFe',
                'Tag "nCFe" do campo "Numero" não encontrada'
            )
        );
        $this->setDataEmissao(
            Util::loadNode(
                $ident,
                'dEmi',
                'Tag "dEmi" do campo "DataEmissao" não encontrada'
            ) .
                Util::loadNode(
                    $ident,
                    'hEmi',
                    'Tag "hEmi" do campo "DataEmissao" não encontrada'
                )
        );
        $this->nota->setDigitoVerificador(
            Util::loadNode(
                $ident,
                'cDV',
                'Tag "cDV" do campo "DigitoVerificador" não encontrada'
            )
        );
        $this->setAmbiente(
            Util::loadNode(
                $ident,
                'tpAmb',
                $tagName == 'CFe' ? 'Tag "tpAmb" do campo "Ambiente" não encontrada' : null
            )
        );
        $this->nota->getCaixa()->setNumero(
            Util::loadNode(
                $ident,
                'numeroCaixa',
                'Tag "numeroCaixa" do campo "Caixa::Numero" não encontrada'
            )
        );
        $responsavel = new Responsavel();
        $responsavel->setCNPJ(
            Util::loadNode(
                $ident,
                'CNPJ',
                'Tag "CNPJ" do campo "Responsavel" não encontrada'
            )
        );
        $responsavel->setAssinatura(
            Util::loadNode(
                $ident,
                'signAC',
                'Tag "signAC" do campo "Responsavel" não encontrada'
            )
        );
        $this->nota->setResponsavel($responsavel);
        $emitente->loadNode(
            Util::findNode(
                $info,
                'emit',
                'Tag "emit" do objeto "Emitente" não encontrada'
            ),
            'emit',
            $version
        );
        $this->nota->setEmitente($emitente);
        $_fields = $info->getElementsByTagName('dest');
        $destinatario = null;
        if ($_fields->length > 0 && $_fields->item(0)->childNodes->count() > 0) {
            $destinatario = new Destinatario();
            $destinatario->loadNode($_fields->item(0), 'dest', $version);
        }
        $this->nota->setDestinatario($destinatario);
        $produtos = [];
        $_items = $info->getElementsByTagName('det');
        foreach ($_items as $_item) {
            $produto = new Produto();
            $produto->loadNode($_item, 'det', $version);
            $produtos[] = $produto;
        }
        $this->nota->setProdutos($produtos);
        $pagamentos = [];
        $_items = $info->getElementsByTagName('pgto');
        foreach ($_items as $_item) {
            $_det_items = $_item->getElementsByTagName('MP');
            foreach ($_det_items as $_det_item) {
                $pagamento = new Pagamento();
                $pagamento->loadNode($_det_item, 'MP', $version);
                $pagamentos[] = $pagamento;
            }
            if (Util::nodeExists($_item, 'vTroco')) {
                $pagamento = new Pagamento();
                $pagamento->loadNode($_item, 'vTroco', $version);
                if ($pagamento->getValor() < 0) {
                    $pagamentos[] = $pagamento;
                }
            }
        }
        $this->nota->setPagamentos($pagamentos);
        $_fields = $info->getElementsByTagName('total');
        if ($_fields->length > 0) {
            $total = new Total();
            if ($tagName == 'CFe') {
                $total->loadNode($_fields->item(0), 'ICMSTot', $version);
                $infoAdic = Util::getNode($info, 'infAdic');
                $total->setComplemento(Util::loadNode($infoAdic, 'infCpl'));
            } else {
                $total->setProdutos(
                    Util::loadNode(
                        $element,
                        'vCFe',
                        'Tag "vCFe" não encontrada no Total da nota'
                    )
                );
            }
            $this->nota->setTotal($total);
        } else {
            throw new \Exception('Tag "total" do objeto "Total" não encontrada na Nota', 404);
        }
        $informacoes = [];
        $_items = $info->getElementsByTagName('obsFisco');
        foreach ($_items as $_item) {
            $informacao = [
                'campo' => $_item->getAttribute('xCampo'),
                'valor' => Util::loadNode(
                    $_item,
                    'xTexto',
                    'Tag "xTexto" do campo "Informação" não encontrada'
                )
            ];
            $informacoes[] = $informacao;
        }
        $this->nota->setInformacoes($informacoes);
        return $element;
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
        return $dom;
    }

    /**
     * Valida o documento após assinar
     */
    public function validar($dom)
    {
        return $dom;
    }

    /**
     * Adiciona o protocolo no XML da nota
     */
    public function addProtocolo($dom)
    {
        return $dom;
    }
}
