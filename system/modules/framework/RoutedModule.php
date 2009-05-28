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
 * Class RoutedModule 
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki 
 * @package    Controller
 */
abstract class RoutedModule extends Module
{
  protected $controller;
  protected $action;
  protected $lastFlash = array();
  protected $nextFlash = array();
  protected $strTemplate;
  protected $isJson;
  protected $sendJson = false;
  protected $lang;

  public function __construct( Database_Result $objModule, $strColumn = 'main' )
  {
    parent::__construct( $objModule, $strColumn );
    $chunks = explode( '?', $this->Environment->request );
    $this->lastFlash = $_SESSION[ 'flash' ];
    $this->lang = $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ get_class( $this ) ];

    $this->isJson = ( substr( $chunks[0], -5 ) == '.json' );
    if ( $this->isJson )
    {
      $this->import( 'Json' );
    }

  }

  public function __destruct()
  {
    $_SESSION[ 'flash' ] = $this->nextFlash ;
  }



  /**
   * Determine which action to launch
   * @return string
   */
  public function generate()
  {
    if ( TL_MODE == "BE" )
    {
      $objTemplate = new BackendTemplate( 'be_wildcard' ) ;
      $objTemplate->wildcard = '### FRAMEWORK MODULE : '. $this->name . ' ###' ;

      return $objTemplate->parse() ;
    }

    $action = $this->Input->get( 'action' );
    if ( strlen( $action ) and method_exists( $this, $action ) )
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
    $action = $this->action;
    $GLOBALS[ 'TL_JAVASCRIPT' ][] = 'system/modules/framework/js/addLiveEvent.js';

    $layout = 'fe_' . $this->controller . '_layout';
    try
    {
      $this->getTemplate( $layout );
      $layout = new FrontendTemplate( $layout );
    }
    catch ( Exception $e )
    {
      $layout = false;
    }

    $this->Template = new FrontendTemplate( 'fe_' . $this->controller . '_' . $action );

    $this->$action();
    $this->Template->flash = $this->lastFlash;
    $this->Template->lang = $this->lang;

    if ( $this->isJson and $this->sendJson )
    {
      echo $this->Json->encode();
      die();
    }

    if ( $layout )
    {
      $layout->content = $this->Template->parse();
      $layout->flash = $this->lastFlash;
      $layout->lang = $this->lang;
      return $layout->parse();
    }

    else
    {
      return $this->Template->parse();
    }
  }



  /**
   * Set a message for the next action
   * @param mixed
   * @param mixed
   */
  protected function flash( $key, $message )
  {
    $this->nextFlash[ $key ] = $message;
  }



  /**
   * find out if a param exists and has the good type
   * @param string
   * @param string
   * @param string
   * @return boolean
   */
  protected function hasParam( $param, $type = 'string', $method = 'get' )
  {

    switch ( $type )
    {
    case "string":
      if ( ! strlen( $this->Input->$method( $param ) ) )
      {
        return false;
      }
      return true;
      break;

    case "int":
    case "integer":
      if ( ! ( strlen( $this->Input->$method( $param ) ) and is_numeric( $this->Input->$method( $param ) ) ) )
      {
        return false;
      }
      return true;
      break;

    case "array":
      if ( ! is_array( $this->Input->$method( $param ) ) )
      {
        return false;
      }
      return true;
      break;
    }

    return false;
  }



  /**
   * Convenient method to check if id is present
   * @param string
   * @return boolean
   */
  protected function hasId( $method = 'get' )
  {
    return $this->hasParam( 'id', 'integer', $method );
  }



  /**
   * Retrieve a route from its name and redirect to it
   * @param string
   * @param integer
   */
  public function redirectTo( $name, $params = array(), $anchor='' )
  {
    if ( strlen( $url = Route::compose( $name, $params ) ) )
    {
      if ( strlen( $anchor ) )
      {
        $url .= '#' . $anchor;
      }

      $this->redirect( $url );
      return true;
    }

    throw new Exception( 'No route match the name ' . $name );
  }



  /**
   * Convenient method to redirect to the default route
   */
  protected function redirectInvalid()
  {
    $this->redirect( $this->index() );
  }



  /**
   * Convenient method to find out the name of the current page ( without parameters )
   * @return string
   */
  protected function pagename()
  {
    return preg_replace( '/\?.*/', '', ampersand($this->Environment->request, ENCODE_AMPERSANDS) ) ;
  }



  /** Convenient method to find out the absolute url of the current page ( without parameters )
   * @return string
   */
  protected function absolute_pagename()
  {
    return sprintf( 'http://%s%s/%s', $_SERVER[ 'SERVER_NAME' ], $GLOBALS[ 'TL_CONFIG' ][ 'websitePath' ], $this->pagename() );
  }

}


?>
