<?php

/**
 * Copyright (c) 2016 GrandChef Desenvolvimento de Sistemas LTDA. All rights reserved.
 *
 * @author Equipe GrandChef <desenvolvimento@grandchef.com.br>
 *
 * This work is licensed under the terms of the MIT license.
 * For a copy, see <https://opensource.org/licenses/MIT>.
 */

namespace  DFe\Loader\CFe;

use DFe\Common\Util;
use DFe\Common\Loader;
use DFe\Entity\Pagamento;
use Exception;

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


    /**
     * CNPJ da credenciadora de cartão de crédito/débito
     *
     * @return string credenciadora of Pagamento
     */
    public function getCredenciadora()
    {
        switch ($this->pagamento->getCredenciadora()) {
            case '03106213000190':
                return '001';
            case '03106213000271':
                return '002';
            case '60419645000195':
                return '003';
            case '62421979000129':
                return '004';
            case '58160789000128':
                return '005';
            case '07679404000100':
                return '006';
            case '17351180000159':
                return '007';
            case '04627085000193':
                return '008';
            case '01418852000166':
                return '009';
            case '03766873000106':
                return '010';
            case '03722919000187':
                return '011';
            case '01027058000191':
                return '012';
            case '03529067000106':
                return '013';
            case '71225700000122':
                return '014';
            case '03506307000157':
                return '015';
            case '04432048000120':
                return '016';
            case '07953674000150':
                return '017';
            case '03322366000175':
                return '018';
            case '03012230000169':
                return '019';
            case '03966317000175':
                return '020';
            case '00163051000134':
                return '021';
            case '43180355000112':
                return '022';
            case '00904951000195':
                return '023';
            case '33098658000137':
                return '024';
            case '01425787000104':
                return '025';
            case '90055609000150':
                return '026';
            case '03007699000100':
                return '027';
            case '00122327000136':
                return '028';
            case '69034668000156':
                return '029';
            case '60114865000100':
                return '030';
            case '51427102000471':
                return '031';
            case '47866934000174':
                return '032';
            case '00604122000197':
                return '033';
            case '61071387000161':
                return '034';
            default:
                return '999';
        }
    }

    /**
     * Altera o valor da Credenciadora para o informado no parâmetro
     *
     * @param string|null $credenciadora Novo credenciadora para Pagamento
     *
     * @return self A própria instância da classe
     */
    public function setCredenciadora($credenciadora)
    {
        switch ($credenciadora) {
            case '001':
                $credenciadora = '03106213000190';
                break;
            case '002':
                $credenciadora = '03106213000271';
                break;
            case '003':
                $credenciadora = '60419645000195';
                break;
            case '004':
                $credenciadora = '62421979000129';
                break;
            case '005':
                $credenciadora = '58160789000128';
                break;
            case '006':
                $credenciadora = '07679404000100';
                break;
            case '007':
                $credenciadora = '17351180000159';
                break;
            case '008':
                $credenciadora = '04627085000193';
                break;
            case '009':
                $credenciadora = '01418852000166';
                break;
            case '010':
                $credenciadora = '03766873000106';
                break;
            case '011':
                $credenciadora = '03722919000187';
                break;
            case '012':
                $credenciadora = '01027058000191';
                break;
            case '013':
                $credenciadora = '03529067000106';
                break;
            case '014':
                $credenciadora = '71225700000122';
                break;
            case '015':
                $credenciadora = '03506307000157';
                break;
            case '016':
                $credenciadora = '04432048000120';
                break;
            case '017':
                $credenciadora = '07953674000150';
                break;
            case '018':
                $credenciadora = '03322366000175';
                break;
            case '019':
                $credenciadora = '03012230000169';
                break;
            case '020':
                $credenciadora = '03966317000175';
                break;
            case '021':
                $credenciadora = '00163051000134';
                break;
            case '022':
                $credenciadora = '43180355000112';
                break;
            case '023':
                $credenciadora = '00904951000195';
                break;
            case '024':
                $credenciadora = '33098658000137';
                break;
            case '025':
                $credenciadora = '01425787000104';
                break;
            case '026':
                $credenciadora = '90055609000150';
                break;
            case '027':
                $credenciadora = '03007699000100';
                break;
            case '028':
                $credenciadora = '00122327000136';
                break;
            case '029':
                $credenciadora = '69034668000156';
                break;
            case '030':
                $credenciadora = '60114865000100';
                break;
            case '031':
                $credenciadora = '51427102000471';
                break;
            case '032':
                $credenciadora = '47866934000174';
                break;
            case '033':
                $credenciadora = '00604122000197';
                break;
            case '034':
                $credenciadora = '61071387000161';
                break;
            default:
                $credenciadora = null;
        }
        $this->pagamento->setCredenciadora($credenciadora);
        return $this;
    }

    public function getNode(string $version = '', ?string $name = null): \DOMElement
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        if ($this->pagamento->getValor() < 0) {
            throw new Exception('O valor do troco é calculado automaticamente pelo SAT');
        }
        $element = $dom->createElement($name ?? 'MP');
        Util::appendNode($element, 'cMP', $this->getForma());
        Util::appendNode($element, 'vMP', $this->getValor());
        if (!$this->pagamento->isCartao()) {
            return $element;
        }
        if (!is_null($this->pagamento->getCredenciadora())) {
            Util::appendNode($element, 'cAdmC', $this->getCredenciadora());
        }
        if (!is_null($this->pagamento->getAutorizacao())) {
            Util::appendNode($element, 'cAut', $this->pagamento->getAutorizacao());
        }
        return $element;
    }

    public function loadNode(\DOMElement $element, ?string $name = null, string $version = ''): \DOMElement
    {
        $name ??= 'MP';
        $element = Util::findNode($element, $name);
        if ($name == 'vTroco') {
            $this->setValor('-' . $element->nodeValue);
            return $element;
        }
        $this->setForma(
            Util::loadNode(
                $element,
                'cMP',
                'Tag "cMP" do campo "Forma" não encontrada'
            )
        );
        $this->setValor(
            Util::loadNode(
                $element,
                'vMP',
                'Tag "vMP" do campo "Valor" não encontrada'
            )
        );
        $credenciadora = Util::loadNode($element, 'cAdmC');
        $this->setCredenciadora($credenciadora);
        $autorizacao = Util::loadNode($element, 'cAut');
        $this->pagamento->setAutorizacao($autorizacao);
        return $element;
    }
}
