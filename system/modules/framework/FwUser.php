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
 * Class FwUser
 *
 * @copyright  Olivier El Mekki, 2010 
 * @author     Olivier El Mekki 
 * @package    Model
 */
class FwUser extends FwRegistered
{
  protected $strTable     = "tl_user" ;
  protected $group_table  = "tl_user_group";


  /**
   * Turn the model to the logged user
   *
   * Return false if the visitor isn't logged in,
   * true otherwise.
   *
   * @return boolean
   **/
  public function toCurrent()
  {
    $legacy = BackendUser::getInstance();
    if ( $legacy->id )
    {
      $this->found = $legacy->getData();
      return true;
    }

    return false;
  }
}
