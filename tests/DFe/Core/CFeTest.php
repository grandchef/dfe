<?php

namespace DFe\Core;

class CFeTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        \DFe\Core\SEFAZTest::createSEFAZ();
    }

    public static function createCFe($sefaz, $simples = false)
    {
        $cfe = new \DFe\Core\CFe();
        $cfe->setCodigo('77882192');
        $cfe->setSerie('1');
        $cfe->setNumero('81');
        $cfe->getCaixa()->setNumero('1');
        $cfe->setDataEmissao(strtotime('2016-09-16T21:36:03-03:00'));
        $cfe->setPresenca(\DFe\Core\Nota::PRESENCA_PRESENCIAL);
        $cfe->addObservacao('Vendedor', 'Fulano de Tal');
        $cfe->addObservacao('Local', 'Mesa 02');
        $cfe->addInformacao('RegimeEspecial', '123456');

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
        $cfe->setEmitente($emitente);

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
        $cfe->setDestinatario($destinatario);

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
        $cfe->setResponsavel($responsavel);

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
        $cfe->addProduto($produto);

        /* Impostos */
        if ($simples) {
            $imposto = new \DFe\Entity\Imposto\ICMS\Simples\Isento();
            $imposto->fromArray($imposto);
            $imposto->fromArray($imposto->toArray());
            $imposto->fromArray(null);
            $produto->addImposto($imposto);
        } else {
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
        }
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
        $cfe->addProduto($produto);

        /* Impostos */
        if ($simples) {
            $imposto = new \DFe\Entity\Imposto\ICMS\Simples\Isento();
            $imposto->fromArray($imposto);
            $imposto->fromArray($imposto->toArray());
            $imposto->fromArray(null);
            $produto->addImposto($imposto);
        } else {
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
        }
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
        $cfe->addPagamento($pagamento);

        $pagamento = new \DFe\Entity\Pagamento();
        $pagamento->setForma(\DFe\Entity\Pagamento::FORMA_DINHEIRO);
        $pagamento->setValor(9.49);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);
        $cfe->addPagamento($pagamento);

        $cfe->fromArray($cfe);
        $cfe->fromArray($cfe->toArray());
        $cfe->fromArray(null);
        return $cfe;
    }

    public static function createCFeTroco($sefaz)
    {
        $cfe = self::createCFe($sefaz);

        $pagamento = new \DFe\Entity\Pagamento();
        $pagamento->setForma(\DFe\Entity\Pagamento::FORMA_DINHEIRO);
        $pagamento->setValor(5);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);
        $cfe->addPagamento($pagamento);

        $pagamento = new \DFe\Entity\Pagamento();
        $pagamento->setValor(-5);
        $pagamento->fromArray($pagamento);
        $pagamento->fromArray($pagamento->toArray());
        $pagamento->fromArray(null);
        $cfe->addPagamento($pagamento);
        return $cfe;
    }

    public static function createCFeIntermediador($sefaz)
    {
        $cfe = self::createCFe($sefaz);

        $cfe->setIntermediacao(\DFe\Core\Nota::INTERMEDIACAO_TERCEIROS);
        $intermediador = new \DFe\Entity\Intermediador();
        $intermediador->setCNPJ('14380200000121');
        $intermediador->setIdentificador('iFood');
        $cfe->setIntermediador($intermediador);
        return $cfe;
    }

    public function testLoadXML()
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testCFeResponse.xml';

        $cfe = new \DFe\Core\CFe();
        $cfe->load($xml_file);

        $this->assertEquals($cfe->getID(), '35161261099008000141599000026310003024947916');
    }

    public function testTrocoLoadXML()
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testCFeTrocoResponse.xml';

        $cfe = new \DFe\Core\CFe();
        $cfe->load($xml_file);

        $this->assertEquals($cfe->getID(), '35161261099008000141599000026310003024947916');
    }

    public function testIntermediadorLoadXML()
    {
        $xml_file = dirname(dirname(__DIR__)) . '/resources/xml/nota/testCFeIntermediadorResponse.xml';

        $cfe = new \DFe\Core\CFe();
        $cfe->load($xml_file);

        $this->assertEquals($cfe->getID(), '35161261099008000141599000026310003024947916');
    }

    public function testLoadFail()
    {
        $cfe = new \DFe\Core\CFe();
        $this->expectException('\Exception');
        $cfe->load('invalido.xml');
    }
}
