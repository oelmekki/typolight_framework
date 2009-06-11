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
  protected $templateClass;
  protected $controller;
  protected $action;
  protected $strTemplate;
  protected $isJson;
  protected $sendJson = false;
  protected $lang;
  protected $arrCache = array();
  protected $uncachable = array();
  protected $arrActions = array();

  public function __construct( Database_Result $objModule, $strColumn = 'main', $templateClass = 'FrontendTemplate' )
  {
    $this->uncachable[] = 'template';
    parent::__construct( $objModule, $strColumn );
    $this->templateClass = $templateClass;
    $chunks = explode( '?', $this->Environment->request );
    $this->lang = $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ get_class( $this ) ];

    $this->isJson = ( substr( $chunks[0], -5 ) == '.json' );
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
      $this->action = $this->defaultRoutedAction;
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
    $templateClass = $this->templateClass;

    $GLOBALS[ 'TL_JAVASCRIPT' ][] = 'system/modules/framework/js/addLiveEvent.js';


    $this->Template = new $templateClass( 'fe_' . $this->controller . '_' . $action );

    $this->$action();
    $this->Template->lang = $this->lang;

    $this->Template->pagename = $this->pagename;
    $this->Template->absolute_pagename = $this->absolutePagename;

    if ( $this->isJson and $this->sendJson )
    {
      echo $this->Json->encode();
      die();
    }

    return $this->Template->parse();
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
  protected function getPagename()
  {
    return preg_replace( '/\?.*/', '', ampersand($this->Environment->request, ENCODE_AMPERSANDS) ) ;
  }



  /** Convenient method to find out the absolute url of the current page ( without parameters )
   * @return string
   */
  protected function getAbsolutePagename()
  {
    return sprintf( 'http://%s%s/%s', $_SERVER[ 'SERVER_NAME' ], $GLOBALS[ 'TL_CONFIG' ][ 'websitePath' ], $this->pagename );
  }



  /**
   * Get the list of executable actions
   */
  public function getActions()
  {
    return $this->arrActions;
  }



  /**
   * Return the template
   * ( mainly useful for functional testing internals )
   */
  public function getView()
  {
    return $this->Template;
  }
}


