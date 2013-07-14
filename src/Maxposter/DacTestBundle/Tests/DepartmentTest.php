<?php
namespace Maxposter\DacTestBundle\Tests;

class DepartmentTest extends AppTestCase
{
    public function testSelect_UserWithoutDepartments()
    {
        $this->markTestSkipped('Не реализована инициализация МКДД');
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();

        $session = $this->container->get('session');
        $firewall = 'main';
        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
            'user1', null, $firewall, array('ROLE_USER')
        );
        $session->set('_security_'.$firewall, serialize($token));

        $session->set('dac', array(
            '\\Maxposter\\DacDoctrineTestBundle\\Entity\\Business'   => '',
            '\\Maxposter\\DacDoctrineTestBundle\\Entity\\Company'    => '',
            '\\Maxposter\\DacDoctrineTestBundle\\Entity\\Dealer'     => '',
            '\\Maxposter\\DacDoctrineTestBundle\\Entity\\Department' => '',
        ));
        $session->save();

        $cookie = new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->client->request('GET', '/');

        $res = $this->em->createQuery('SELECT d FROM MaxposterDacTestBundle:Department d')->getResult();

        $this->assertInternalType('array', $res);
        $this->assertEquals(0, count($res));
    }
}