<?php
/**
 * @copyright  Copyright (C) GDP. All rights reserved.
 * @author Aerosaf aerosaf@gmail.com
 */
namespace Aero\Controller;
defined('_EXE') or die;
require_once('Ads.php');
use \Aero\Controller\AdsApp;

class AppFactory
{
	public static $application;

    public static function getApplication($app = 'ads', $id = null) 
    {
    	$app = ucfirst($app);
        $appType = 'Aero\Controller\\'.$app.'App';
        if (class_exists($appType)) {
            self::$application = new $appType($id);
            return true;
        } else {
            throw new Exception("Invalid application given.");
            return false;
        }
    } 

    // public static function getModule($type = 'resource', $tmpl = null) 
    // {
    //     $modClass = 'Aero\Controller\ModuleApp';
    //     if (class_exists($modClass)) {
    //         self::$module = new $modClass($type, $tmpl);
    //         return true;
    //     } else {
    //         throw new Exception("Invalid Module given.");
    //         return false;
    //     }
    // } 
}