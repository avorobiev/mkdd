<?php
namespace Maxposter\DacTestBundle\Dac;

class Settings extends \Maxposter\DacBundle\Dac\Settings
{
    /**
     * Задал сразу все используемые для фильтрации entity
     */
    public function reset()
    {
        $this->settings = array(
            'Maxposter\\DacTestBundle\\Entity\\Business' => array(),
            'Maxposter\\DacTestBundle\\Entity\\Dealer' => array(),
            'Maxposter\\DacTestBundle\\Entity\\Department' => array(),
            'Maxposter\\DacTestBundle\\Entity\\Company' => array(),
        );
    }
}