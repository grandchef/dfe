<?php

namespace DFe\Entity\Imposto\ICMS\Simples;

class CobrancaTest extends \PHPUnit\Framework\TestCase
{
    private $resource_path;

    protected function setUp(): void
    {
        $this->resource_path = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . '/resources';
    }

    public function testCobrancaXML()
    {
        // TODO: verificar vICMSST = 12.96
        $icms_cobranca = new Cobranca();
        $icms_cobranca->getNormal()->setModalidade(\DFe\Entity\Imposto\ICMS\Normal::MODALIDADE_OPERACAO);
        $icms_cobranca->getNormal()->setBase(1036.80);
        $icms_cobranca->getNormal()->setAliquota(1.25);
        $icms_cobranca->setModalidade(\DFe\Entity\Imposto\ICMS\Parcial::MODALIDADE_AGREGADO);
        $icms_cobranca->setBase(162.00);
        $icms_cobranca->setMargem(100.00);
        $icms_cobranca->setReducao(10.00);
        $icms_cobranca->setAliquota(18.00);
        $icms_cobranca->fromArray($icms_cobranca);
        $icms_cobranca->fromArray($icms_cobranca->toArray());
        $icms_cobranca->fromArray(null);

        $xml = $icms_cobranca->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                $this->resource_path . '/xml/imposto/icms/simples/testCobrancaXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/simples/testCobrancaXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testCobrancaLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load($this->resource_path . '/xml/imposto/icms/simples/testCobrancaXML.xml');

        $icms_cobranca = Cobranca::loadImposto($dom_cmp->documentElement);
        $this->assertInstanceOf(Cobranca::class, $icms_cobranca);

        $xml = $icms_cobranca->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
