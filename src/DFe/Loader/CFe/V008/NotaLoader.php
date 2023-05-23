<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\CFe\V008;

use DFe\Core\Nota;
use DFe\Core\SEFAZ;
use DFe\Common\Util;
use DFe\Entity\Total;
use DFe\Common\Loader;
use DFe\Task\Protocolo;
use DFe\Entity\Produto;
use DFe\Entity\Emitente;
use DFe\Entity\Pagamento;
use DFe\Entity\Transporte;
use DFe\Entity\Responsavel;
use DFe\Entity\Destinatario;
use DFe\Entity\Intermediador;
use DFe\Util\AdapterInterface;
use DFe\Util\XmlseclibsAdapter;
use DFe\Exception\ValidationException;

/**
 * Classe para carregamento e geração do XML
 */
class NotaLoader implements Loader
{
    /**
     * Versão da nota fiscal
     */
    public const VERSAO = '0.08';

    /**
     * Portal da nota fiscal
     */
    public const PORTAL = 'http://www.portalfiscal.inf.br/nfe';

    public function __construct(private Nota $nota) {}

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
        if (!is_numeric($data_emissao) && ! is_null($data_emissao)) {
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

    private function getNodeTotal($name = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'total');

        // Totais referentes ao ICMS
        $total = $this->nota->getTotais();
        $icms = $dom->createElement('ICMSTot');
        Util::appendNode($icms, 'vBC', Util::toCurrency($total['base']));
        Util::appendNode($icms, 'vICMS', Util::toCurrency($total['icms']));
        Util::appendNode($icms, 'vICMSDeson', Util::toCurrency($total['desoneracao']));
        Util::appendNode($icms, 'vFCP', Util::toCurrency($total['fundo']));
        Util::appendNode($icms, 'vBCST', Util::toCurrency($total['base.st']));
        Util::appendNode($icms, 'vST', Util::toCurrency($total['icms.st']));
        Util::appendNode($icms, 'vFCPST', Util::toCurrency($total['fundo.st']));
        Util::appendNode($icms, 'vFCPSTRet', Util::toCurrency($total['fundo.retido.st']));
        Util::appendNode($icms, 'vProd', Util::toCurrency($total['produtos']));
        Util::appendNode($icms, 'vFrete', Util::toCurrency($total['frete']));
        Util::appendNode($icms, 'vSeg', Util::toCurrency($total['seguro']));
        Util::appendNode($icms, 'vDesc', Util::toCurrency($total['desconto']));
        Util::appendNode($icms, 'vII', Util::toCurrency($total['ii']));
        Util::appendNode($icms, 'vIPI', Util::toCurrency($total['ipi']));
        Util::appendNode($icms, 'vIPIDevol', Util::toCurrency($total['ipi.devolvido']));
        Util::appendNode($icms, 'vPIS', Util::toCurrency($total['pis']));
        Util::appendNode($icms, 'vCOFINS', Util::toCurrency($total['cofins']));
        Util::appendNode($icms, 'vOutro', Util::toCurrency($total['despesas']));
        Util::appendNode($icms, 'vNF', Util::toCurrency($total['nota']));
        Util::appendNode($icms, 'vTotTrib', Util::toCurrency($total['tributos']));
        $element->appendChild($icms);
        $this->nota->setTotal(new Total($total));
        $this->nota->getTotal()->setProdutos($total['produtos']);

        // TODO: Totais referentes ao ISSQN

        // TODO: Retenção de Tributos Federais
        return $element;
    }

