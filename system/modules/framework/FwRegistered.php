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
 * Class FwRegistered
 *
 * @copyright  Olivier El Mekki, 2010 
 * @author     Olivier El Mekki 
 * @package    Model
 */
abstract class FwRegistered extends EModel
{
  /**
   * Retrieve group names for this user
   *
   * @return array the list of group names
   **/
  public function getGroupNames()
  {
    $groups = deserialize( $this->groups );
    if ( ! count( $groups ) )
    {
      return array();
    }

    $query  = 'select name from ' . $this->group_table . ' where ';
    $params = array();
    foreach ( $groups as $group )
    {
      $query .= 'id = ? and';
      $params[] = $group;
    }

    $query = substr( $query, 0, strlen( $query ) - 4 );

    $records = $this->Database->prepare( $query )
                              ->execute( $params );

    $group_names = array();
    while ( $records->next() )
    {
      $group_names[] = $records->name;
    }

    return $group_names;
  }



  /**
   * Tell if the user belongs to a give group
   *
   * @param string the group name to test
   * @return boolean
   **/
  public function hasGroup( $group_name )
  {
    return in_array( $group_name, $this->groupNames );
  }



  /**
   * Tell if the user belongs to a set of groups.
   *
   * For this to be successful, the user as to belongs
   * to each group passed in the param array.
   *
   * @param array the list of group names to test
   * @return boolean
   **/
  public function hasGroups( $group_names )
  {
    foreach ( $group_names as $group_name )
    {
      if ( ! $this->hasGroup( $group_name ) )
      {
        return false;
      }
    }

    return true;
  }



  /**
   * Turn the model to the logged user
   *
   * Return false if the visitor isn't logged in,
   * true otherwise.
   *
   * @return boolean
   **/
  protected function toCurrent()
  {
    $class  = $this->legacy;
    $legacy = $class::getInstance();
    if ( $legacy->id )
    {
      $this->found = $legacy->getData();
      return true;
    }

    return false;
  }
}
