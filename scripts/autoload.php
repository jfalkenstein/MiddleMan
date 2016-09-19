<?php
/**
 * This registers the autoloader function that is utilized throughout the MiddleMan 
 * application. It looks in the root directory and then corresponds the namespace segments with
 * directory heirarchy.
 */
function registerAutoloader(){
    spl_autoload_register(function ($className){
        /*Removes any '\' from left side of URL string*/
        $className = ltrim($className, '\\');
        /*Finds the position of the last occurance of '\' in the string*/
        $lastNsPos = strripos($className, '\\');
        /*Returns only the first part of the URL.*/
        $namespace = substr($className, 0,$lastNsPos);
        /*Returns only the last part of the URL.*/
        $className = substr($className, $lastNsPos + 1);
        $fileName = ROOT_DIR . 
                DS . 
                /*Replaces and '/' or '\' with the appropriate slash for environment.
                 * This is a key part of why this function is required
                 */ 
                str_replace('\\', DS, $namespace) . 
                DS .
                $className . 
                ".php";
        if(file_exists($fileName)){
            require $fileName;
        }else{
            return false;
        }
    },true,true);
}