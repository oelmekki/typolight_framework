<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki 
 * @package    Framework 
 * @license    LGPL 
 * @filesource
 */


$GLOBALS[ 'BE_MOD' ][ 'framework' ][ 'routes' ] = array
( 
  'tables' => array( 'tl_framework_routes' )
) ;

$GLOBALS[ 'BE_MOD' ][ 'framework' ][ 'hardRoutes' ] = array
( 
  'callback' => 'HardRoutesList',
) ;

$GLOBALS[ 'FE_MOD' ][ 'framework' ][ 'routedNav' ] = 'ModuleRoutedNav';
$GLOBALS[ 'BE_FFL' ][ 'paramWizard' ] = 'ParamWizard' ;  
$GLOBALS[ 'BE_FFL' ][ 'routesWizard' ] = 'RoutesWizard' ;  
$GLOBALS[ 'BE_FFL' ][ 'manyToManyCheckbox' ] = 'ManyToManyCheckbox' ;  
$GLOBALS[ 'TL_HOOKS' ][ 'getPageIdFromUrl' ][] = array( 'Route', 'resolve' );
$GLOBALS[ 'TL_HOOKS' ][ 'replaceInsertTags' ][] = array( 'Route', 'insertTags' );

 
/**
 * -------------------------------------------------------------------------
 * ROUTES
 * -------------------------------------------------------------------------
 *
 *  $GLOBALS[ 'TL_ROUTES' ] = array(
 *    "themes"          => array(  
 *      'route'         => 'themes/liste',
 *      'staticParams'  => array(
 *        'action' => 'index',
 *      ),
 *      'POSTroute'     => false,
 *      'resolveTo'     => 'themes',
 *    ),
 *    "theme"          => array(  
 *      'route'         => 'voir/theme/:id',
 *      'staticParams'  => array(
 *        'action' => 'show',
 *      ),
 *      'POSTroute'     => false,
 *      'resolveTo'     => 'themes',
 *    ),
 *  );
 */
?>
