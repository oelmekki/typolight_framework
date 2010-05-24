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
 * @copyright  Olivier El Mekki, 2009-2010
 * @author     Olivier El Mekki 
 * @package    Framework
 * @license    LGPL 
 * @filesource
 */


/**
 * Class BackendController 
 *
 * @copyright  Olivier El Mekki, 2009-2010
 * @author     Olivier El Mekki 
 * @package    Controller
 *
 * ------
 * Basics
 * ------

 * A BacktendController is a mean to handle easily several actions
 * in a single backed module, just like FrontendController does for
 * frontend modules. 
 *
 * Every BackendController should have a least an action_index action.
 * This is the default action if none is specified.
 *
 * The Template to use is determined by the controller name and the action
 * name : 
 * 'be_' . <controller_name> . '_' . <action_name>
 *
 * So, the template for the default action of a books controller would be
 * be_books_index.tpl . Then, if you have a action_show method, the template
 * will be be_books_show.tpl .
 *
 * You can override the template in an action using $this->render. Set it to
 * name of the template you want to render. Don't worry about what you have
 * passed to $this->Template, the actual template is render after you runned your
 * action, and $this->Template is just a fake object from which template variables
 * will be retrieved.
 *
 *
 * ------------------------
 * Before and after filters
 * ------------------------
 *
 * Works like FrontendController.
 *
 *
 *
 * -------------------
 * Getters and setters
 * -------------------
 *
 * Works like FrontendController.
 *
 *
 *
 * --------------
 * Flash messages
 * --------------
 *
 * Works like FrontendController.
 *
 *
 *
 * ---------
 * Languages
 * ---------
 *
 * Works like FrontendController.
 *
 *
 *
 * -------------------------------------------
 * Calling an action from BE_MOD in config.php
 * -------------------------------------------
 *
 * If you use a dca, you may want to use the 'key' key of the BE_MOD
 * configuration to call a specific action.
 *
 * This can be done passing the method name as callSomething to call
 * the 'action_something' action. The params, like the dca object,
 * will be put in $this->params.
 *
 */
abstract class BackendController extends BackendModule
{
  /**
   * @var string the controller name
   *
   * Must be set, so the module know which
   * template to load. The default behaviour to get
   * the template name is to use :
   * 'be_' . $this->controller . '_' . $this->action
   **/
  protected $controller;



  /**
   * @var string the action name
   *
   * It is automatically set, but you can use this attribute
   * to know in which action you are ( it is passed to the
   * template )
   **/
  protected $action;



  /**
   * @var boolean is the request a json one?
   *
   * This attribute is set to true if the requested page has the
   * .json extension.
   **/
  protected $isJson;



  /**
   * @var array the list of action that can be request through json.
   *
   * If an action is in this array and the request is a json one,
   * you will be able to pass data to $this->Json, just like a template.
   **/
  protected $sendJson = array();



  /**
   * @var array the language array
   *
   * This attribute is a shortcut to access
   * $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ $this->controller ]
   **/
  protected $lang;



  /**
   * @var array the list of uncachable pseudo attributes
   *
   * When you define a getter through a method like getSomething
   * and request then $this->something, the result is cached for
   * further calls.
   *
   * If you do not this to happens, put this attribute name in this
   * array.
   **/
  protected $uncachable = array();


  /**
   * @var array the passed params
   *
   * If you want to call a specific action with the 'key' key in the definition
   * of a backend module in config.php, you can do it using call<TheAction>.
   * parameters, such as the dca object, will be put in this attribute.
   **/
  protected $params;



  /**
   * @var boolean wrap the action in a layout.
   *
   * By default, a layout is generated around the returned string of an action.
   * It contains mostly the back link and a container.
   *
   * If you want to prevent this ( for example, if you want the action to be used with
   * label_callback ), you can set this to false in your action.
   **/
  protected $wrap = true;



  /**
   * @var string back link
   *
   * Set this attribute in an action if you want to force the back
   * link instead of using the referer.
   **/
  protected $href;



  /**
   * @var string template name
   *
   * Set a template name in this attribute
   * to bypass the default template generation based
   * on the scheme mod_ + controllername + _ + actionname
   **/
  protected $render;


  /**
   * @var array   before filters
   *
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
   * @var array   after filters
   *
   * afterFilters acts just like beforeFilters, but are executed after the action.
   **/
  protected $afterFilters = array();



  protected $arrCache = array();



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

    $session = $this->Session->getData();

    if ( count( $session[ 'TL_MSG' ] ) )
    {
      $GLOBALS[ 'TL_MSG' ] = $session[ 'TL_MSG' ];
      unset( $session[ 'TL_MSG' ] );
      $this->Session->setData( $session );
    }
  }



  public function generate()
  {
    $this->action = 'index';
    $action = $this->Input->get( 'act' );
    if ( ! strlen( $action ) and strlen( $action = $this->Input->post( 'act' ) ) )
    {
      $action = array_search( $action, $this->lang );
    }


    if ( strlen( $action ) and method_exists( $this, 'action_' . $action ) )
    {
      $this->action = $action;
    }

    if ( $this->isJson and ! in_array( $this->action, $this->sendJson ) )
    {
      return '';
    }

    return $this->compile();
  }



  public function compile()
  {
    $action         = 'action_' . $this->action;
    $wrapper        = new BackendTemplate( 'be_framework_controller' );

    // use a fake template for now, just in case of redirection action
    $this->Template = (object) array();

    $wrapper->title    = specialchars($GLOBALS['TL_LANG']['MSC']['backBT']);

    $this->Template->lang               = $this->lang;
    $this->Template->pagename           = $this->pagename;
    $this->Template->absolute_pagename  = $this->absolutePagename;


    $this->executeBeforeFilters();
    $this->$action();
    $this->executeAfterFilters();

    // create the real template
    $faked          = (array) $this->Template;
    $this->Template = new BackendTemplate( strlen( $this->render ) ? $this->render : 'be_' . $this->controller . '_' . $this->action );
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



  /*
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



  /*
   * Get the list of executable actions
   */
  public function getActions()
  {
    $index = $this->controller . '_index';
    $actions = array( $index => (strlen( $this->lang[ $index ] ) ? $this->lang[ $index ] : $index ) );

    foreach ( get_class_methods( $this ) as $methods )
    {
      if ( strpos( $method, 'action_' ) === 0 )
      {
        $action   = substr( $method, 7 );
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
    }

    return $actions;
  }



  /*
   * Return the template
   * ( mainly useful for functional testing internals )
   */
  public function getView()
  {
    return $this->Template;
  }



  protected function executeBeforeFilters()
  {
    $this->executeFilters( $this->beforeFilters );
  }


  protected function executeAfterFilters()
  {
    $this->executeFilters( $this->afterFilters );
  }


  protected function executeFilters( $filters )
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
