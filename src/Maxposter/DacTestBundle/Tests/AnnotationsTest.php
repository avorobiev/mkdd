<?php
namespace Maxposter\DacTestBundle\Tests;

class AnnotationsTest extends AppTestCase
{
    public function testDacService_HasField()
    {
        $ann = $this->container->get('maxposter.dac.annotations');

        $this->assertTrue($ann->hasDacFields('Maxposter\\DacTestBundle\\Entity\\Department'));
    }


    public function testDacService_HasNoField()
    {
        $ann = $this->container->get('maxposter.dac.annotations');

        $this->assertFalse($ann->hasDacFields('Maxposter\\DacTestBundle\\Entity\\City'));
    }


    public function testDacService_GetFields()
    {
        $ann = $this->container->get('maxposter.dac.annotations');

        $this->assertEquals(
            array(
                'id' => 'Maxposter\DacTestBundle\Entity\Department',
                'dealer' => 'Maxposter\DacTestBundle\Entity\Dealer'
            ),
            $ann->getDacFields('Maxposter\\DacTestBundle\\Entity\\Department')
        );
    }

}