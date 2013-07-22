<?php
namespace Maxposter\DacBundle\Dac;

use \Doctrine\ORM\Mapping\ClassMetadata;
use \Doctrine\DBAL\Connection;
use Maxposter\DacBundle\Annotations\Mapping\Service\Annotations;

/**
 * @package Maxposter\DacBundle\SqlFilter
 */
class SqlFilter extends \Doctrine\ORM\Query\Filter\SQLFilter
{

    private
        /** @var Settings */
        $dacSettings,

        /** @var \Maxposter\DacBundle\Annotations\Mapping\Service\Annotations */
        $annotations;

    public function setAnnotations(Annotations $annotations)
    {
        $this->annotations = $annotations;
    }

    public function setDacSettings(Settings $dacSettings)
    {
        $this->dacSettings = $dacSettings;
    }

    private function getDacSettings()
    {
        if (is_null($this->dacSettings)) {
            throw new Exception('Ошибка в инициализации SQL-фильтра: не заданы параметры фильтрации');
        }
        return $this->dacSettings;
    }

    /**
     * Gets the SQL query part to add to a query.
     *
     * @param  \Doctrine\ORM\Mapping\ClassMetadata  $targetEntity
     * @param  string  $targetTableAlias
     * @return string The constraint SQL if there is available, empty string otherwise
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        $entityName = $targetEntity->getReflectionClass()->getName();
        if (!$this->annotations->hasDacFields($entityName)){
            return '';
        }

        $dacFields = $this->annotations->getDacFields($entityName);
        $conditions = array();
        foreach ($dacFields as $filteredFieldName => $dacSettingsName) {
            $conditions[] = sprintf(
                '%s.%s IN (\'%s\')',
                $targetTableAlias,
                $filteredFieldName,
                implode('\', \'', (array) $this->getDacSettings()->get($dacSettingsName))
            );
        }

        $result = '';
        if ($conditions) {
            $result = sprintf('((%s))', implode(') OR (', $conditions));
        }

        return $result;
    }
}