    public function getNode(?string $name = null): \DOMElement
    {
        $this->nota->getEmitente()->getEndereco()->checkCodigos();
        $this->nota->setID($this->gerarID());
        $this->nota->setDigitoVerificador(substr($this->getID(), -1, 1));

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $element = $dom->createElement($name ?? 'CFe');

        $info = $dom->createElement('infCFe');
        $versao = $dom->createAttribute('versaoDadosEnt');
        $versao->value = self::VERSAO;
        $info->appendChild($versao);

        $ident = $dom->createElement('ide');
        Util::appendNode($ident, 'CNPJ', $this->nota->getResponsavel()->getCNPJ());
        Util::appendNode($ident, 'signAC', $this->nota->getResponsavel()->getAssinatura());
        Util::appendNode($ident, 'numeroCaixa', $this->nota->getCaixa()->getNumero());
        $info->appendChild($ident);

        $emitente = $this->nota->getEmitente()->getNode();
        $emitente = $dom->importNode($emitente, true);
        $info->appendChild($emitente);
        if ($this->nota->getAmbiente() == Nota::AMBIENTE_HOMOLOGACAO && !is_null($this->nota->getDestinatario())) {
            $this->nota->getDestinatario()->setNome('NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL');
        }
        if (!is_null($this->nota->getDestinatario())) {
            $destinatario = $this->nota->getDestinatario()->getNode();
            $destinatario = $dom->importNode($destinatario, true);
            $info->appendChild($destinatario);
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
            if ($this->nota->getAmbiente() == Nota::AMBIENTE_HOMOLOGACAO) {
                $_produto->setDescricao('NOTA FISCAL EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL');
            }
            $produto = $_produto->getNode();
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
        $total = $this->getNodeTotal();
        $total = $dom->importNode($total, true);
        $info->appendChild($total);
        $transporte = $this->nota->getTransporte()->getNode();
        $transporte = $dom->importNode($transporte, true);
        $info->appendChild($transporte);
        // TODO: adicionar cobrança
        $pag = $dom->createElement('pgto');
        $_pagamentos = $this->nota->getPagamentos();
        foreach ($_pagamentos as $_pagamento) {
            $pagamento = $_pagamento->getNode();
            $pagamento = $dom->importNode($pagamento, true);
            $pag->appendChild($pagamento);
        }
        $info->appendChild($pag);
        if (!is_null($this->nota->getIntermediador())) {
            $intermediador = $this->nota->getIntermediador()->getNode();
            $intermediador = $dom->importNode($intermediador, true);
            $info->appendChild($intermediador);
        }
        $info_adic = $dom->createElement('infAdic');
        if (!is_null($this->nota->getAdicionais())) {
            Util::appendNode($info_adic, 'infAdFisco', $this->nota->getAdicionais());
        }
        // TODO: adicionar informações adicionais somente na NFC-e?
        $_complemento = Produto::addNodeInformacoes($tributos, $info_adic, 'infCpl');
        $this->nota->getTotal()->setComplemento($_complemento);
        if (!is_null($this->nota->getObservacoes())) {
            $_observacoes = $this->nota->getObservacoes();
            foreach ($_observacoes as $_observacao) {
                $observacoes = $dom->createElement('obsCont');
                Util::addAttribute($observacoes, 'xCampo', $_observacao['campo']);
                Util::appendNode($observacoes, 'xTexto', $_observacao['valor']);
                $info_adic->appendChild($observacoes);
            }
        }
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
        // TODO: adicionar exportação
        // TODO: adicionar compra
        // TODO: adicionar cana
        if (!is_null($this->nota->getResponsavel())) {
            $responsavel = $this->nota->getResponsavel()->getNode();
            $responsavel = $dom->importNode($responsavel, true);
            $info->appendChild($responsavel);
        }
        $element->appendChild($info);
        $dom->appendChild($element);
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null): \DOMElement
    {
        $root = $element;
        $name ??= 'NFe';
        $element = Util::findNode($element, $name);
        $_fields = $element->getElementsByTagName('infNFe');
        if ($_fields->length > 0) {
            /** @var \DOMElement */
            $info = $_fields->item(0);
        } else {
            throw new \Exception('Tag "infNFe" não encontrada', 404);
        }
        $id = $info->getAttribute('Id');
        if (strlen($id) != 47) {
            throw new \Exception('Atributo "Id" inválido, encontrado: "' . $id . '"', 500);
        }
        $this->nota->setID(substr($id, 3));
        $_fields = $info->getElementsByTagName('ide');
        if ($_fields->length > 0) {
            $ident = $_fields->item(0);
        } else {
            throw new \Exception('Tag "ide" não encontrada', 404);
        }
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
        $this->nota->setNatureza(
            Util::loadNode(
                $ident,
                'natOp',
                'Tag "natOp" do campo "Natureza" não encontrada'
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
                'serie',
                'Tag "serie" do campo "Serie" não encontrada'
            )
        );
        $this->nota->setNumero(
            Util::loadNode(
                $ident,
                'nNF',
                'Tag "nNF" do campo "Numero" não encontrada'
            )
        );
        $this->setDataEmissao(
            Util::loadNode(
                $ident,
                'dhEmi',
                'Tag "dhEmi" do campo "DataEmissao" não encontrada'
            )
        );
        $this->setTipo(
            Util::loadNode(
                $ident,
                'tpNF',
                'Tag "tpNF" do campo "Tipo" não encontrada'
            )
        );
        $this->setDestino(
            Util::loadNode(
                $ident,
                'idDest',
                'Tag "idDest" do campo "Destino" não encontrada'
            )
        );
        $emitente->getEndereco()->getMunicipio()->setCodigo(
            Util::loadNode(
                $ident,
                'cMunFG',
                'Tag "cMunFG" do campo "Codigo IBGE do município" não encontrada'
            )
        );
        $this->setDataMovimentacao(Util::loadNode($ident, 'dhSaiEnt'));
        $this->setFormato(
            Util::loadNode(
                $ident,
                'tpImp',
                'Tag "tpImp" do campo "Formato" não encontrada'
            )
        );
        $this->setEmissao(
            Util::loadNode(
                $ident,
                'tpEmis',
                'Tag "tpEmis" do campo "Emissao" não encontrada'
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
                'Tag "tpAmb" do campo "Ambiente" não encontrada'
            )
        );
        $this->setFinalidade(
            Util::loadNode(
                $ident,
                'finNFe',
                'Tag "finNFe" do campo "Finalidade" não encontrada'
            )
        );
        $this->setConsumidorFinal(
            Util::loadNode(
                $ident,
                'indFinal',
                'Tag "indFinal" do campo "ConsumidorFinal" não encontrada'
            )
        );
        $this->setPresenca(
            Util::loadNode(
                $ident,
                'indPres',
                'Tag "indPres" do campo "Presenca" não encontrada'
            )
        );
        $this->setIntermediacao(Util::loadNode($ident, 'indIntermed'));
        $this->setDataContingencia(Util::loadNode($ident, 'dhCont'));
        $this->nota->setJustificativa(Util::loadNode($ident, 'xJust'));
        $emitente->loadNode(
            Util::findNode(
                $info,
                'emit',
                'Tag "emit" do objeto "Emitente" não encontrada'
            ),
            'emit'
        );
        $this->nota->setEmitente($emitente);
        $_fields = $info->getElementsByTagName('dest');
        $destinatario = null;
        if ($_fields->length > 0) {
            $destinatario = new Destinatario();
            $destinatario->loadNode($_fields->item(0), 'dest');
        }
        $this->nota->setDestinatario($destinatario);
        $_fields = $info->getElementsByTagName('infRespTec');
        $responsavel = null;
        if ($_fields->length > 0) {
            $responsavel = new Responsavel();
            $responsavel->loadNode($_fields->item(0), 'infRespTec');
        }
        $this->nota->setResponsavel($responsavel);
        $produtos = [];
        $_items = $info->getElementsByTagName('det');
        foreach ($_items as $_item) {
            $produto = new Produto();
            $produto->loadNode($_item, 'det');
            $produtos[] = $produto;
        }
        $this->nota->setProdutos($produtos);
        $_fields = $info->getElementsByTagName('transp');
        $transporte = null;
        if ($_fields->length > 0) {
            $transporte = new Transporte();
            $transporte->loadNode($_fields->item(0), 'transp');
        }
        $this->nota->setTransporte($transporte);
        $pagamentos = [];
        $_items = $info->getElementsByTagName('pag');
        foreach ($_items as $_item) {
            $_det_items = $_item->getElementsByTagName('detPag');
            foreach ($_det_items as $_det_item) {
                $pagamento = new Pagamento();
                $pagamento->loadNode($_det_item, 'detPag');
                $pagamentos[] = $pagamento;
            }
            if (Util::nodeExists($_item, 'vTroco')) {
                $pagamento = new Pagamento();
                $pagamento->loadNode($_item, 'vTroco');
                if ($pagamento->getValor() < 0) {
                    $pagamentos[] = $pagamento;
                }
            }
        }
        $this->nota->setPagamentos($pagamentos);
        $_fields = $info->getElementsByTagName('total');
        if ($_fields->length > 0) {
            $total = new Total();
            $total->loadNode($_fields->item(0), 'total');
            $total->setComplemento(Util::loadNode($info, 'infCpl'));
        } else {
            throw new \Exception('Tag "total" do objeto "Total" não encontrada na Nota', 404);
        }
        $this->nota->setTotal($total);
        $_fields = $info->getElementsByTagName('infIntermed');
        $intermediador = null;
        if ($_fields->length > 0) {
            $intermediador = new Intermediador();
            $intermediador->loadNode($_fields->item(0), 'infIntermed');
        }
        $this->nota->setIntermediador($intermediador);
        $this->nota->setAdicionais(Util::loadNode($info, 'infAdFisco'));
        $observacoes = [];
        $_items = $info->getElementsByTagName('obsCont');
        foreach ($_items as $_item) {
            $observacao = [
                'campo' => $_item->getAttribute('xCampo'),
                'valor' => Util::loadNode(
                    $_item,
                    'xTexto',
                    'Tag "xTexto" do campo "Observação" não encontrada'
                )
            ];
            $observacoes[] = $observacao;
        }
        $this->nota->setObservacoes($observacoes);
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

        $_fields = $root->getElementsByTagName('protNFe');
        $protocolo = null;
        if ($_fields->length > 0) {
            $protocolo = new Protocolo();
            $protocolo->loadNode($_fields->item(0), 'infProt');
        }
        $this->nota->setProtocolo($protocolo);
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
        $config = SEFAZ::getInstance()->getConfiguracao();
        $config->verificaValidadeCertificado();

        $adapter = new XmlseclibsAdapter();
        $adapter->setPrivateKey($config->getCertificado()->getChavePrivada());
        $adapter->setPublicKey($config->getCertificado()->getChavePublica());
        $adapter->addTransform(AdapterInterface::ENVELOPED);
        $adapter->addTransform(AdapterInterface::XML_C14N);
        $adapter->sign($dom, 'infNFe');
        return $dom;
    }

    /**
     * Valida o documento após assinar
     */
    public function validar($dom)
    {
        $dom->loadXML($dom->saveXML());
        $xsd_path = dirname(dirname(dirname(__DIR__))) . '/Core/schema';
        if (is_null($this->nota->getProtocolo())) {
            $xsd_file = $xsd_path . '/NFe/v4.0.0/nfe_v' . self::VERSAO . '.xsd';
        } else {
            $xsd_file = $xsd_path . '/NFe/v4.0.0/procNFe_v' . self::VERSAO . '.xsd';
        }
        if (!file_exists($xsd_file)) {
            throw new \Exception(sprintf('O arquivo "%s" de esquema XSD não existe!', $xsd_file), 404);
        }
        // Enable user error handling
        $save = libxml_use_internal_errors();
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

    /**
     * Adiciona o protocolo no XML da nota
     */
    public function addProtocolo($dom)
    {
        if (is_null($this->nota->getProtocolo())) {
            throw new \Exception('O protocolo não foi informado na nota "' . $this->getID() . '"', 404);
        }
        $notae = $dom->getElementsByTagName('NFe')->item(0);
        // Corrige xmlns:default
        $notae_xml = $dom->saveXML($notae);

        $element = $dom->createElement('nfeProc');
        $element->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns', self::PORTAL);
        $versao = $dom->createAttribute('versao');
        $versao->value = self::VERSAO;
        $element->appendChild($versao);
        $dom->removeChild($notae);
        // Corrige xmlns:default
        $notae = $dom->createElement('NFe', 0);

        $element->appendChild($notae);
        $info = $this->nota->getProtocolo()->getNode();
        $info = $dom->importNode($info, true);
        $element->appendChild($info);
        $dom->appendChild($element);
        // Corrige xmlns:default
        $xml = $dom->saveXML();
        $xml = str_replace('<NFe>0</NFe>', $notae_xml, $xml);
        $dom->loadXML($xml);

        return $dom;
    }
}
