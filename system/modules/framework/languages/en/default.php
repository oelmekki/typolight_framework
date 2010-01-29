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
  'noRoute'       => 'Pas de routes pour l\instant',
  'inDatabase'    => 'exists in database',
  'none'          => 'None',
  'definition'    => 'Definition',
  'resolveTo'     => 'Resolve to',
  'staticParams'  => 'Static params',
  'method'        => 'Which method match this route?',
  'true'          => 'Yes',
  'false'         => 'No',
  'loadInDb'      => 'load in database',
  'loadAll'       => 'Load all routes in database',
  'toggle-show'   => 'Show',
  'toggle-hide'   => 'Hide',
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
