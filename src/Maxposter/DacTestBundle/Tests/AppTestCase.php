<?php
namespace Maxposter\DacTestBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppTestCase extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var EntityHelper
     */
    protected $helper;


    protected function setUp()
    {
        $this->client = static::createClient(array(), array());
        $this->container = $this->client->getContainer();
        $this->em = $this->container->get('doctrine')->getManager();
        $this->em->beginTransaction();
        $this->helper = new EntityHelper($this->em);
    }

    /**
     *
     */
    protected function tearDown()
    {
        $this->em->getConnection()->rollback();
        $this->em->getConnection()->close();
        $this->helper = null;
        parent::tearDown();
    }

}