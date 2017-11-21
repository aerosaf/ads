<?php
/**
 * Ads package
 *
 * @package  v1
 * @author   aerosaf
 */

/*
|--------------------------------------------------------------------------
| Load framework
|--------------------------------------------------------------------------
*/
// error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING );
define('_EXE', 1);
require_once(__DIR__.'/const.php');
require_once(PATH_CONTROLLER.'/Frame.php');
use \Aero\Controller\AppFactory;

$status = AppFactory::getApplication('ads');

if ($status == true){
	AppFactory::$application->render();
}