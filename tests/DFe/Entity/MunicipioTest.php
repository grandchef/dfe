<?php

namespace DFe\Entity;

class MunicipioTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp(): void
    {
        $this->sefaz = \DFe\Core\SEFAZ::getInstance(true);
    }

    public function testMunicipio()
    {
        $municipio = new \DFe\Entity\Municipio();
        $municipio->setNome('Paranavaí');
        $estado = new \DFe\Entity\Estado();
        $estado->setNome('Paraná');
        $estado->setUF('PR');
        $municipio->setEstado($estado);
        $municipio->checkCodigos();
        $municipio->fromArray($municipio);
        $municipio->fromArray($municipio->toArray());
        $municipio->fromArray(null);

        $this->assertEquals(4118402, $municipio->getCodigo());
        $this->assertEquals('Paranavaí', $municipio->getNome());
    }
}
