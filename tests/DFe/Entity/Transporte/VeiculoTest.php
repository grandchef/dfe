<?php

namespace DFe\Entity\Transporte;

class VeiculoTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
    }

    public function testVeiculoXML()
    {
        $veiculo = new \DFe\Entity\Transporte\Veiculo();
        $veiculo->setPlaca('KLM1234');
        $veiculo->setUF('PI');
        $veiculo->setRNTC('123456');

        $veiculo->fromArray($veiculo);
        $veiculo->fromArray($veiculo->toArray());
        $veiculo->fromArray(null);

        $xml = $veiculo->getNode();
        $dom = $xml->ownerDocument;

        if (getenv('TEST_MODE') == 'override') {
            $dom->formatOutput = true;
            file_put_contents(
                dirname(dirname(dirname(__DIR__))) . '/resources/xml/transporte/testVeiculoXML.xml',
                $dom->saveXML($xml)
            );
        }

        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(dirname(__DIR__))) . '/resources/xml/transporte/testVeiculoXML.xml');
        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }

    public function testVeiculoLoadXML()
    {
        $dom_cmp = new \DOMDocument();
        $dom_cmp->preserveWhiteSpace = false;
        $dom_cmp->load(dirname(dirname(dirname(__DIR__))) . '/resources/xml/transporte/testVeiculoXML.xml');

        $veiculo = new \DFe\Entity\Transporte\Veiculo();
        $veiculo->loadNode($dom_cmp->documentElement);

        $xml = $veiculo->getNode();
        $dom = $xml->ownerDocument;

        $xml_cmp = $dom_cmp->saveXML($dom_cmp->documentElement);
        $this->assertXmlStringEqualsXmlString($xml_cmp, $dom->saveXML($xml));
    }
}
