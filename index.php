<?php
//Set output buffering so that headers can be accumulated before the ouput is created.
ob_start();

use PB\Core\Bootstrapper\Bootstrapper;
$env = getenv('APPLICATION_ENV');
/* $_Get['display'] is only true if you manually set or pass this value.
 * The only time 'display' should be set to true is if you are troubleshooting this webservice.
 * RON 9-1-2016
 */ 

if($env === "development" && $_GET['display']){
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

const ROOT_DIR = __DIR__; /**< Save the base directory of this index.php file to the ROOT_DIR constant. */
const DS = DIRECTORY_SEPARATOR; /**< Alias the DIRECTORY_SEPARATOR to make it shorter. 
 * This is required to ensure that this webservice can be transferred across multiple platforms and operating systems.
 * Sometimes "/" is required and sometimes "\" is required depending upon the environment. */

/* It is important to note that the autoloader is used for the purposes of loading the proper code dependencies.
 * Dependencies are managed using composer and a custom script.
 * You can spend a lot of time exploring this, but it does not contain the primary code for performing the service provided.
 * To learn more about composer, go to https://getcomposer.org.
 */
require 'vendor' . DS . 'autoload.php';
require 'scripts' . DS . 'autoload.php';
registerAutoloader();

/*This initalizes the main script for middleman.*/
Bootstrapper::initialize();

ob_end_flush();

