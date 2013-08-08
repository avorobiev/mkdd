<?php
namespace Maxposter\DacTestBundle\Tests\Relations;

use Maxposter\DacTestBundle\Tests\AppTestCase;
use Maxposter\DacBundle\Dac\Dac;
use Maxposter\DacTestBundle\Dac\Settings;

class OneToOneTest extends AppTestCase
{
    /**
     * Создать: единственное значение из настроек ДАК
     */
    public function testInsert_LoadValueFromSettings_Success()
    {
        $comp1 = $this->helper->makeCompany();
        $comp2 = $this->helper->makeCompany();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp1->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $info = new \Maxposter\DacTestBundle\Entity\CompanyInfo();
        $info->setData('blah-blah');
        $this->em->persist($info);
        $this->em->flush();

        $dac->disable();
        $dql = 'SELECT ci FROM MaxposterDacTestBundle:CompanyInfo ci WHERE ci.company = ?1';
        $this->assertEquals(1, count($this->em->createQuery($dql)->setParameters(array('1' => $comp1->getId()))->getResult()));
    }


    /**
     * Создать: значение установлено вручную
     */
    public function testInsert_ValidateValue_Success()
    {
        $comp1 = $this->helper->makeCompany();
        $comp2 = $this->helper->makeCompany();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp1->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $info = new \Maxposter\DacTestBundle\Entity\CompanyInfo();
        $info->setData('blah-blah');
        $info->setCompany($comp1);
        $this->em->persist($info);
        $this->em->flush();

        $dac->disable();
        $dql = 'SELECT ci FROM MaxposterDacTestBundle:CompanyInfo ci WHERE ci.company = ?1';
        $this->assertEquals(1, count($this->em->createQuery($dql)->setParameters(array('1' => $comp1->getId()))->getResult()));
    }


    /**
     * Создать: неверное значение
     */
    public function testInsert_ValidateValue_Fail()
    {
        $comp1 = $this->helper->makeCompany();
        $comp2 = $this->helper->makeCompany();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp1->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $info = new \Maxposter\DacTestBundle\Entity\CompanyInfo();
        $info->setData('blah-blah');
        $info->setCompany($comp2);
        $this->em->persist($info);

        $this->setExpectedException('\\Maxposter\\DacBundle\\Dac\\Exception', 'Неверное значение поля');
        $this->em->flush();
    }


    /**
     * Создать: значение из настроек ДАК: много вариантов
     */
    public function testInsert_LoadValueFromSettings_Multiple()
    {
        $comp1 = $this->helper->makeCompany();
        $comp2 = $this->helper->makeCompany();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp1->getId(),
                $comp2->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $info = new \Maxposter\DacTestBundle\Entity\CompanyInfo();
        $info->setData('blah-blah');
        $this->em->persist($info);

        $this->setExpectedException('\\Maxposter\\DacBundle\\Dac\\Exception', 'Невозможно получить единственно верное значение');
        $this->em->flush();
    }


    /**
     * Обновить: единственное значение из настроек ДАК
     */
    public function testUpdate_LoadValueFromSettings_Success()
    {
        $comp1 = $this->helper->makeCompany();
        $comp2 = $this->helper->makeCompany();
        $info1 = $this->helper->makeCompanyInfo(array('Company' => $comp1));

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp1->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $info1->setCompany(null);
        $this->em->flush();

        $dac->disable();
        $dql = 'SELECT ci FROM MaxposterDacTestBundle:CompanyInfo ci WHERE ci.company = ?1';
        $this->assertEquals(1, count($this->em->createQuery($dql)->setParameters(array('1' => $comp1->getId()))->getResult()));
    }


    /**
     * Обновить: неверное значение
     */
    public function testUpdate_ValidateValue_Fail()
    {
        $comp1 = $this->helper->makeCompany();
        $comp2 = $this->helper->makeCompany();
        $info1 = $this->helper->makeCompanyInfo(array('Company' => $comp1));

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp1->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $info1->setCompany($comp2);

        $this->setExpectedException('\\Maxposter\\DacBundle\\Dac\\Exception', 'Неверное значение поля');
        $this->em->flush();
    }


    /**
     * Обновить: значение установлено вручную
     */
    public function testUpdate_ValidateValue_Success()
    {
        $comp1 = $this->helper->makeCompany();
        $comp2 = $this->helper->makeCompany();
        $info1 = $this->helper->makeCompanyInfo(array('Company' => $comp1));

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp1->getId(),
                $comp2->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $info1->setCompany($comp2);
        $this->em->flush();

        $dac->disable();
        $dql = 'SELECT ci FROM MaxposterDacTestBundle:CompanyInfo ci WHERE ci.company = ?1';
        $this->assertEquals(1, count($this->em->createQuery($dql)->setParameters(array('1' => $comp2->getId()))->getResult()));
    }


    /**
     * Обновить: значение из настроек ДАК: много вариантов
     */
    public function testUpdate_LoadValueFromSettings_Multiple()
    {
        $comp1 = $this->helper->makeCompany();
        $comp2 = $this->helper->makeCompany();
        $info1 = $this->helper->makeCompanyInfo(array('Company' => $comp1));

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp1->getId(),
                $comp2->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $info1->setCompany(null);

        $this->setExpectedException('\\Maxposter\\DacBundle\\Dac\\Exception', 'Невозможно получить единственно верное значение');
        $this->em->flush();
    }


    /**
     * Удалить: верное значение
     */
    public function testDelete_ValidateValue_Success()
    {
        $comp1 = $this->helper->makeCompany();
        $comp2 = $this->helper->makeCompany();
        $info1 = $this->helper->makeCompanyInfo(array('Company' => $comp1));

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp1->getId(),
                $comp2->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $this->em->remove($info1);
        $this->em->flush();

        $dac->disable();
        $dql = 'SELECT ci FROM MaxposterDacTestBundle:CompanyInfo ci';
        $this->assertEquals(0, count($this->em->createQuery($dql)->getResult()));
    }


    /**
     * Удалить: неверное значение
     */
    public function testDelete_ValidateValue_Fail()
    {
        $comp1 = $this->helper->makeCompany();
        $comp2 = $this->helper->makeCompany();
        $info1 = $this->helper->makeCompanyInfo(array('Company' => $comp1));

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp2->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $this->em->remove($info1);
        $this->setExpectedException('\\Maxposter\\DacBundle\\Dac\\Exception', 'Неверное значение поля');
        $this->em->flush();
    }


    /**
     * Удалить: обе записи
     */
    public function testDeleteBoth_ValidateValue_Success()
    {
        $comp1 = $this->helper->makeCompany();
        $info1 = $this->helper->makeCompanyInfo(array('Company' => $comp1));

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Business' => array(
                $comp1->getBusiness()->getId(),
            ),
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp1->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $this->em->remove($comp1);
        $this->em->remove($info1);
        $this->em->flush();
    }


    /**
     * Создать и удалить родителя
     */
    public function testDeleteAndInsert_Validate_Success()
    {
        $comp1 = $this->helper->makeCompany();

        $dacSettings = new Settings();
        $dacSettings->setSettings(array(
            'Maxposter\\DacTestBundle\\Entity\\Business' => array(
                $comp1->getBusiness()->getId(),
            ),
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(
                $comp1->getId(),
            ),
        ));

        $dac = $this->client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $info1 = new \Maxposter\DacTestBundle\Entity\CompanyInfo();
        $info1->setData('blah-blah');
        $info1->setCompany($comp1);
        $this->em->persist($info1);

        $this->em->remove($comp1);
        $this->em->remove($info1);
        $this->em->flush();
    }
}
