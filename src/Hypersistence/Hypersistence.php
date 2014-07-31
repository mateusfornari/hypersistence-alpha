<?php
require_once 'Core'.DIRECTORY_SEPARATOR.'Engine.php';

class Hypersistence extends \Hypersistence\Core\Engine{
	
	public static function autoload($className)
    {
        $thisClass = str_replace(__NAMESPACE__.'\\', '', __CLASS__);

        $baseDir = __DIR__;

        if (substr($baseDir, -strlen($thisClass)) === $thisClass) {
            $baseDir = substr($baseDir, 0, -strlen($thisClass));
        }

        $className = ltrim($className, '\\');
        $fileName = $baseDir;
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        if (file_exists($fileName)) {
            require $fileName;
        }
    }
	
	public static function registerAutoloader()
    {
        spl_autoload_register(__NAMESPACE__ . "\\Hypersistence::autoload");
    }
	
	public static function &getDBConnection(){
		return \Hypersistence\Core\DB::getDBConnection();
	}
	
}
