<?php

/**
 * Return the number of elements (the cardinality) of the Set at key
 * 
 * @param string $name Key name
 * @return integer
 * 
 * @author Ivan Shumkov
 * @package Rediska
 * @version @package_version@
 * @link http://rediska.geometria-lab.net
 * @licence http://www.opensource.org/licenses/bsd-license.php
 */
class Rediska_Command_GetSetLength extends Rediska_Command_Abstract
{
    public function create($name)
    {
        $connection = $this->_rediska->getConnectionByKeyName($name);

        $command = "SCARD {$this->_rediska->getOption('namespace')}$name";

        return new Rediska_Connection_Exec($connection, $command);
    }
}