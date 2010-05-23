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
 * Class FrontendController 
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki 
 * @package    Controller
 */
abstract class FrontendController extends Module
{
  protected $templateClass;
  protected $controller;
  protected $action;
  protected $strTemplate;
  protected $isJson;
  protected $sendJson = array();
  protected $lang;
  protected $arrCache     = array();
  protected $uncachable   = array();
  protected $arrActions   = array();


  /**
   * Set a template name in this variable
   * to bypass the default template generation based
   * on the scheme mod_ + controllername + _ + actionname
   **/
  protected $render;


  /**
   * beforeFilters are methods to be executed before actions.
   * Specify an array of method names.
   *
   * If you give a simple string, the method will be executed
   * before every method.
   *
   * if you give an array, specify the method name as key
   * and specify a list of actions as 'except' or 'only' keys.
   * This way, the method will be executed only or at the exception
   * of the actions specified.
   * The value of 'except' and 'only' keys can be the method name
   * to execute, as string, or an array of method names.
   *
   * You can use the magic method name "pass<em>Something</em>" to
   * pass an attribute to the template. This also works with getters.
   * passSomething will execute :
   * $this->Template->something = $this->something;
   *
   * example :
   * protected $beforeFilter = array(
   *   'checkCredential',
   *   'getParentId'          => array( 'only' => 'create' ),
   *   'getItemId'            => array( 'except' => 'index' ),
   *   'importItemJavascript' => array( 'only' => array( 'index', 'show', 'search' ) ),
   *   'passSitename',
   * );
   **/
  protected $beforeFilters = array();


  /**
   * afterFilters acts just like beforeFilters, but are executed after the action.
   **/
  protected $afterFilters = array();



  public function __construct( Database_Result $objModule, $strColumn = 'main', $templateClass = 'FrontendTemplate' )
  {
    parent::__construct( $objModule, $strColumn );

    $this->uncachable[]   = 'template';
    $this->templateClass  = $templateClass;
    $this->lang           = $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ get_class( $this ) ];
    $this->objModule      = $objModule;

    $chunks       = explode( '?', $this->Environment->request );
    $this->isJson = ( substr( $chunks[0], -5 ) == '.json' );
    if ( $this->isJson )
    {
      $this->import( 'Json' );
    }

    $session = $this->Session->getData();

    if ( count( $session[ 'TL_MSG' ] ) )
    {
      $GLOBALS[ 'TL_MSG' ] = $session[ 'TL_MSG' ];
      unset( $session[ 'TL_MSG' ] );
      $this->Session->setData( $session );
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

    // use a fake template for now, just in case of redirection action
    $this->Template = (object) array();

    if (strlen($this->arrData['space'][0]))
    {
      $this->arrStyle[] = 'margin-top:'.$this->arrData['space'][0].'px;';
    }

    if (strlen($this->arrData['space'][1]))
    {
      $this->arrStyle[] = 'margin-bottom:'.$this->arrData['space'][1].'px;';
    }

    $this->cssID = deserialize($this->objModule->cssID, true);
    $this->Template->style    = count($this->arrStyle) ? implode(' ', $this->arrStyle) : '';
    $this->Template->cssID    = strlen($this->cssID[0]) ? ' id="' . $this->cssID[0] . '"' : '';
    $this->Template->class    = trim('mod_' . $this->type . ' ' . $this->cssID[1]);
    $this->Template->headline = $this->headline;
    $this->Template->hl       = $this->hl;
    $this->Template->action   = $this->action;


    $this->executeBeforeFilters();
    $this->$action();
    $this->executeAfterFilters();

    // create the real template
    $faked = (array) $this->Template;
    $this->Template = new $templateClass( strlen( $this->render ) ? $this->render : 'mod_' . $this->controller . '_' . $action );
    $this->Template->setData( $faked );

    $this->Template->lang               = $this->lang;
    $this->Template->pagename           = $this->pagename;
    $this->Template->absolute_pagename  = $this->absolutePagename;
    $this->Template->helper             = $this->helper;

    if ( $this->isJson )
    {
      echo $this->Json->encode();
      die();
    }

    return $this->Template->parse();
  }


