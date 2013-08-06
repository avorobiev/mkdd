<?php
namespace Maxposter\DacTestBundle\Tests;

use Maxposter\DacTestBundle\Tests\AppTestCase;
use Maxposter\DacBundle\Dac\Dac;
use Maxposter\DacTestBundle\Dac\Settings;

class ManyToManyTest extends AppTestCase
{
    public function testInsert_SingleValue()
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
                $sideOne3->getId()
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $sideTwo1->addSideOne($sideOne1);
        $sideTwo1->addSideOne($sideOne3);
        $this->em->persist($sideTwo1);
        $this->em->flush();
    }
}
