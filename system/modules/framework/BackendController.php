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
 * Class BackendController 
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki 
 * @package    Controller
 */
abstract class BackendController extends BackendModule
{
  protected $controller;
  protected $action;
  protected $strTemplate;
  protected $isJson;
  protected $sendJson = array();
  protected $lang;
  protected $arrCache = array();
  protected $uncachable = array();
  protected $arrActions = array();
  protected $params;
  protected $wrap = true;
  protected $href;

  public function __construct( DataContainer $objDc = null )
  {
    parent::__construct( $objDc );

    $this->uncachable[]   = 'template';
    $this->lang           = $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ get_class( $this ) ];

    $chunks       = explode( '?', $this->Environment->request );
    $this->isJson = $this->Input->get( 'json' );
    if ( $this->isJson )
    {
      $this->import( 'Json' );
    }


    // Fix referer issue on keyed actions
    $session      = $this->Session->getData();
    $httpReferer  = ampersand( $this->Environment->httpReferer, true );
    $arrReferer   = parse_url( $httpReferer );
    $referer      = $arrReferer[ 'path' ] . ( strlen( $arrReferer[ 'query' ] ) ? '?' . $arrReferer[ 'query' ] : '' );

    if ( $this->Input->get( 'key' ) and $referer != $session[ 'referer' ][ 'current' ] )
    {
       $session['referer']['last']    = $session['referer']['current'];                                                                                                                                                          
       $session['referer']['current'] = $referer;
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
   * Magic method that let you generate the backend module from
   * the config file of your extension using the "key" method.
   * give, e.g., the 'callIndex' method name to generate the
   * module with the index method.
   **/
  public function __call( $statement, $params )
  {
    if ( strpos( $statement, 'call' ) === 0 )
    {
      $method      = substr( $statement, 4 );
      $firstLetter = substr( $method, 0, 1 );
      $rest        = substr( $method, 1 );
      $method      = strtolower( $firstLetter ) . $rest;

      $this->Input->setGet( 'act', $method );
      if ( $params )
      {
        $this->params = $params;
      }

      return $this->generate();
    }

    throw new Exception( 'Unknown method: ' . $statement );
  }



  /**
   * Determine which action to launch
   * @return string
   */
  public function generate()
  {
    $this->action = 'index';
    $action = $this->Input->get( 'act' );
    if ( ! strlen( $action ) and strlen( $action = $this->Input->post( 'act' ) ) )
    {
      $action = array_search( $action, $this->lang );
    }


    if ( strlen( $action ) and method_exists( $this, $action ) )
    {
      // if the action is not prefixed by 'action_', only authorized it if it is in the $arrActions array
      if ( method_exists( $this, $action ) and in_array( $action, $this->arrActions ) )
      {
        $this->action = $action;
      }

      // whitelist methods prefixed by 'action_'
      else if ( method_exists( $this, 'action_' . $action ) )
      {
        $this->action = 'action_' . $action;
      }
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

    $wrapper        = new BackendTemplate( 'be_framework_controller' );

    // use a fake template for now, just in case of redirection action
    $this->Template = (object) array();

    $wrapper->title    = specialchars($GLOBALS['TL_LANG']['MSC']['backBT']);

    $this->Template->lang               = $this->lang;
    $this->Template->pagename           = $this->pagename;
    $this->Template->absolute_pagename  = $this->absolutePagename;


    $this->$action();

    // create the real template
    $faked = (array) $this->Template;
    $this->Template = new BackendTemplate( 'be_' . $this->controller . '_' . $action );
    $this->Template->setData( $faked );

    if ( $this->isJson )
    {
      echo $this->Json->encode();
      die();
    }

    if ( $this->wrap )
    {
      
      if ( ! strlen( $this->href ) )
      {
        $this->href = $this->getReferer( true );
      }

      $wrapper->main = $this->Template->parse();
      $wrapper->href = $this->href;
      return $wrapper->parse();
    }

    return $this->Template->parse();
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
   * @arg integer       the total number of pages
   * @arg integer       the current page number
   */
  protected function preparePagination( $model )
  {
    $page_count = $model->paginate_page_count;
    $page       = $model->paginate_page;
    $links      = array();

    for ( $i = 1; $i <= $page_count; $i++ )
    {
      $links[ $i ] = $this->addToUrl( "paginate=$i" );
    }

    $pagination = new BackendTemplate( 'mod_framework_pagination' );
    $pagination->links      = $links;
    $pagination->page_count = $page_count;
    $pagination->selected   = $page;
    $pagination->lang       = $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'framework_pagination' ];

    $this->pagination = $pagination->parse();
  }



  /**
   * Redirect to the error page
   **/
  protected function error( $log = null )
  {
    if ( $log )
    {
      $this->log( $log, 'BackendController error()', TL_GENERAL );
      error_log( $log );
    }

    $this->redirect( $this->Environment->script . '?act=error' );
  }



  /**
   * Set the referer
   **/
  public function setReferer( $current, $last = null )
  {
    if ( $last = null )
    {
      $session = $this->Session->getData();
      $last = $session[ 'referer' ][ 'current' ];
    }

    $this->Session->set( 'referer', array( 'current' => $current, 'last' => $last ) );
  }
}
