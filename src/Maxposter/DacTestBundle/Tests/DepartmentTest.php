<?php
namespace Maxposter\DacTestBundle\Tests;

use \Maxposter\DacBundle\Dac\Dac;
use \Maxposter\DacTestBundle\Dac\Settings;

class DepartmentTest extends AppTestCase
{
    public function testSelect_UserWithoutDepartments()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();

        $dacSettings = new Settings();
        $dacSettings->set('Maxposter\\DacTestBundle\\Entity\\Department', array());

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
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

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
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

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
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

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $res = $this->em->createQuery('SELECT d FROM MaxposterDacTestBundle:Department d')->getResult();

        $this->assertInternalType('array', $res);
        $this->assertEquals(2, count($res), 'Отобраны 2 записи с фильтром по Department, фильтр по Dealer ничего не добавил');
    }


    /**
     * Пользователь без прав
     *
     * @test
     */
    public function testUpdate_UserWithoutDepartments()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();

        $dacSettings = new Settings();
        $dacSettings->set('Maxposter\\DacTestBundle\\Entity\\Department', array());

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();
        $this->em->clear();

        $dql = 'UPDATE MaxposterDacTestBundle:Department d SET d.name = \'changed\'';
        $q = $this->em->createQuery($dql);
        $res = $q->getResult();

        $this->assertStringEndsWith('WHERE (1=2)', $q->getSQL());
        $this->assertInternalType('integer', $res);
        $this->assertSame(0, $res);
    }


    /**
     * Пользователь с одним авто-подразделением
     *
     * @test
     */
    public function testUpdate_UserWithDepartment()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Department' => array(
                $department1->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();
        $this->em->clear();

        $dql = 'UPDATE MaxposterDacTestBundle:Department d SET d.name = \'changed\'';
        $q = $this->em->createQuery($dql);
        $uRes = $q->execute();

        $this->assertStringEndsWith('WHERE (((test_dac_department.id IN (\''.$department1->getId().'\'))))', $q->getSQL());
        $this->assertInternalType('integer', $uRes);
        $this->assertSame(1, $uRes);

        $dac->disable();
        $dql = 'SELECT d FROM MaxposterDacTestBundle:Department d WHERE d.name = \'changed\'';
        $sRes = $this->em->createQuery($dql)->getResult();

        $this->assertInternalType('array', $sRes);
        $this->assertEquals(1, count($sRes), 'Только 1 запись изменилась');
    }


    /**
     * Пользователь с несколькими авто-подразделениями
     *
     * @test
     */
    public function testUpdate_UserWithDepartments()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();
        $department3 = $this->helper->makeDepartment();
        $departmentIds = array($department1->getId(), $department3->getId());

        $dacSettings = new Settings();
        $dacSettings->set('Maxposter\\DacTestBundle\\Entity\\Department', $departmentIds);

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();
        $this->em->clear();

        $dql = 'UPDATE MaxposterDacTestBundle:Department d SET d.name = \'changed\'';
        $q = $this->em->createQuery($dql);
        $uRes = $q->execute();

        $this->assertStringEndsWith('WHERE (((test_dac_department.id IN (\''.implode('\', \'', $departmentIds).'\'))))', $q->getSQL());
        $this->assertInternalType('integer', $uRes);
        $this->assertSame(2, $uRes, 'Две записи обновлены');

        $dac->disable();
        $dql = 'SELECT d FROM MaxposterDacTestBundle:Department d WHERE d.name = \'changed\'';
        $sRes = $this->em->createQuery($dql)->getResult();

        $this->assertInternalType('array', $sRes);
        $this->assertEquals(2, count($sRes), 'Две записи изменены');
        $this->assertTrue(in_array($sRes[0]->getId(), $departmentIds), 'Идентификатор первой записи');
        $this->assertTrue(in_array($sRes[1]->getId(), $departmentIds), 'Идентификатор второй записи');
    }


    /**
     * Пользователь с несколькими авто-подразделениями и без автосалона
     *
     * @test
     */
    public function testUpdate_UserWithDepartmentsButWithoutDealer()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();
        $department3 = $this->helper->makeDepartment();
        $departmentIds = array(
            $department1->getId(),
            $department3->getId()
        );

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Dealer' => array(),
            'Maxposter\\DacTestBundle\\Entity\\Department' => $departmentIds,
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();
        $this->em->clear();

        $dql = 'UPDATE MaxposterDacTestBundle:Department d SET d.name = \'changed\'';
        $q = $this->em->createQuery($dql);
        $uRes = $q->execute();

        $this->assertStringEndsWith('WHERE (((test_dac_department.id IN (\''.implode('\', \'', $departmentIds).'\'))))', $q->getSQL());
        $this->assertInternalType('integer', $uRes);
        $this->assertSame(2, $uRes, 'Две записи обновлены');

        $dac->disable();
        $dql = 'SELECT d FROM MaxposterDacTestBundle:Department d WHERE d.name = \'changed\'';
        $sRes = $this->em->createQuery($dql)->getResult();

        $this->assertInternalType('array', $sRes);
        $this->assertEquals(2, count($sRes), 'Две записи изменены');
        $this->assertTrue(in_array($sRes[0]->getId(), $departmentIds), 'Идентификатор первой записи');
        $this->assertTrue(in_array($sRes[1]->getId(), $departmentIds), 'Идентификатор второй записи');
    }


    /**
     * Пользователь без прав
     *
     * @test
     */
    public function testDelete_UserWithoutDepartments()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();

        $dacSettings = new Settings();
        $dacSettings->set('Maxposter\\DacTestBundle\\Entity\\Department', array());

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();
        $this->em->clear();

        $dql = 'DELETE MaxposterDacTestBundle:Department';
        $q = $this->em->createQuery($dql);
        $res = $q->getResult();

        $this->assertStringEndsWith('WHERE (1=2)', $q->getSQL());
        $this->assertInternalType('integer', $res);
        $this->assertSame(0, $res);
    }


    /**
     * Пользователь с одним авто-подразделением
     *
     * @test
     */
    public function testDelete_UserWithDepartment()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Department' => array(
                $department1->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();
        $this->em->clear();

        $dql = 'DELETE MaxposterDacTestBundle:Department';
        $q = $this->em->createQuery($dql);
        $uRes = $q->execute();

        $this->assertStringEndsWith('WHERE (((test_dac_department.id IN (\''.$department1->getId().'\'))))', $q->getSQL());
        $this->assertInternalType('integer', $uRes);
        $this->assertSame(1, $uRes);

        $dac->disable();
        $dql = 'SELECT d FROM MaxposterDacTestBundle:Department d';
        $sRes = $this->em->createQuery($dql)->getResult();

        $this->assertInternalType('array', $sRes);
        $this->assertEquals(1, count($sRes), 'Осталась одна запись');
        $this->assertEquals($department2->getId(), $sRes[0]->getId());
    }


    /**
     * Пользователь с несколькими авто-подразделениями
     *
     * @test
     */
    public function testDelete_UserWithDepartments()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();
        $department3 = $this->helper->makeDepartment();
        $departmentIds = array($department1->getId(), $department3->getId());

        $dacSettings = new Settings();
        $dacSettings->set('Maxposter\\DacTestBundle\\Entity\\Department', $departmentIds);

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();
        $this->em->clear();

        $dql = 'DELETE MaxposterDacTestBundle:Department';
        $q = $this->em->createQuery($dql);
        $uRes = $q->execute();

        $this->assertStringEndsWith('WHERE (((test_dac_department.id IN (\''.implode('\', \'', $departmentIds).'\'))))', $q->getSQL());
        $this->assertInternalType('integer', $uRes);
        $this->assertSame(2, $uRes, 'Две записи удалены');

        $dac->disable();
        $dql = 'SELECT d FROM MaxposterDacTestBundle:Department d';
        $sRes = $this->em->createQuery($dql)->getResult();

        $this->assertInternalType('array', $sRes);
        $this->assertEquals(1, count($sRes), 'Осталась одна запись');
        $this->assertEquals($sRes[0]->getId(), $department2->getId(), 'Идентификатор второй записи');
    }


    /**
     * Пользователь с несколькими авто-подразделениями и без автосалона
     *
     * @test
     */
    public function testDelete_UserWithDepartmentsButWithoutDealer()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();
        $department3 = $this->helper->makeDepartment();
        $departmentIds = array(
            $department1->getId(),
            $department3->getId()
        );

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Dealer' => array(),
            'Maxposter\\DacTestBundle\\Entity\\Department' => $departmentIds,
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();
        $this->em->clear();

        $dql = 'DELETE MaxposterDacTestBundle:Department';
        $q = $this->em->createQuery($dql);
        $uRes = $q->execute();

        $this->assertStringEndsWith('WHERE (((test_dac_department.id IN (\''.implode('\', \'', $departmentIds).'\'))))', $q->getSQL());
        $this->assertInternalType('integer', $uRes);
        $this->assertSame(2, $uRes, 'Две записи удалены');

        $dac->disable();
        $dql = 'SELECT d FROM MaxposterDacTestBundle:Department d';
        $sRes = $this->em->createQuery($dql)->getResult();

        $this->assertInternalType('array', $sRes);
        $this->assertEquals(1, count($sRes), 'Одна запись осталась');
        $this->assertEquals($sRes[0]->getId(), $department2->getId(), 'Идентификатор первой записи');
    }

}