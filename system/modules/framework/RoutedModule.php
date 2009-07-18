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
  protected $sendJson = array();
  protected $lang;
  protected $arrCache = array();
  protected $uncachable = array();
  protected $arrActions = array();

  public function __construct( Database_Result $objModule, $strColumn = 'main', $templateClass = 'FrontendTemplate' )
  {
    parent::__construct( $objModule, $strColumn );

    $this->uncachable[]   = 'template';
    $this->templateClass  = $templateClass;
    $this->lang           = $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ get_class( $this ) ];

    $chunks       = explode( '?', $this->Environment->request );
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

    $this->action = $this->defaultRoutedAction;

    if ( ! strlen( $this->action ) )
    {
      $this->action = 'index';
    }


    if ( strpos( $this->action, $this->controller . '_' ) === 0 )
    {
      $this->action = str_replace( $this->controller . '_', '', $this->action );
    }


    $action = $this->Input->get( 'action' );
    if ( strpos( $action, $this->controller . '_' ) === 0 )
    {
      $action = str_replace( $this->controller . '_', '', $action );
    }


    if ( ! $this->forceRoutedAction and strlen( $action ) and method_exists( $this, $action ) )
    {
      $this->action = $action;
    }

    if ( $this->isJson and ! in_array( $this->action, $this->sendJson ) )
    {
      return '';
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
    $GLOBALS[ 'TL_JAVASCRIPT' ][] = 'system/modules/framework/js/Preloader.js';


    $this->Template = new $templateClass( 'fe_' . $this->controller . '_' . $action );
    if (strlen($this->arrData['space'][0]))
    {
      $this->arrStyle[] = 'margin-top:'.$this->arrData['space'][0].'px;';
    }

    if (strlen($this->arrData['space'][1]))
    {
      $this->arrStyle[] = 'margin-bottom:'.$this->arrData['space'][1].'px;';
    }

    $this->Template->style = count($this->arrStyle) ? implode(' ', $this->arrStyle) : '';
    $this->Template->cssID = strlen($this->cssID[0]) ? ' id="' . $this->cssID[0] . '"' : '';
    $this->Template->class = trim('mod_' . $this->type . ' ' . $this->cssID[1]);

    $this->Template->headline = $this->headline;
    $this->Template->hl = $this->hl;


    $this->$action();
    $this->Template->lang = $this->lang;

    $this->Template->pagename = $this->pagename;
    $this->Template->absolute_pagename = $this->absolutePagename;

    if ( $this->isJson )
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
   * Override redirection not to kill the testing suite
   */
  protected function redirect( $strLocation, $intStatus = 303 )
  {
    if ( $GLOBALS[ 'TEST_ENV' ] and class_exists( 'RedirectionException' ) )
    {
      throw new RedirectionException( $strLocation, $intStatus, 'redirected' );
    }

    else
    {
      parent::redirect( $strLocation, $intStatus );
    }
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
    $index = $this->controller . '_index';
    $actions = array( $index => (strlen( $this->lang[ $index ] ) ? $this->lang[ $index ] : $index ) );

    foreach ( $this->arrActions as $action )
    {
      $fullname = $this->controller . '_' . $action;
      if ( strlen( $this->lang[ $fullname ] ) )
      {
        $actions[ $fullname ] = $this->lang[ $fullname ];
      }

      else
      {
        $actions[ $fullname ] = $fullname;
      }
    }

    return $actions;
  }



  /**
   * Return the template
   * ( mainly useful for functional testing internals )
   */
  public function getView()
  {
    return $this->Template;
  }



  /**
   * Resize an image by width
   * @param int     width
   * @param string  the path of the file to resize
   * return string  path to the renderer
   */
  public function resizeImageByWidth( $width, $path )
  {
    return $GLOBALS[ 'websitePath' ] . '/system/modules/framework/ImageRenderer.php?action=resizer&type=width&file=' . urlencode( $path ) . '&value=' . $width;
  }



  /**
   * Resize an image by height
   * @param int     height
   * @param string  the path of the file to resize
   * return string  path to the renderer
   */
  public function resizeImageByHeight( $height, $path )
  {
    return $GLOBALS[ 'websitePath' ] . '/system/modules/framework/ImageRenderer.php?action=resizer&type=height&file=' . urlencode( $path ) . '&value=' . $height;
  }



  /**
   * Helper to do some pagination
   *
   * @param   mixed     collection of objects on which pagination will be done
   * @param   integer   number of items per page
   * @return  mixed     the extract of the collection
   */
  protected function paginate( $collection, $perPage )
  {
    $page = $this->Input->get( 'paginate' );
    if ( ! ( strlen( $page ) and is_numeric( $page ) ) )
    {
      $page = 1;
    }

    if ( ! is_array( $collection ) )
    {
      return false;
    }

    $item_count = count( $collection );
    $start      = ( $page - 1 ) * $perPage;

    if ( $start > $item_count )
    {
      $start = 0;
    }


    $page_count = ceil( $item_count / $perPage );
    $pagename   = preg_replace( '/(\&|\?)paginate=\d+/', '',  $this->Environment->request);

    $links = array();
    for ( $i = 1; $i <= $page_count; $i++ )
    {
      if ( strpos( $pagename, '?' ) !== false )
      {
        $links[ $i ] = $pagename . '&paginate=' . $i;
      }

      else
      {
        $links[ $i ] = $pagename . '?paginate=' . $i;
      }
    }


    $pagination = new FrontendTemplate( 'fe_framework_pagination' );
    $pagination->links      = $links;
    $pagination->page_count = $page_count;
    $pagination->selected   = $page;
    $pagination->lang       = $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'framework_pagination' ];

    $this->pagination = $pagination->parse();

    $selected = array_slice( $collection, $start, $perPage );
    return $selected;
  }
}


