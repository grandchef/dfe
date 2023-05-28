<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace DFe\Loader\NFe;

use DFe\Common\Util;
use DFe\Common\Loader;
use DFe\Entity\Pagamento;

class PagamentoLoader implements Loader
{
    public function __construct(private Pagamento $pagamento)
    {
    }

    /**
     * Indicador da forma de pagamento: 0 – pagamento à vista; 1 – pagamento à
     * prazo.
     *
     * @return mixed indicador da Nota
     */
    public function getIndicador()
    {
        switch ($this->pagamento->getIndicador()) {
            case Pagamento::INDICADOR_AVISTA:
                return '0';
            case Pagamento::INDICADOR_APRAZO:
                return '1';
        }
        return $this->pagamento->getIndicador();
    }

    /**
     * Altera o valor do Indicador para o informado no parâmetro
     * @param mixed $indicador novo valor para Indicador
     * @return self A própria instância da classe
     */
    public function setIndicador($indicador)
    {
        switch ($indicador) {
            case '0':
                $indicador = Pagamento::INDICADOR_AVISTA;
                break;
            case '1':
                $indicador = Pagamento::INDICADOR_APRAZO;
                break;
        }
        $this->pagamento->setIndicador($indicador);
        return $this;
    }

    /**
     * Forma de Pagamento:01-Dinheiro;02-Cheque;03-Cartão de Crédito;04-Cartão
     * de Débito;05-Crédito Loja;10-Vale Alimentação;11-Vale Refeição;12-Vale
     * Presente;13-Vale Combustível;14 - Duplicata Mercantil;15 - Boleto
     * Bancario;16=Depósito Bancário;17=Pagamento Instantâneo
     * (PIX);18=Transferência bancária, Carteira Digital;19=Programa de
     * fidelidade, Cashback, Crédito Virtual.;90 - Sem Pagamento;99 - Outros
     *
     * @return string forma of Pagamento
     */
    public function getForma()
    {
        switch ($this->pagamento->getForma()) {
            case Pagamento::FORMA_DINHEIRO:
                return '01';
            case Pagamento::FORMA_CHEQUE:
                return '02';
            case Pagamento::FORMA_CREDITO:
                return '03';
            case Pagamento::FORMA_DEBITO:
                return '04';
            case Pagamento::FORMA_CREDIARIO:
                return '05';
            case Pagamento::FORMA_ALIMENTACAO:
                return '10';
            case Pagamento::FORMA_REFEICAO:
                return '11';
            case Pagamento::FORMA_PRESENTE:
                return '12';
            case Pagamento::FORMA_COMBUSTIVEL:
                return '13';
            case Pagamento::FORMA_DUPLICATA:
                return '14';
            case Pagamento::FORMA_BOLETO:
                return '15';
            case Pagamento::FORMA_DEPOSITO:
                return '16';
            case Pagamento::FORMA_INSTANTANEO:
                return '17';
            case Pagamento::FORMA_TRANSFERENCIA:
                return '18';
            case Pagamento::FORMA_FIDELIDADE:
                return '19';
            case Pagamento::FORMA_CORTESIA:
                return '90';
            case Pagamento::FORMA_OUTROS:
                return '99';
        }
        return $this->pagamento->getForma();
    }

