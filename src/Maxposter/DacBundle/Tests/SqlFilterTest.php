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

        $dacEntity = $this->getMock('\\Maxposter\\DacBundle\\Entity\\DacInterface');
        $dacEntity::staticExpects($this->atLeastOnce())
            ->method('getDacFields')
            ->will($this->returnValue(array('id', 'foreign_id')))
        ;

        $dacSettings = new Settings();
        $dacSettings->set(get_class($dacEntity), array(24, 36));

        $dac = $client->getContainer()->get('maxposter.dac.dac');
        $dac->setSettings($dacSettings);
        $dac->enable();

        $meta = new \Doctrine\ORM\Mapping\ClassMetadata(get_class($dacEntity), $em->getConfiguration()->getNamingStrategy());
        $meta->initializeReflection(new \Doctrine\Common\Persistence\Mapping\RuntimeReflectionService());
        $meta->identifier[0] = 'id';
        /** @var $filters \Doctrine\ORM\Query\FilterCollection */
        $filter = $em->getFilters()->getFilter(Dac::SQL_FILTER_NAME);
        $this->assertEquals('((a_.id IN (\'24\', \'36\')))', $filter->addFilterConstraint($meta, 'a_'));
    }
}