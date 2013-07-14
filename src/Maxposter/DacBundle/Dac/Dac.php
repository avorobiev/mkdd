<?php

namespace Maxposter\DacBundle\Dac;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class Dac
{
    private
        /* @var \Doctrine\Bundle\DoctrineBundle\Registry */
        $doctrine
    ;


    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;

        // Регистрация sql-фильтра, чтобы в коде можно было ссылаться на его имя
        $this->doctrine->getManager()->getConfiguration()->addFilter(
            'dac_sql_filter', 'Maxposter\\DacBundle\\SqlFilter\\DacSqlFilter'
        );
    }

    public function enable()
    {
        // todo: Включаться должны и SqlFilter и DoctrineListener за раз
        $this->doctrine->getManager()->getFilters()->enable('dac_sql_filter');
    }

    public function disable()
    {
        // todo: Выключаться должны и SqlFilter и DoctrineListener за раз
        $this->doctrine->getManager()->getFilters()->disable('dac_sql_filter');
    }
}