    /**
     * Altera o valor do Forma para o informado no parâmetro
     *
     * @param string|null $forma Novo forma para Pagamento
     *
     * @return self A própria instância da classe
     */
    public function setForma($forma)
    {
        switch ($forma) {
            case '01':
                $forma = Pagamento::FORMA_DINHEIRO;
                break;
            case '02':
                $forma = Pagamento::FORMA_CHEQUE;
                break;
            case '03':
                $forma = Pagamento::FORMA_CREDITO;
                break;
            case '04':
                $forma = Pagamento::FORMA_DEBITO;
                break;
            case '05':
                $forma = Pagamento::FORMA_CREDIARIO;
                break;
            case '10':
                $forma = Pagamento::FORMA_ALIMENTACAO;
                break;
            case '11':
                $forma = Pagamento::FORMA_REFEICAO;
                break;
            case '12':
                $forma = Pagamento::FORMA_PRESENTE;
                break;
            case '13':
                $forma = Pagamento::FORMA_COMBUSTIVEL;
                break;
            case '14':
                $forma = Pagamento::FORMA_DUPLICATA;
                break;
            case '15':
                $forma = Pagamento::FORMA_BOLETO;
                break;
            case '16':
                $forma = Pagamento::FORMA_DEPOSITO;
                break;
            case '17':
                $forma = Pagamento::FORMA_INSTANTANEO;
                break;
            case '18':
                $forma = Pagamento::FORMA_TRANSFERENCIA;
                break;
            case '19':
                $forma = Pagamento::FORMA_FIDELIDADE;
                break;
            case '90':
                $forma = Pagamento::FORMA_CORTESIA;
                break;
            case '99':
                $forma = Pagamento::FORMA_OUTROS;
                break;
        }
        $this->pagamento->setForma($forma);
        return $this;
    }

    /**
     * Valor do Pagamento
     *
     * @return float|string valor of Pagamento
     */
    public function getValor()
    {
        return Util::toCurrency($this->pagamento->getValor());
    }

    /**
     * Altera o valor da Valor para o informado no parâmetro
     *
     * @param float|string|null $valor Novo valor para Pagamento
     *
     * @return self A própria instância da classe
     */
    public function setValor($valor)
    {
        $valor = floatval($valor);
        $this->pagamento->setValor($valor);
        return $this;
    }

    /**
     * Tipo de Integração do processo de pagamento com o sistema de automação
     * da empresa/1=Pagamento integrado com o sistema de automação da empresa
     * Ex. equipamento TEF , Comercio Eletronico 2=Pagamento não integrado com
     * o sistema de automação da empresa Ex: equipamento POS
     *
     * @return string integrado of Pagamento
     */
    public function getIntegrado()
    {
        return $this->pagamento->isIntegrado() ? '1' : '2';
    }

    /**
     * Altera o valor do Integrado para o informado no parâmetro
     *
     * @param string $integrado Novo integrado para Pagamento
     *
     * @return self A própria instância da classe
     */
    public function setIntegrado($integrado)
    {
        if (is_bool($integrado)) {
            $integrado = $integrado ? 'Y' : 'N';
        }
        $this->pagamento->setIntegrado(in_array($integrado, ['Y', '1']) ? 'Y' : 'N');
        return $this;
    }

    /**
     * Bandeira da operadora de cartão de crédito/débito:01–Visa;
     * 02–Mastercard; 03–American Express; 04–Sorocred;05-Diners
     * Club;06-Elo;07-Hipercard;08-Aura;09-Cabal;99–Outros
     *
     * @return string|null bandeira of Pagamento
     */
    public function getBandeira()
    {
        switch ($this->pagamento->getBandeira()) {
            case Pagamento::BANDEIRA_VISA:
                return '01';
            case Pagamento::BANDEIRA_MASTERCARD:
                return '02';
            case Pagamento::BANDEIRA_AMEX:
                return '03';
            case Pagamento::BANDEIRA_SOROCRED:
                return '04';
            case Pagamento::BANDEIRA_DINERS:
                return '05';
            case Pagamento::BANDEIRA_ELO:
                return '06';
            case Pagamento::BANDEIRA_HIPERCARD:
                return '07';
            case Pagamento::BANDEIRA_AURA:
                return '08';
            case Pagamento::BANDEIRA_CABAL:
                return '09';
            case Pagamento::BANDEIRA_OUTROS:
                return '99';
        }
        return $this->pagamento->getBandeira();
    }

