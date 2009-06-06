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
 * Class FwPage
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki 
 * @package    Model
 */
class FwPage extends EModel
{
  protected $strTable   = "tl_page" ;
  protected $belongsTo  = array( 'FwPage' );

  public function getAccessible()
  {
    if ( FE_USER_LOGGED_IN )
    {
      if ( $this->guests )
      {
        return false;
      }

      $parent = $this->FwPage();
      if ( $parent->id )
      {
        $continue = true;
        while ( $continue )
        {
          if ( $parent->guests )
          {
            return false;
          }

          $parent = $parent->FwPage();
          if ( ! $parent->id )
          {
            $continue = false;
          }
        }

        return true;
      }
    }

    else
    {
      if ( $this->protected )
      {
        return false;
      }

      $parent = $this->FwPage();
      if ( $parent->id )
      {
        $continue = true;
        while ( $continue )
        {
          if ( $parent->protected )
          {
            return false;
          }

          $parent = $parent->FwPage();
          if ( ! $parent->id )
          {
            $continue = false;
          }
        }
      }

      return true;
    }
  }
}

