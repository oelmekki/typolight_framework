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
  protected $treeAssoc  = true;

  public function getAccessible()
  {
    if ( FE_USER_LOGGED_IN )
    {
      if ( $this->guests )
      {
        return false;
      }

      $parent = new FwPage( $this->pid );
      if ( $parent->id )
      {
        $continue = true;
        while ( $continue )
        {
          if ( $parent->guests )
          {
            return false;
          }

          $parent = new FwPage( $parent->pid );
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

      $parent = new FwPage( $this->pid );
      if ( $parent->id )
      {
        $continue = true;
        while ( $continue )
        {
          if ( $parent->protected )
          {
            return false;
          }

          $parent = new FwPage( $parent->pid );
          if ( ! $parent->id )
          {
            $continue = false;
          }
        }
      }

      return true;
    }
  }


  public function getRoot()
  {
    $page = $this;
    while ( $page->pid != 0 )
    {
      $page = new FwPage( $page->pid );
    }

    return $page;
  }


  public function insertAfter( $page )
  {
    if ( is_numeric( $page ) )
    {
      $page = new FwPage( $page );
    }


    if ( $page->id )
    {
      $pid = $page->pid;

      if ( ! is_numeric( $pid ) )
      {
        throw( new Exception( "Page " . $page->id . " has no pid" ) );
      }

      $objNextSorting = $this->Database->prepare( 'select min(sorting) as sorting from tl_page where pid = ? and sorting > ?' )
                                       ->execute( $pid, $page->sorting );

      if ( ! is_null( $objNextSorting->sorting ) )
      {
        $nextSorting = $objNextSorting->sorting;

        if ( ( ( $page->sorting + $nextSorting ) % 2 ) != 0 or $nextSorting >= 4294967295 )
        {
          $count          = 1;
          $objNewSorting  = $this->Database->prepare( 'select id, sorting from tl_page where pid = ? order by sorting' )
                                           ->execute( $pid );

          while ( $objNewSorting->next() )
          {
            $this->Database->prepare( 'update tl_page set sorting = ? where id = ?' )
                           ->execute( ( $count++ * 128 ), $objNewSorting->id );

            if ( $objNewSorting->id == $page->id )
            {
              $newSorting = ( $count++ * 128 );
            }
          }
        }

        else
        {
          $newSorting = ( ( $page->sorting + $nextSorting ) / 2 );
        }
      }

      else
      {
        $newSorting = ( $page->sorting + 128 );
      }

      $this->pid      = $pid;
      $this->sorting  = intval( $newSorting );

      return $this->save();
    }

    return false;
  }

  public function insertInto( $page )
  {
    if ( is_numeric( $page ) )
    {
      $page = new FwPage( $page );
    }

    if ( $page->id )
    {
      $objSorting = $this->Database->prepare( "select min(sorting) as sorting from tl_page where pid = ?" )
                                   ->execute( $page->id );

      if ( $objSorting->numRows )
      {
        $curSorting = $objSorting->sorting;


        // rehash sorting
        if ( ( $curSorting % 2 ) != 0 || $curSorting < 1 )
        {
          $objNewSorting = $this->Database->prepare( 'select id, sorting from tl_page where pid = ? order by sorting' )
                                          ->execute( $page->id );
          $count      = 2;
          $newSorting = 128;

          while ( $objNewSorting->next() )
          {
            $this->Database->prepare( 'update tl_page set sorting = ? where id = ?' )->limit(1)
                           ->execute( ( $count++ * 128 ), $objNewSorting->id );
          }
        }

        else
        {
          $newSorting = $curSorting / 2;
        }
      }

      else
      {
        $newSorting = 128;
      }

      $this->pid      = $page->id;
      $this->sorting  = intval( $newSorting );

      return $this->save();
    }

    return false;
  }
}

