<?php
namespace Maxposter\DacBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Symfony\Bundle\FrameworkBundle\Client;
use \Doctrine\Orm\EntityManager;
use \Maxposter\DacBundle\Dac\Dac;
use \Maxposter\DacBundle\Dac\Settings;

class SqlFilterTest extends WebTestCase
{
    public function testAddFilterConstraint()
    {
        $client = static::createClient($options = array(), $serverArgs = array());
        $doctrine = $client->getContainer()->get('doctrine');
        $em = $doctrine->getManager();

        $dacEntity = $this->getMock('\stdClass');

        $dacSettings = new Settings();
        $dacSettings->set(get_class($dacEntity), array(24, 36));

        $dacAnnotations = $this->getMock('\Maxposter\DacBundle\Annotations\Mapping\Service\Annotations', array('hasDacFields', 'getDacFields'), array(), '', false);
        $dacAnnotations->expects($this->any())
            ->method('hasDacFields')
            ->will($this->returnValue(true));
        $dacAnnotations->expects($this->any())
            ->method('getDacFields')
            ->will($this->returnValue(array(
                'id' => get_class($dacEntity)
            )));

        $dac = $client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        /** @var $filters \Doctrine\ORM\Query\FilterCollection */
        $filter = $em->getFilters()->getFilter(Dac::SQL_FILTER_NAME);
        $filter->setAnnotations($dacAnnotations);

        $meta = new \Doctrine\ORM\Mapping\ClassMetadata(get_class($dacEntity), $em->getConfiguration()->getNamingStrategy());
        $meta->initializeReflection(new \Doctrine\Common\Persistence\Mapping\RuntimeReflectionService());
        $meta->identifier[0] = 'id';
        $meta->columnNames = array('id'=>'id');
        $meta->fieldMappings = array('id'=>'id');

        $this->assertEquals('((a_.id IN (\'24\', \'36\')))', $filter->addFilterConstraint($meta, 'a_'));
    }
}