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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_framework_routes']['name'] = array('Route name', "Specify here the name of the route. Anywhere, you can automatically generate its path with this name, using the function Route::compose( name ).");
$GLOBALS['TL_LANG']['tl_framework_routes']['route'] = array('Route definition', "Give the path this route must resolv against. You can specify parameters by placing a ':' character in front of them. Example: '/blog/page/:id/comments/:comment_id/delete'");
$GLOBALS['TL_LANG']['tl_framework_routes']['resolveTo'] = array('Destination page', 'On which page should the route lead?');
$GLOBALS['TL_LANG']['tl_framework_routes']['addStatic'] = array('Add some static GET params', "You can add some params that will be passed as GET params to the destination page.");
$GLOBALS['TL_LANG']['tl_framework_routes']['staticParams'] = array('Params', 'Give the param names and values.');
$GLOBALS['TL_LANG']['tl_framework_routes']['POSTroute'] = array('POST route', 'If you check this, this route will be recognized if and only if there is at least one POST param in the request. This let you route the same url on several pages.');


/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_framework_routes']['routeParam'] = 'Param name';
$GLOBALS['TL_LANG']['tl_framework_routes']['routeValue'] = 'Param value';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_framework_routes']['new']    = array('New route', 'New');
$GLOBALS['TL_LANG']['tl_framework_routes']['edit']   = array('Edit', 'Edit');
$GLOBALS['TL_LANG']['tl_framework_routes']['copy']   = array('Duplicate', 'Duplicate');
$GLOBALS['TL_LANG']['tl_framework_routes']['delete'] = array('Delete', 'Delete');
$GLOBALS['TL_LANG']['tl_framework_routes']['show']   = array('Voir', 'Voir');

?>
