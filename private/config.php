<?php
/* ----------------------------------------------------------------------------------------
# Program       : config.php
# Description   : Configuration file
# Author        : NL
# Date          :	August 2017
------------------------------------------------------------------------------------------- */

$config = array();

// DB connection variables
$config['hostname']   ='192.168.169.128';
$config['port']       = 3306;
$config['username']   ='webuser';
$config['password']   ='webuser';
$config['dbname']     ='nat_projects';

// --------------------------------------------------------
// TODO SPECIFIC - start
// --------------------------------------------------------
$config['logFilename'] = '../private/data/phplogs/todo.' . date('Y_m_d') . '.log';
// --------------------------------------------------------
// TODO - end
// --------------------------------------------------------
?>
