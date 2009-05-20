<?php

function tl_autoload($strClassName)
{
        // Library
        if (file_exists(TL_ROOT . '/system/libraries/' . $strClassName . '.php'))
        {
                include_once(TL_ROOT . '/system/libraries/' . $strClassName . '.php');
                return;
        }

        // Modules
        foreach (scan(TL_ROOT . '/system/modules/') as $strFolder)
        {
                if (substr($strFolder, 0, 1) == '.')
                {
                        continue;
                }

                if (file_exists(TL_ROOT . '/system/modules/' . $strFolder . '/' . $strClassName . '.php'))
                {
                        include_once(TL_ROOT . '/system/modules/' . $strFolder . '/' . $strClassName . '.php');
                        return;
                }
        }

        // HOOK: include DOMPDF classes
        if (function_exists('DOMPDF_autoload'))
        {
                DOMPDF_autoload($strClassName);
                return;
        }

        PHPSpec_Framework::autoload($strClassName);
}

spl_autoload_register( 'tl_autoload' );

define('TL_MODE', 'BE');
//require_once( '../../system/initialize.php' );

define('TL_ROOT', dirname(dirname(dirname(dirname(dirname( dirname( __FILE__ )))))));
require(TL_ROOT . '/system/functions.php');
require(TL_ROOT . '/system/constants.php');
require(TL_ROOT . '/system/interface.php');



?>