  /**
   * Pass a message to the next action.
   * The message will be put in the $GLOBALS[ 'TL_MSG' ] array,
   * at the $level key ( default to 'info' ).
   *
   * @param string    the message to pass
   * @param string    the level concerned ( eg: info, warn, error ... )
   **/
  protected function passMessage( $message, $level = 'info' )
  {
    $session = $this->Session->getData();
    $session[ 'TL_MSG' ][ $level ][] = $message;
    $this->Session->setData( $session );
  }



  /**
   * Override redirection not to kill the testing suite
   */
  protected function redirect( $strLocation, $intStatus = 303 )
  {
    if ( $GLOBALS[ 'TEST_ENV' ] and class_exists( 'RedirectionException' ) )
    {
      echo "Redirected\n";
      throw new RedirectionException( $strLocation, $intStatus, 'redirected' );
    }

    else
    {
      parent::redirect( $strLocation, $intStatus );
    }
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
   * Prepare template for pagination
   * Pagination should have been executed on model
   *
   * If $route is given, the pagination links will
   * be created with Route::compose(), using $routeParameters.
   *
   * If not, they wil be created with System#addToUrl()
   *
   * @arg EModel        the paginated model
   * @arg string        the route name
   * @arg array         the route parameters
   */
  protected function preparePagination( $model, $route = null, $routeParams = array(), $pageKey = 'page' )
  {
    $page_count = $model->paginate_page_count;
    $page       = $model->paginate_page;
    $links      = array();

    for ( $i = 1; $i <= $page_count; $i++ )
    {
      if ( $route )
      {
        $links[ $i ] = Route::compose( $route, array_merge( $routeParams, array( $pageKey => $i ) ) );
      }

      else
      {
        $links[ $i ] = $this->addToUrl( "$pageKey=$i" );
      }
    }

    $pagination = new FrontendTemplate( 'mod_framework_pagination' );
    $pagination->links      = $links;
    $pagination->page_count = $page_count;
    $pagination->selected   = $page;
    $pagination->lang       = $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'framework_pagination' ];

    $this->pagination = $pagination->parse();
    return $this->pagination;
  }



  /**
   * Default getPage method to retrieve current page for pagination
   *
   * @return integer  the page number
   **/
  protected function getPage()
  {
    $page = $this->Input->get( 'page' );
    if ( ! $page )
    {
      $page = 1;
    }

    return $page;
  }



  private function executeBeforeFilters()
  {
    $this->executeFilters( $this->beforeFilters );
  }


  private function executeAfterFilters()
  {
    $this->executeFilters( $this->afterFilters );
  }


  private function executeFilters( $filters )
  {
    foreach ( $filters as $key => $filter )
    {
      if ( is_array( $filter ) )
      {
        $method = $key;

        if ( is_string( $filter[ 'only' ] ) and $this->action != $filter[ 'only' ] )
        {
          continue;
        }

        if ( is_array( $filter[ 'only' ] ) and ! in_array( $this->action, $filter[ 'only' ] ) )
        {
          continue;
        }

        if ( is_string( $filter[ 'except' ] ) and $this->action == $filter[ 'except' ] )
        {
          continue;
        }

        if ( is_array( $filter[ 'except' ] ) and in_array( $this->action, $filter[ 'except' ] ) )
        {
          continue;
        }
      }

      else
      {
        $method = $filter;
      }

      if ( strpos( $method, 'pass' ) === 0 )
      {
        $attr         = str_replace( 'pass', '', $method );
        $firstLetter  = substr( $attr, 0, 1 );
        $rest         = substr( $attr, 1 );
        $attr         = strtolower( $firstLetter ) . $rest;

        $this->Template->$attr = $this->$attr;
      }

      else
      {
        $this->$method();
      }
    }
  }
}
