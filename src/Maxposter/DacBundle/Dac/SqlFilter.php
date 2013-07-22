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

    public function setDacSettings(Settings $dacSettings, Annotations $annotations)
    {
        $this->dacSettings = $dacSettings;
        $this->annotations = $annotations;
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
        if (!$this->annotations->hasDacFields($targetEntity->getReflectionClass()->getName())){
            return '';
        }

        $dacSettings = $this->getDacSettings();
        //$class = $targetEntity->getName();
        //$dacFields = $class::getDacFields();
        $dacFields = $targetEntity->getReflectionClass()->getMethod('getDacFields')->invoke(null);
        $conditions = array();
        foreach ($dacFields as $dacField) {
            $filteredFieldName = false;
            // Фильтр по FK
            if ($targetEntity->hasAssociation($dacField)) {
                $filteredFieldName = $targetEntity->getSingleAssociationJoinColumnName($dacField);
                $assocMapping = $targetEntity->getAssociationMapping($dacField);
                $dacSettingsName = $assocMapping['targetEntity'];
            } // Фильтруем по самому себе, т.е. PK
            else if ($targetEntity->getSingleIdentifierColumnName() == $dacField) {
                $filteredFieldName = $targetEntity->getSingleIdentifierColumnName();
                $dacSettingsName = $targetEntity->getName();
            }

            if ((false !== $filteredFieldName) && !is_null($dacSettings->get($dacSettingsName))) {
                $conditions[] = sprintf(
                    '%s.%s IN (\'%s\')',
                    $targetTableAlias,
                    $filteredFieldName,
                    implode('\', \'', $dacSettings->get($dacSettingsName))
                );
            }
        }

        $result = '';
        if ($conditions) {
            $result = sprintf('((%s))', implode(') OR (', $conditions));
        }

        return $result;
    }
}