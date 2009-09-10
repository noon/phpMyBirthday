<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library/ZendFramework-1.8.0/library'),
    get_include_path(),
)));


// include model folder
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/models'),
    realpath(APPLICATION_PATH . '/models/Bc'),
    get_include_path(),
)));

ini_set('error_log', APPLICATION_PATH . '/../log/php.log');
error_reporting(E_ALL | E_STRICT);
ini_set('log_errors', 1);

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


// load custom view, save it into registry
$customView =  new Zend_View();

// bootstrap layouts
Zend_Layout::startMvc(array(
    'layoutPath' => '../application/layouts' ,
    'layout' => 'main'))->setView($customView);

// prepare for Dojo, this actually only adds a helper path for Dojo related helpers to our view
Zend_Dojo::enableView($customView);

//register our custom view renderer
$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
$viewRenderer->setView($customView)->setViewSuffix('phtml');
Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

$application->bootstrap()
            ->run();
