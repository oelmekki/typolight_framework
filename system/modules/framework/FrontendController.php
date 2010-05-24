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
 * Class FrontendController 
 *
 * @copyright  Olivier El Mekki, 2009-2010
 * @author     Olivier El Mekki 
 * @package    Controller
 *
 * ------
 * Basics
 * ------

 * A FrontendController is a mean to handle easily several actions
 * in a single module. Instead of using a huge amount of Module, or a 
 * Module and put a switch in your compile(), like "if action is show, 
 * or if action is list, or if action is delete", etc, you can spawn 
 * those automatically into several methods.
 *
 * This make sense used together with the routes, so a single module
 * can handle a whole resource, and all its pages, from the visitor
 * point of view.
 *
 * All you have to do is to create your controller class extending
 * FrontendController, give a controller name through the protected
 * controller attribute, and write your actions, prefixing them by
 * 'action_'.
 *
 * Every FrontendController should have a least an action_index action.
 * This is the default action if none is specified.
 *
 * The Template to use is determined by the controller name and the action
 * name : 
 * 'mod_' . <controller_name> . '_' . <action_name>
 *
 * So, the template for the default action of a books controller would be
 * mod_books_index.tpl . Then, if you have a action_show method, the template
 * will be mod_books_show.tpl .
 *
 * You can override the template in an action using $this->render. Set it to
 * name of the template you want to render. Don't worry about what you have
 * passed to $this->Template, the actual template is render after you runned your
 * action, and $this->Template is just a fake object from which template variables
 * will be retrieved.
 *
 *
 *
 * ------------------------
 * Before and after filters
 * ------------------------
 *
 * There often are some methods you want to call on many actions. For example, to
 * edit and delete an image, you want to ensure the person requesting it is actually
 * the one that created it. Or you may want to update the last activity timestamp of
 * a member after every action.
 * To avoid putting the method call in each action, you can use the beforeFilter and
 * afterFilter arrays. Each entry can be either a method name ( and the method will be
 * called on each action ), or an array with the method name as key. The array can contains
 * an 'only' array or an 'except' array, listing the only method names the filter should
 * apply on, or the ones it should not.
 *
 * Additionnaly, there is a shortcut to pass getter values to the template, because this is
 * a common task : you can set passSomething as method name, and the template will receive
 * $this->something.
 *
 * e.g.:
 * protected beforeFilter = array( 
 *   'passCurrentMember',
 *   'checkCredentials' => array( 'only' => array( 'edit', 'delete' ) ),
 *   'passBook'         => array( 'except' => 'index' ),
 * )
 *
 *
 *
 * -------------------
 * Getters and setters
 * -------------------
 *
 * Just like EModel and BackendController, FrontendController can handle virtual attributes through
 * getter and setter methods. Just define a function getSomething() or setSomething( $value ).
 * You can then do :
 * echo $this->something;
 * $this->something = 'foo';
 *
 * The result is cached, so you don't have to worry about speed issues. If you want 
 * to bypass the cache, you can call the method explicitly ( its result won't be cache either ).
 * If you want to never cache a virtual attribute, put its name in the $uncachable array.
 *
 * If you have defined a getter, you have a setter for free ( it will change the cached value ).
 * So, write setters only if you want to do special things rather than simply setting the value.
 *
 *
 *
 * --------------
 * Flash messages
 * --------------
 *
 * Sometimes, you would like to pass messages from an action to another. For exemple, after a POST
 * request, you may want to redirect the visitor somewhere else, to prevent the double post
 * through reloading the page. But how to let him know the query was successful if you redirect?
 * Instead of settings others actions in order to say "Ok, it's cool" or "No, it's not", you can
 * use $this->passMessage(). This method take two parameters : the message and optionnaly the warn
 * level ( any string you want, info is used by default ).
 * 
 * In the next action, in any FrontendController, you can retrieve the message through
 * $GLOBALS[ 'TL_MSG' ][ <level> ]
 *
 * To avoid handling it again and again, you can use the ModuleMessage module.
 *
 *
 *
 * ---------
 * Languages
 * ---------
 *
 * If you are designing a multi language website, accessing $GLOBALS[ 'TL_LANG' ] in deep keys can
 * be very time consuming. To make this quicker, a FrontendController is always assigned the content
 * of $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ <controllerClassName> ] as $this->lang.
 *
 * This is passed as is to the template, so you can use $this->lang in the template too.
 *
 * For every action in your controller, you can define a key in this array, in you language file,
 * to give a friendly name. This in mainly used when creating the module in the backend, if you want
 * to force an action. The key should be : <controller_name>_<action_name> .
 *
 *
 *
 * -----------------
 * Forcing an action
 * -----------------
 *
 * If you want to force an action for a specific module, you will have to change the tl_module dca
 * in your extension. For exemple, if I have defined in my config.php :
 * $GLOBALS[ 'FE_MOD' ][ 'application' ][ 'books' ] = 'ControllerBooks';
 * I add this in the tl_module.php dca file :
 * $GLOBALS[ 'TL_DCA' ][ 'tl_module' ][ 'palettes' ][ 'books' ] = 'name,type;defaultRoutedAction,forceRoutedAction;';
 *
 * This way, when I create a new books module, I can check the "force route" checkbox and set the default
 * ( and thus unique ) route to, eg, 'lastest'. This will only render action_latest for this module.
 * No worry if you then create an other module which rely on the action parameter, only the current
 * module is forced.
 *
 * This let you use FrontendControllers in your layout without taking into account the action GET
 * parameter.
 *
 *
 *
 * ---------------------------------------------------------------------------
 * But wait, the breadcrumb module show the same page for every single action!
 * ---------------------------------------------------------------------------
 *
 * Actually, even if you use routes to show various urls, the controller is on the same page, from
 * the Typolight point of view. If you want to use the actions as seperated pages in the breadcrumb,
 * use the ModuleRoutedBreadcrumb instead of the default one.
 *
 * In the same way, you can use ControllerRoutedNav instead of ModuleCustomNavigation if you want
 * to point navigation to specifics actions ( but this is more about routes than controllers, here ).
 */
