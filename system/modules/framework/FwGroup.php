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
 * @copyright  Olivier El Mekki, 2010
 * @author     Olivier El Mekki 
 * @package    Framework 
 * @license    LGPL 
 * @filesource
 */


/**
 * Class FwGroup
 *
 * @copyright  Olivier El Mekki, 2010 
 * @author     Olivier El Mekki 
 * @package    Model
 */
abstract class FwGroup extends EModel
{
  /**
   * Find the users of this group
   * $model->users will still work with FwMemberGroup, in order
   * to be able to use the model agnosticly from the kind of
   * groups. If you are certain you are using a FwMemberGroup,
   * use $model->members instead.
   *
   * @return array  the array of users
   **/
  protected function getUsers()
  {
    $class  = $this->userClass;
    $carbon = new $class();
    // absolute horror, thx using serialized fields
    return $carbon->getAll( 'id', array( 'groups like \'%"' . $this->id . '"%\'' ) );
  }
}
