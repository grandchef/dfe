<?php

namespace DFe\Entity;

class EstadoTest extends \PHPUnit\Framework\TestCase
{
    private $sefaz;

    protected function setUp(): void
    {
        $this->sefaz = \DFe\Core\SEFAZ::getInstance(true);
    }

    public function testEstado()
    {
        $estado = new \DFe\Entity\Estado();
        $estado->setNome('Paraná');
        $estado->setUF('PR');
        $estado->checkCodigos();
        $estado->fromArray($estado);
        $estado->fromArray($estado->toArray());
        $estado->fromArray(null);

        $this->assertEquals(41, $estado->getCodigo());
        $this->assertEquals('Paraná', $estado->getNome());
        $this->assertEquals('Paraná', $estado->getNome(true));
        $this->assertEquals('PR', $estado->getUF());
    }
}
