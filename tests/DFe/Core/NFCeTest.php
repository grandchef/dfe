<?php

namespace DFe\Core;

class NFCeTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp(): void
    {
        $this->sefaz = \DFe\Core\SEFAZTest::createSEFAZ();
    }

    public static function createNFCe($sefaz)
    {
        $nfce = new \DFe\Core\NFCe();
        $nfce->setCodigo('77882192');
        $nfce->setSerie('1');
        $nfce->setNumero('81');
        $nfce->setDataEmissao(strtotime('2016-09-16T21:36:03-03:00'));
        $nfce->setPresenca(\DFe\Core\Nota::PRESENCA_PRESENCIAL);
        $nfce->addObservacao('Vendedor', 'Fulano de Tal');
        $nfce->addObservacao('Local', 'Mesa 02');
        $nfce->addInformacao('RegimeEspecial', '123456');

        /* Emitente */
        $emitente = new \DFe\Entity\Emitente();
        $emitente->setRazaoSocial('Empresa LTDA');
        $emitente->setFantasia('Minha Empresa');
        $emitente->setCNPJ('08380787000176');
        $emitente->setTelefone('11955886644');
        $emitente->setIE('123456789');
        $emitente->setIM('98765');
        $emitente->setRegime(\DFe\Entity\Emitente::REGIME_SIMPLES);

        $endereco = new \DFe\Entity\Endereco();
        $endereco->setCEP('01122500');
        $endereco->getMunicipio()
            ->setNome('Paranavaí')
            ->getEstado()
            ->setUF('PR');
        $endereco->setBairro('Centro');
        $endereco->setLogradouro('Rua Paranavaí');
        $endereco->setNumero('123');
        $endereco->fromArray($endereco);
        $endereco->fromArray($endereco->toArray());
        $endereco->fromArray(null);

        $emitente->setEndereco($endereco);
        $emitente->fromArray($emitente);
        $emitente->fromArray($emitente->toArray());
        $emitente->fromArray(null);
        $sefaz->getConfiguracao()->setEmitente($emitente);
        $nfce->setEmitente($emitente);

        /* Destinatário */
        $destinatario = new \DFe\Entity\Destinatario();
        $destinatario->setNome('Fulano da Silva');
        $destinatario->setCPF('12345678912');
        $destinatario->setEmail('fulano@site.com.br');
        $destinatario->setTelefone('11988220055');

        $endereco = new \DFe\Entity\Endereco();
        $endereco->setCEP('01122500');
        $endereco->getMunicipio()
            ->setNome('Paranavaí')
            ->getEstado()
            ->setUF('PR');
        $endereco->setBairro('Centro');
        $endereco->setLogradouro('Rua Paranavaí');
        $endereco->setNumero('123');
        $endereco->fromArray($endereco);
        $endereco->fromArray($endereco->toArray());
        $endereco->fromArray(null);

        $destinatario->setEndereco($endereco);
        $destinatario->fromArray($destinatario);
        $destinatario->fromArray($destinatario->toArray());
        $destinatario->fromArray(null);
        $nfce->setDestinatario($destinatario);

        /* Responsável */
        $responsavel = new \DFe\Entity\Responsavel();
        $responsavel->setCNPJ('12345678000123');
        $responsavel->setContato('Empresa LTDA');
        $responsavel->setEmail('contato@empresa.com.br');
        $responsavel->setTelefone('11988220055');
        $responsavel->setIdentificador(99);
        $responsavel->setAssinatura('aWv6LeEM4X6u4+qBl2OYZ8grigw=');

        $responsavel->fromArray($responsavel);
        $responsavel->fromArray($responsavel->toArray());
        $responsavel->fromArray(null);
        $nfce->setResponsavel($responsavel);

        /* Produtos */
        $produto = new \DFe\Entity\Produto();
        $produto->setCodigo(123456);
        $produto->setCodigoBarras('7894900011531');
        $produto->setDescricao('REFRIGERANTE COCA-COLA 2L');
        $produto->setUnidade(\DFe\Entity\Produto::UNIDADE_UNIDADE);
        $produto->setPreco(4.99);
        $produto->setQuantidade(1);
        $produto->setNCM('22021000');
        $produto->setCEST('0300700');
        $produto->setCFOP('5405');
        $nfce->addProduto($produto);

        /* Impostos */
        $imposto = new \DFe\Entity\Imposto\ICMS\Cobrado();
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);

        $imposto = new \DFe\Entity\Imposto\PIS\Aliquota();
        $imposto->setTributacao(\DFe\Entity\Imposto\PIS\Aliquota::TRIBUTACAO_NORMAL);
        $imposto->setAliquota(0.65);
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);

        $imposto = new \DFe\Entity\Imposto\COFINS\Aliquota();
        $imposto->setTributacao(\DFe\Entity\Imposto\COFINS\Aliquota::TRIBUTACAO_NORMAL);
        $imposto->setAliquota(3.00);
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);
        $produto->fromArray($produto);
        $produto->fromArray($produto->toArray());
        $produto->fromArray(null);

        $produto = new \DFe\Entity\Produto();
        $produto->setCodigo(123456);
        $produto->setCodigoBarras('7894900011523');
        $produto->setDescricao('REFRIGERANTE FANTA LARANJA 2L');
        $produto->setUnidade(\DFe\Entity\Produto::UNIDADE_UNIDADE);
        $produto->setPreco(9.00);
        $produto->setQuantidade(2);
        $produto->setDesconto(2.20);
        $produto->setNCM('22021000');
        $produto->setCEST('0300700');
        $produto->setCFOP('5405');
        $nfce->addProduto($produto);

        /* Impostos */
        $imposto = new \DFe\Entity\Imposto\ICMS\Cobrado();
        $imposto->setBase(0.00);
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);

        $imposto = new \DFe\Entity\Imposto\PIS\Aliquota();
        $imposto->setTributacao(\DFe\Entity\Imposto\PIS\Aliquota::TRIBUTACAO_NORMAL);
        $imposto->setAliquota(0.65);
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);

        $imposto = new \DFe\Entity\Imposto\COFINS\Aliquota();
        $imposto->setTributacao(\DFe\Entity\Imposto\COFINS\Aliquota::TRIBUTACAO_NORMAL);
        $imposto->setAliquota(3.00);
        $imposto->fromArray($imposto);
        $imposto->fromArray($imposto->toArray());
        $imposto->fromArray(null);
        $produto->addImposto($imposto);
        $produto->fromArray($produto);
        $produto->fromArray($produto->toArray());
        $produto->fromArray(null);

        /* Pagamentos */
        $pagamento = new \DFe\Entity\Pagamento();
        $pagamento->setForma(\DFe\Entity\Pagamento::FORMA_CREDITO);
        $pagamento->setValor(4.50);
        $pagamento->setIntegrado('N');
        $pagamento->setBandeira(\DFe\Entity\Pagamento::BANDEIRA_MASTERCARD);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);
        $nfce->addPagamento($pagamento);

        $pagamento = new \DFe\Entity\Pagamento();
        $pagamento->setForma(\DFe\Entity\Pagamento::FORMA_DINHEIRO);
        $pagamento->setValor(9.49);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);
        $nfce->addPagamento($pagamento);

        $nfce->fromArray($nfce);
        $nfce->fromArray($nfce->toArray());
        $nfce->fromArray(null);
        return $nfce;
    }

    public static function createTrocoNFCe($sefaz)
    {
        $nfce = self::createNFCe($sefaz);

        $pagamento = new \DFe\Entity\Pagamento();
        $pagamento->setForma(\DFe\Entity\Pagamento::FORMA_DINHEIRO);
        $pagamento->setValor(5);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);
        $nfce->addPagamento($pagamento);

        $pagamento = new \DFe\Entity\Pagamento();
        $pagamento->setValor(-5);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);
        $nfce->addPagamento($pagamento);
        return $nfce;
    }

    public static function createIntermediadorNFCe($sefaz)
    {
        $nfce = self::createNFCe($sefaz);

        $nfce->setIntermediacao(\DFe\Core\Nota::INTERMEDIACAO_TERCEIROS);
        $intermediador = new \DFe\Entity\Intermediador();
        $intermediador->setCNPJ('14380200000121');
        $intermediador->setIdentificador('iFood');
        $nfce->setIntermediador($intermediador);
        return $nfce;
    }

    public static function loadNFCeXML()
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testNFCeXML.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);
        return $dom_cmp;
    }

    public static function loadTrocoNFCeXMLValidada()
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testTrocoNFCeValidadaXML.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $nfce = new \DFe\Core\NFCe();
        $nfce->load($xml_file);
        $dom = $nfce->assinar(); // O carregamento (load) não carrega assinatura
        $dom = $nfce->validar($dom);
        return [
            'nota' => $nfce,
            'dom' => $dom,
            'cmp' => $dom_cmp
        ];
    }

    public static function loadIntermediadorNFCeXMLValidada()
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testIntermediadorNFCeValidadaXML.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);

        $nfce = new \DFe\Core\NFCe();
        $nfce->load($xml_file);
        $dom = $nfce->assinar(); // O carregamento (load) não carrega assinatura
        $dom = $nfce->validar($dom);
        return [
            'nota' => $nfce,
            'dom' => $dom,
            'cmp' => $dom_cmp
        ];
    }

    public static function loadNFCeXMLAssinado()
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testNFCeAssinadaXML.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);
        return $dom_cmp;
    }

    public static function loadNFCeXMLAutorizado()
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testNFCeAutorizadoXML.xml';
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($xml_file);
        return $dom_cmp;
    }

    public static function loadNFCeAssinada()
    {
        $dom_cmp = self::loadNFCeXMLAssinado();

        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testNFCeAssinadaXML.xml';
        $nfce = new \DFe\Core\NFCe();
        $nfce->load($xml_file);

        $dom = $nfce->assinar(); // O carregamento (load) não carrega assinatura
        return [
            'nota' => $nfce,
            'dom' => $dom,
            'cmp' => $dom_cmp
        ];
    }

    public static function loadNFCeValidada()
    {
        $data = self::loadNFCeAssinada();
        $nfce = $data['nota'];
        $dom = $data['dom'];
        $dom_cmp = $data['cmp'];

        $dom = $nfce->validar($dom);
        return [
            'nota' => $nfce,
            'dom' => $dom,
            'cmp' => $dom_cmp
        ];
    }

    public static function loadNFCeAutorizada()
    {
        $dom_cmp = self::loadNFCeXMLAutorizado();

        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testNFCeAutorizadoXML.xml';
        $nfce = new \DFe\Core\NFCe();
        $nfce->load($xml_file);

        $protocolo = $nfce->getProtocolo();
        $protocolo->fromArray($protocolo);
        $protocolo->fromArray($protocolo->toArray());
        $protocolo->fromArray(null);
        $nfce->setProtocolo(null);
        $dom = $nfce->assinar(); // O carregamento (load) não carrega assinatura
        $dom = $nfce->validar($dom);
        $nfce->setProtocolo($protocolo);
        $dom = $nfce->addProtocolo($dom);
        return [
            'nota' => $nfce,
            'dom' => $dom,
            'cmp' => $dom_cmp
        ];
    }

    public function testNFCeXML()
    {
        $nfce = self::createNFCe($this->sefaz);
        $xml = $nfce->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(dirname(dirname(__DIR__)) . '/resources/xml/nota/testNFCeXML.xml', $dom->saveXML());
        }

        $dom_cmp = self::loadNFCeXML();
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testTrocoNFCeValidadaXML()
    {
        $nfce = self::createTrocoNFCe($this->sefaz);
        $xml = $nfce->getNode();
        $dom = $xml->ownerDocument;
        $dom = $nfce->assinar($dom);
        $dom = $nfce->validar($dom);

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/nota/testTrocoNFCeValidadaXML.xml',
                $dom->saveXML()
            );
        }

        $data = self::loadTrocoNFCeXMLValidada();
        $dom_cmp = $data['cmp'];
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testIntermediadorNFCeValidadaXML()
    {
        $nfce = self::createIntermediadorNFCe($this->sefaz);
        $xml = $nfce->getNode();
        $dom = $xml->ownerDocument;
        $dom = $nfce->assinar($dom);
        $dom = $nfce->validar($dom);

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/nota/testIntermediadorNFCeValidadaXML.xml',
                $dom->saveXML()
            );
        }

        $data = self::loadIntermediadorNFCeXMLValidada();
        $dom_cmp = $data['cmp'];
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testTrocoNFCeAssinadaLoadXML()
    {
        $data = self::loadTrocoNFCeXMLValidada();
        $dom = $data['dom'];
        $dom_cmp = $data['cmp'];

        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testNFCeAssinadaXML()
    {
        $nfce = self::createNFCe($this->sefaz);
        $xml = $nfce->getNode();
        $dom = $xml->ownerDocument;
        $dom = $nfce->assinar($dom);

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/nota/testNFCeAssinadaXML.xml',
                $dom->saveXML()
            );
        }

        $dom_cmp = self::loadNFCeXMLAssinado();
        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testNFCeLoadFail()
    {
        $nfce = new \DFe\Core\NFCe();
        $this->expectException('\Exception');
        $nfce->load('invalido.xml');
    }

    public function testNFCeLoadXML()
    {
        $dom_cmp = self::loadNFCeXML();

        $nfce = new \DFe\Core\NFCe();
        $nfce->loadNode($dom_cmp->documentElement);

        $xml = $nfce->getNode();
        $dom = $xml->ownerDocument;

        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testNFCeAssinadaLoadXML()
    {
        $data = self::loadNFCeAssinada();
        $dom = $data['dom'];
        $dom_cmp = $data['cmp'];

        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }

    public function testNFCeAutorizadoLoadXML()
    {
        $data = self::loadNFCeAutorizada();
        $dom = $data['dom'];
        $dom_cmp = $data['cmp'];

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(__DIR__)) . '/resources/xml/nota/testNFCeAutorizadoXML.xml',
                $dom->saveXML()
            );
        }

        $this->assertXmlStringEqualsXmlString($dom_cmp->saveXML(), $dom->saveXML());
    }
}
