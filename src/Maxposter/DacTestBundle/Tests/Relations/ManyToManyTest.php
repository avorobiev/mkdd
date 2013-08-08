<?php
namespace Maxposter\DacTestBundle\Tests\Relations;

use Maxposter\DacTestBundle\Tests\AppTestCase;
use Maxposter\DacBundle\Dac\Dac;
use Maxposter\DacTestBundle\Dac\Settings;

class ManyToManyTest extends AppTestCase
{
    /**
     * Создать: единственное значение из настроек ДАК
     */
    public function testInsert_LoadValueFromSettings_Success()
    {
        $sideOne1 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne();
        $sideOne2 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne();

        $sideTwo1 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideTwo();

        $this->em->persist($sideOne1);
        $this->em->persist($sideOne2);
        $this->em->flush();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\TestManyToMany\\SideOne' => array(
                $sideOne1->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $this->em->persist($sideTwo1);
        $this->em->flush();

        $dac->disable();
        $dql = 'SELECT s, s2 FROM MaxposterDacTestBundle:TestManyToMany\\SideTwo s LEFT JOIN s.sideOne s2';
        $result = $this->em->createQuery($dql)->getArrayResult();
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, count($result['0']['sideOne']));
        $this->assertEquals($sideOne1->getId(), $result['0']['sideOne']['0']['id']);
    }


    /**
     * Создать: значение установлено вручную
     */
    public function testInsert_ValidateValue_Success()
    {
        $sideOne1 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne();
        $sideOne2 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne();
        $sideOne3 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne();

        $sideTwo1 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideTwo();

        $this->em->persist($sideOne1);
        $this->em->persist($sideOne2);
        $this->em->persist($sideOne3);
        $this->em->flush();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\TestManyToMany\\SideOne' => array(
                $sideOne1->getId(),
                $sideOne3->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $sideTwo1->addSideOne($sideOne1);
        $sideTwo1->addSideOne($sideOne3);
        $this->em->persist($sideTwo1);
        $this->em->flush();

        $dac->disable();
        $dql = 'SELECT s, s2 FROM MaxposterDacTestBundle:TestManyToMany\\SideTwo s LEFT JOIN s.sideOne s2';
        $result = $this->em->createQuery($dql)->getArrayResult();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, count($result['0']['sideOne']));
        $this->assertEquals($sideOne1->getId(), $result['0']['sideOne']['0']['id']);
        $this->assertEquals($sideOne3->getId(), $result['0']['sideOne']['1']['id']);
    }


    /**
     * Создать: неверное значение
     */
    public function testInsert_ValidateValue_Fail()
    {
        $sideOne1 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne();
        $sideOne2 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne();
        $sideOne3 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne();

        $sideTwo1 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideTwo();

        $this->em->persist($sideOne1);
        $this->em->persist($sideOne2);
        $this->em->persist($sideOne3);
        $this->em->flush();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\TestManyToMany\\SideOne' => array(
                $sideOne1->getId(),
                $sideOne3->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $sideTwo1->addSideOne($sideOne2);
        $this->em->persist($sideTwo1);

        $this->setExpectedException('\\Maxposter\\DacBundle\\Dac\\Exception', 'Неверное значение поля');
        $this->em->flush();
    }


    /**
     * Создать: значение из настроек ДАК: много вариантов
     */
    public function testInsert_LoadValueFromSettings_Multiple()
    {
        $sideOne1 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne();
        $sideOne2 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne();
        $sideOne3 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideOne();

        $sideTwo1 = new \Maxposter\DacTestBundle\Entity\TestManyToMany\SideTwo();

        $this->em->persist($sideOne1);
        $this->em->persist($sideOne2);
        $this->em->persist($sideOne3);
        $this->em->flush();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\TestManyToMany\\SideOne' => array(
                $sideOne1->getId(),
                $sideOne3->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $this->em->persist($sideTwo1);

        $this->setExpectedException('\\Maxposter\\DacBundle\\Dac\\Exception', 'Невозможно получить единственно верное значение');
        $this->em->flush();
    }
}