    /**
     * Altera o valor da Bandeira para o informado no parâmetro
     *
     * @param string|null $bandeira Novo bandeira para Pagamento
     *
     * @return self A própria instância da classe
     */
    public function setBandeira($bandeira)
    {
        switch ($bandeira) {
            case '01':
                $bandeira = Pagamento::BANDEIRA_VISA;
                break;
            case '02':
                $bandeira = Pagamento::BANDEIRA_MASTERCARD;
                break;
            case '03':
                $bandeira = Pagamento::BANDEIRA_AMEX;
                break;
            case '04':
                $bandeira = Pagamento::BANDEIRA_SOROCRED;
                break;
            case '05':
                $bandeira = Pagamento::BANDEIRA_DINERS;
                break;
            case '06':
                $bandeira = Pagamento::BANDEIRA_ELO;
                break;
            case '07':
                $bandeira = Pagamento::BANDEIRA_HIPERCARD;
                break;
            case '08':
                $bandeira = Pagamento::BANDEIRA_AURA;
                break;
            case '09':
                $bandeira = Pagamento::BANDEIRA_CABAL;
                break;
            case '99':
                $bandeira = Pagamento::BANDEIRA_OUTROS;
                break;
        }
        $this->pagamento->setBandeira($bandeira);
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        if ($this->pagamento->getValor() < 0) {
            $element = $dom->createElement($name ?? 'vTroco');
            $this->pagamento->setValor(-floatval($this->pagamento->getValor()));
            $element->appendChild($dom->createTextNode($this->getValor()));
            $this->pagamento->setValor(-floatval($this->pagamento->getValor()));
            return $element;
        }
        $element = $dom->createElement($name ?? 'detPag');
        if (!is_null($this->pagamento->getIndicador())) {
            Util::appendNode($element, 'indPag', $this->getIndicador());
        }
        Util::appendNode($element, 'tPag', $this->getForma());
        Util::appendNode($element, 'vPag', $this->getValor());
        if (!$this->pagamento->isCartao()) {
            return $element;
        }
        $cartao = $dom->createElement('card');
        Util::appendNode($cartao, 'tpIntegra', $this->getIntegrado());
        if ($this->pagamento->isIntegrado()) {
            Util::appendNode($cartao, 'CNPJ', $this->pagamento->getCredenciadora());
        }
        if (!is_null($this->pagamento->getBandeira())) {
            Util::appendNode($cartao, 'tBand', $this->getBandeira());
        }
        if ($this->pagamento->isIntegrado()) {
            Util::appendNode($cartao, 'cAut', $this->pagamento->getAutorizacao());
        }
        $element->appendChild($cartao);
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'detPag';
        $element = Util::findNode($element, $name);
        if ($name == 'vTroco') {
            $this->setValor('-' . $element->nodeValue);
            return $element;
        }
        $this->setIndicador(
            Util::loadNode(
                $element,
                'indPag'
            )
        );
        $this->setForma(
            Util::loadNode(
                $element,
                'tPag',
                'Tag "tPag" do campo "Forma" não encontrada'
            )
        );
        $this->setValor(
            Util::loadNode(
                $element,
                'vPag',
                'Tag "vPag" do campo "Valor" não encontrada'
            )
        );
        $integrado = Util::loadNode($element, 'tpIntegra');
        if (is_null($integrado) && $this->pagamento->isCartao()) {
            throw new \Exception('Tag "tpIntegra" do campo "Integrado" não encontrada', 404);
        }
        $this->setIntegrado($integrado);
        $this->pagamento->setCredenciadora(Util::loadNode($element, 'CNPJ'));
        $autorizacao = Util::loadNode($element, 'cAut');
        if (is_null($autorizacao) && $this->pagamento->isCartao() && is_numeric($this->pagamento->getCredenciadora())) {
            throw new \Exception('Tag "cAut" do campo "Autorizacao" não encontrada', 404);
        }
        $this->pagamento->setAutorizacao($autorizacao);
        $bandeira = Util::loadNode($element, 'tBand');
        if (is_null($bandeira) && $this->pagamento->isCartao() && is_numeric($this->pagamento->getCredenciadora())) {
            throw new \Exception('Tag "tBand" do campo "Bandeira" não encontrada', 404);
        }
        $this->setBandeira($bandeira);
        return $element;
    }
}
