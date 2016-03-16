<?php
/** 
 * @author	   Phong
 * @copyright  2016 
 * @version    1.0
*/
function acwork_db($target)
{
	$param = array();
	
	$param[''] = array(
		'dsn' => 'mysql:dbname=qlfile;host=192.168.1.223',
		'username' => 'qlfile',
		'password' => 'qlfile123',
		'driver_options' => array(PDO::ATTR_PERSISTENT => false)
		);
	
	return $param[$target];
}

