<?php

namespace DFe\Entity;

class LacreTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
    }

    public function testLacre()
    {
        $lacre = new \DFe\Entity\Lacre();
        $lacre->setNumero(123);
        $lacre->fromArray($lacre);
        $lacre->fromArray($lacre->toArray());
        $lacre->fromArray(null);

        $this->assertEquals(123, $lacre->getNumero());
    }
}