abstract class FrontendController extends Module
{
  /**
   * @var string the controller name
   *
   * Must be set, so the module know which
   * template to load. The default behaviour to get
   * the template name is to use :
   * 'mod_' . $this->controller . '_' . $this->action
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
  protected $templateClass;



  /**
   * Check if a getter method exists
   *
   * @param string  the attribute name
   * @return mixed
   */
  public function __get( $key )
  {
    $firstLetter  = substr( $key, 0, 1 );
    $rest         = substr( $key, 1 );
    $getter       = 'get' . strtoupper( $firstLetter ) . $rest;

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
    $firstLetter  = substr( $key, 0, 1 );
    $rest         = substr( $key, 1 );
    $setter       = 'set' . strtoupper( $firstLetter ) . $rest;

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



  public function generate()
  {
    if ( TL_MODE == "BE" )
    {
      $objTemplate            = new BackendTemplate( 'be_wildcard' ) ;
      $objTemplate->wildcard  = '### FRAMEWORK MODULE : '. $this->name . ' ###' ;

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


    if ( ! $this->forceRoutedAction and strlen( $action ) and method_exists( $this, 'action_' . $action ) )
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
    $templateClass  = $this->templateClass;

    // use a fake template for now, just in case of redirection action
    // ( we don't want to complain about missing template if we will redirect )
    $this->Template = (object) array();

    if (strlen( $this->arrData[ 'space' ][0] ) )
    {
      $this->arrStyle[] = 'margin-top:' . $this->arrData[ 'space' ][0] . 'px;';
    }

    if (strlen($this->arrData[ 'space' ][1] ) )
    {
      $this->arrStyle[] = 'margin-bottom:' . $this->arrData[ 'space' ][1] . 'px;';
    }

    $this->cssID              = deserialize( $this->objModule->cssID, true );
    $this->Template->style    = count( $this->arrStyle ) ? implode( ' ', $this->arrStyle ) : '';
    $this->Template->cssID    = strlen( $this->cssID[0] ) ? ' id="' . $this->cssID[0] . '"' : '';
    $this->Template->class    = trim( 'mod_' . $this->type . ' ' . $this->cssID[1] );
    $this->Template->headline = $this->headline;
    $this->Template->hl       = $this->hl;
    $this->Template->action   = $this->action;


    $this->executeBeforeFilters();
    $this->$action();
    $this->executeAfterFilters();

    // create the real template
    $faked = (array) $this->Template;
    $this->Template = new $templateClass( strlen( $this->render ) ? $this->render : 'mod_' . $this->controller . '_' . $this->action );
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



  /*
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



  /*
   * Get the list of executable actions
   * ( mainly used by the tl_module dca class )
   */
  public function getActions()
  {
    $index = $this->controller . '_index';
    $actions = array( $index => (strlen( $this->lang[ $index ] ) ? $this->lang[ $index ] : $index ) );

    foreach ( get_class_methods( $this ) as $method )
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
