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
 * @package    StickInnov 
 * @license    LGPL 
 * @filesource
 */


/**
 * Class EventBackendModule 
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Controller
 */
class EventBackendModule extends BackendModule
{
  protected $strTemplate;
  protected $action;
  protected $isJson;
  protected $sendJson;
  protected $arrCache = array();
  protected $uncachable = array();



  public function __construct( DataContainer $objDc = null )
  {
    parent::__construct( $objDc );

    $this->lang = $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ get_class( $this ) ];
    $this->import( 'Session' );

    $chunks = explode( '?', $this->Environment->request );
    $this->isJson = ( $this->Input->get( 'json' ) == 'true' );
    if ( $this->isJson )
    {
      $this->import( 'Json' );
    }
  }



  /**
   * Check if a getter method exists
   *
   * @param string  the attribute name
   * @return mixed
   */
  public function __get( $key )
  {
    $firstLetter = substr( $key, 0, 1 );
    $rest = substr( $key, 1 );
    $getter = 'get' . strtoupper( $firstLetter ) . $rest;

    if ( method_exists( $this, $getter ) )
    {
      if ( array_key_exists( $key, $this->arrCache ) and ! in_array( $key, $this->uncachable ) )
      {
        return $this->arrCache[ $key ];
      }

      $result = $this->$getter();
      if ( ! in_array( $key, $this->uncachable ) )
      {
        $this->arrCache[ $key ] = $result;
      }

      return $result;
    }

    return parent::__get( $key );
  }



  /**
   * Check if a setter method exists
   *
   * @param string  the attribute name
   * @param string  the attribute value
   * @return mixed
   */
  public function __set( $key, $value )
  {
    $firstLetter = substr( $key, 0, 1 );
    $rest = substr( $key, 1 );
    $setter = 'set' . strtoupper( $firstLetter ) . $rest;

    if ( method_exists( $this, $setter ) )
    {
      $this->arrCache[ $key ] = $this->$setter( $value );
    }

    else
    {
      $getter = 'get' . strtoupper( $firstLetter ) . $rest;
      if ( method_exists( $this, $getter ) )
      {
        $this->arrCache[ $key ] = $value;
        return true;
      }
    }

    return parent::__set( $key, $value );
  }



  /**
   * Check if the record exists
   */
  public function generate()
  {
    $method = ( count( $_POST ) > 0 ? 'post' : 'get' );
    $post = $this->Input->post( 'action' );
    if ( is_array( $post ) )
    {
      foreach ( $post as $name => $v )
      {
        if ( strlen( $v ) )
        {
          $postAction = $name;
          break;
        }
      }
    }

    $action = sprintf( '%s_%s', $method, ( $postAction ? $postAction : $this->Input->get( 'action' ) ) );

    if ( method_exists( $this, $action ) )
    {
      $this->action = $action;
    }

    else
    {
      $this->action = 'index';
    }

    return $this->compile();
  }




  /**
   * Parse the template and the layout
   * @return string
   */
  public function compile()
  {
    $GLOBALS[ 'TL_JAVASCRIPT' ][] = 'system/modules/framework/js/addLiveEvent.js';
    $action = $this->action;

    if ( $action == 'index' )
    {
      $this->Template = new BackendTemplate( $this->strTemplate );
      $this->Template->lang = $this->lang;
      $this->Template->pagename = $this->pagename;
    }

    $this->$action();

    if ( $this->isJson and $this->sendJson )
    {
      echo $this->Json->encode();
      die();
    }

    else
    {
      if ( $action == 'index' )
      {
        return $this->Template->parse();
      }

      else
      {
        $this->redirect( $this->pagename );
      }

    }
  }



  /**
   * Convenient method to find out the name of the current page
   * @return string
   */
  public function getPagename()
  {
    $matches = array();
    preg_match( '/.*?\?do=[^&]+/', ampersand( $this->Environment->request, ENCODE_AMPERSANDS ), $matches );
    return $matches[0];
  }
}

