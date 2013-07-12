<?php
namespace Maxposter\MkddTestBundle\Tests;

class DepartmentTest extends AppTestCase
{
    public function testSelect_UserWithoutDepartments()
    {
        $department1 = $this->helper->makeDepartment();
        $department2 = $this->helper->makeDepartment();

        $session = $this->container->get('session');
        $firewall = 'main';
        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
            'user1', null, $firewall, array('ROLE_USER')
        );
        $session->set('_security_'.$firewall, serialize($token));

        $session->set('mkdd', array(
            '\\Maxposter\\MkddDoctrineTestBundle\\Entity\\Business'   => '',
            '\\Maxposter\\MkddDoctrineTestBundle\\Entity\\Company'    => '',
            '\\Maxposter\\MkddDoctrineTestBundle\\Entity\\Dealer'     => '',
            '\\Maxposter\\MkddDoctrineTestBundle\\Entity\\Department' => '',
        ));
        $session->save();

        $cookie = new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        $this->client->request('GET', '/');

        $res = $this->em->createQuery('SELECT d FROM MaxposterMkddTestBundle:Department d')->getResult();

        $this->assertInternalType('array', $res);
        $this->assertEquals(0, count($res));
    }
}