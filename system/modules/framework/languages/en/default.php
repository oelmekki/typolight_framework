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
 * @package    Language
 * @license    LGPL 
 * @filesource
 */


/**
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['addNewFile'] = 'Add a new file';

$GLOBALS['TL_LANG']['MSC']['framework'] = array(
  'routeName'   => 'Route name',
  'routeParams' => 'Route parameters',
  'altName'     => 'alternative "clean" name',
  'copy'        => 'Copy',
  'up'          => 'Up',
  'down'        => 'Down',
  'delete'      => 'Delete',
);


$GLOBALS['TL_LANG']['MSC']['framework_pagination'] = array(
  'previous'  => 'Previous',
  'next'      => 'Next',
);


$GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'HardRoutesList' ] = array(
  'noRoute'             => 'Pas de routes pour l\instant',
  'inDatabase'          => 'exists in database',
  'none'                => 'None',
  'definition'          => 'Definition',
  'resolveTo'           => 'Resolve to',
  'staticParams'        => 'Static params',
  'method'              => 'Which method match this route?',
  'true'                => 'Yes',
  'false'               => 'No',
  'loadInDb'            => 'load in database',
  'loadAll'             => 'Load all routes in database',
  'toggle-show'         => 'Show',
  'toggle-hide'         => 'Hide',
  'route_loaded'        => 'The route has been loaded in the database',
  'route_not_loaded'    => 'The route can\'t be loaded in the database',
  'routes_loaded'       => 'The routes has been loaded in the database',
  'routes_not_loaded'   => 'The routes can\'t be loaded in the database',
);

$GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'EModel' ] = array(
  'validates_presence_of'     => '%s is required',
  'validates_uniqueness_of'   => '"%s" is already taken',
  'validates_format_of'       => '%s is not formated as expected',
  'validates_numericality_of' => '%s should be numerical',
  'validates_min_length_of'   => '%s should be at least %s letters long',
  'validates_max_length_of'   => '%s should be at most %s letters long',
  'validates_associated'      => '%s should be associated with %s',
);

$GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'upload_errors' ] = array( 
  UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the server maximum size',
  UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the application maximum size',
  UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
  UPLOAD_ERR_NO_FILE => 'No file was uploaded',
  UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
  UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
  UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
);
