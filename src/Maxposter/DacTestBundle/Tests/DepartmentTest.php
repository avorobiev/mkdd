<?php
namespace Maxposter\DacTestBundle\Tests;

class DepartmentTest extends AppTestCase
{
    public function testSelect_UserWithoutDepartments()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();

        $dac = $this->getMock('\\Maxposter\\DacBundle\\Dac\\Dac', array('getDacSettings'), array($this->container->get('doctrine')));
        $dac->expects($this->any())
            ->method('getDacSettings')
            ->will($this->returnValue(array(
                'Maxposter\\DacTestBundle\\Entity\\Business'   => '',
                'Maxposter\\DacTestBundle\\Entity\\Company'    => '',
                'Maxposter\\DacTestBundle\\Entity\\Dealer'     => '',
                'Maxposter\\DacTestBundle\\Entity\\Department' => '',
            )))
        ;
        $dac->enable();

        $res = $this->em->createQuery('SELECT d FROM MaxposterDacTestBundle:Department d')->getResult();

        $this->assertInternalType('array', $res);
        $this->assertEquals(0, count($res));
    }
}