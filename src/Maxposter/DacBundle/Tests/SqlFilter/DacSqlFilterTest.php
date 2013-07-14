<?php
namespace Maxposter\DacBundle\Tests\SqlFilter;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use \Symfony\Bundle\FrameworkBundle\Client;
use \Doctrine\Orm\EntityManager;
use \Maxposter\DacBundle\Dac\Dac;

class DacSqlFilterTest extends WebTestCase
{
    public function testFilterEnabledAndDisable()
    {
        $client = static::createClient($options = array(), $serverArgs = array());
        $doctrine = $client->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $dac = new \Maxposter\DacBundle\Dac\Dac($doctrine);
        /** @var $filters \Doctrine\ORM\Query\FilterCollection */
        $filters = $em->getFilters();

        $this->assertArrayNotHasKey(Dac::SQL_FILTER_NAME, $filters->getEnabledFilters(), 'Изначально фильтр объявлен но не включен');

        $dac->enable();
        $this->assertArrayHasKey(Dac::SQL_FILTER_NAME, $filters->getEnabledFilters(), 'При включении DAC фильтр активизируется');

        $dac->disable();
        $this->assertArrayNotHasKey(Dac::SQL_FILTER_NAME, $filters->getEnabledFilters(), 'При отключении DAC фильтр деактивируется');
    }


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

        $dac = $this->getMock('\\Maxposter\\DacBundle\\Dac\\Dac', array('getDacSettings'), array($doctrine));
        $dac->expects($this->any())
            ->method('getDacSettings')
            ->will($this->returnValue(array(
                get_class($dacEntity) => array(24, 36)
            )))
        ;
        $dac->enable();

        $meta = new \Doctrine\ORM\Mapping\ClassMetadata(get_class($dacEntity), $em->getConfiguration()->getNamingStrategy());
        $meta->initializeReflection(new \Doctrine\Common\Persistence\Mapping\RuntimeReflectionService());
        $meta->identifier[0] = 'id';
        /** @var $filters \Doctrine\ORM\Query\FilterCollection */
        $filter = $em->getFilters()->getFilter(Dac::SQL_FILTER_NAME);
        $this->assertEquals('((a_.id IN (\'24\', \'36\')))', $filter->addFilterConstraint($meta, 'a_'));
    }
}