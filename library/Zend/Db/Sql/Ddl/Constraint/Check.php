<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * @package Zend_Db
 */

namespace Zend\Db\Sql\Ddl\Constraint;

class Check extends AbstractConstraint
{
    protected $specification = 'PRIMARY KEY (%s)';

    public function getExpressionData()
    {
        $colCount = count($this->columns);
        $newSpecParts = array_fill(0, $colCount, '%s');
        $newSpecTypes = array_fill(0, $colCount, self::TYPE_IDENTIFIER);

        $newSpec = sprintf($this->specification, implode(', ', $newSpecParts));

        return array(
            $newSpec,
            $this->columns,
            $newSpecTypes
        );
    }

}