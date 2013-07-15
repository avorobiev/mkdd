<?php
namespace Maxposter\DacTestBundle\Tests;

use \Maxposter\DacBundle\Dac\Dac;
use \Maxposter\DacBundle\Dac\Settings;

class DepartmentTest extends AppTestCase
{
    public function testSelect_UserWithoutDepartments()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();

        $dacSettings = new Settings();
        $dacSettings->set('Maxposter\\DacTestBundle\\Entity\\Department', array());

        $dac = new Dac($this->container->get('doctrine'));
        $dac->setSettings($dacSettings);
        $dac->enable();

        $res = $this->em->createQuery('SELECT d FROM MaxposterDacTestBundle:Department d')->getResult();

        $this->assertInternalType('array', $res);
        $this->assertEquals(0, count($res));
    }

    public function testSelect_UserWithDepartment()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();

        $dacSettings = new Settings();
        $dacSettings->set('Maxposter\\DacTestBundle\\Entity\\Department', array($department1->getId()));

        $dac = new Dac($this->container->get('doctrine'));
        $dac->setSettings($dacSettings);
        $dac->enable();

        $res = $this->em->createQuery('SELECT d FROM MaxposterDacTestBundle:Department d')->getResult();

        $this->assertInternalType('array', $res);
        $this->assertEquals(1, count($res));
        $this->assertEquals($department1->getId(), $res[0]->getId(), 'Выбрана запись с верным идентификатором');
    }

    public function testSelect_UserWithDepartments()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();
        $department3 = $this->helper->makeDepartment();
        $departmentIds = array($department1->getId(), $department3->getId());

        $dacSettings = new Settings();
        $dacSettings->set('Maxposter\\DacTestBundle\\Entity\\Department', $departmentIds);

        $dac = new Dac($this->container->get('doctrine'));
        $dac->setSettings($dacSettings);
        $dac->enable();

        $res = $this->em->createQuery('SELECT d FROM MaxposterDacTestBundle:Department d')->getResult();

        $this->assertInternalType('array', $res);
        $this->assertEquals(2, count($res), 'Выбраны две записи');
        $this->assertTrue(in_array($res[0]->getId(), $departmentIds), 'Идентификатор первой записи соответствует условиям выборки');
        $this->assertTrue(in_array($res[1]->getId(), $departmentIds), 'Идентификатор первой записи соответствует условиям выборки');
    }

    public function testSelect_UserWithDepartmentsButWithoutDealer()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();
        $department3 = $this->helper->makeDepartment();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Dealer' => array(),
            'Maxposter\\DacTestBundle\\Entity\\Department' => array(
                $department1->getId(),
                $department3->getId()
            ),
        ));

        $dac = new Dac($this->container->get('doctrine'));
        $dac->setSettings($dacSettings);
        $dac->enable();

        $res = $this->em->createQuery('SELECT d FROM MaxposterDacTestBundle:Department d')->getResult();

        $this->assertInternalType('array', $res);
        $this->assertEquals(0, count($res), 'Все записи срезаны фильтром по Dealer');
    }
}