<?php

namespace Maxposter\DacBundle\Tests;

use Maxposter\DacBundle\Dac\DacSettings;

class DacSettingsTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $ds = new DacSettings();

        $this->assertEquals(array(), $ds->getSettings(), 'После создания ограничений нет');

        $ds->setSettings(array(
            '\\MaxPoster\\Dac\\Entity\\One' => array(1),
            'MaxPoster\\Dac\\Entity\\Two\\' => array(2, 3),
            '\\MaxPoster\\Dac\\Entity\\Three\\' => array(4, 5, 6),
            'MaxPoster\\Dac\\Entity\\Four' => array(7),
        ));

        $this->assertEquals(
            array(
                'MaxPoster\\Dac\\Entity\\One' => array(1),
                'MaxPoster\\Dac\\Entity\\Two' => array(2, 3),
                'MaxPoster\\Dac\\Entity\\Three' => array(4, 5, 6),
                'MaxPoster\\Dac\\Entity\\Four' => array(7),
            ),
            $ds->getSettings(),
            'В имени entity начальные и конечные \\ удаляются'
        );


        $entityName = 'MaxPoster\\Dac\\Entity\\One';
        $this->assertEquals(array(1), $ds->getSetting($entityName), 'Значения идентификаторов по имени entity вщзвращзаются');

        $newIds = array(11, 12, 13);
        $ds->setSetting($entityName, $newIds);
        $this->assertEquals($newIds, $ds->getSetting($entityName), 'Можно задать значение для конкретной entity');
        $this->assertEquals(4, count($ds->getSettings()), 'Остальные значения остаются без изменений');

        $ds->reset();
        $this->assertEquals(array(), $ds->getSettings(), 'reset() приводит к сбросу ограничений');
    }
}