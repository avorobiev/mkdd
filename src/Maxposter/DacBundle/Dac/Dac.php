<?php

namespace Maxposter\DacBundle\Dac;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Maxposter\DacBundle\Annotations\Mapping\Service\Annotations;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class Dac
{
    const
        SQL_FILTER_NAME = 'dac_sql_filter'
    ;

    private
        /* @var \Doctrine\Bundle\DoctrineBundle\Registry */
        $doctrine,

        /** @var \Maxposter\DacBundle\Dac\Settings */
        $settings
    ;


    public function __construct(Registry $doctrine, Annotations $annotations)
    {
        $this->doctrine = $doctrine;

        // Регистрация sql-фильтра, чтобы в коде можно было ссылаться на его имя
        $this->doctrine->getManager()->getConfiguration()->addFilter(
            static::SQL_FILTER_NAME, 'Maxposter\\DacBundle\\Dac\\SqlFilter'
        );
    }

    public function enable()
    {
        // todo: Включаться должны и SqlFilter и DoctrineListener вместе
        // Включение SQL-фильтра
        $filters = $this->doctrine->getManager()->getFilters(); /** @var $filters \Doctrine\ORM\Query\FilterCollection */
        $filters->enable(static::SQL_FILTER_NAME);
        $filter = $filters->getFilter(static::SQL_FILTER_NAME); /** @var \Doctrine\ORM\Query\Filter\SQLFilter */
        $filter->setDacSettings($this->getSettings());
    }

    public function disable()
    {
        // todo: Выключаться должны и SqlFilter и DoctrineListener за раз
        $this->doctrine->getManager()->getFilters()->disable(static::SQL_FILTER_NAME);
    }

    public function setSettings(Settings $settings)
    {
        $this->settings = $settings;
    }

    protected function getSettings()
    {
        if (is_null($this->settings)) {
            $this->settings = new Settings();
        }
        return $this->settings;
    }
}