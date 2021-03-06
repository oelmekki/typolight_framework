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


/**
 * Back end modules
 */
$GLOBALS['TL_LANG']['MOD']['routes'] = array( 'Routes', 'This module let you define some routes.' );
$GLOBALS['TL_LANG']['MOD']['hardRoutes'] = array( 'Hard Routes List', 'This module show routes defined in modules config files.' );


/**
 * Front end modules
 */
$GLOBALS['TL_LANG']['FMD']['messages']          = array('Messages', 'Display messages from previous action');
$GLOBALS['TL_LANG']['FMD']['routedBreadcrumb']  = array('Routed breadcrumb', 'Like breadcrumb, but include the action for FrontendControllers');
$GLOBALS['TL_LANG']['FMD']['routedNav']         = array('Routed navigation', 'Build a navigation using routes');
