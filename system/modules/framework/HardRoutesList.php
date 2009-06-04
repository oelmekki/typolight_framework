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
 * Class HardRoutesList 
 *
 * A module that list routes defined in modules config
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Controller
 */
class HardRoutesList extends EventBackendModule
{
  /**
   * Template
   * @var string
   */
  protected $strTemplate = 'be_framework_route_list';



  /**
   * Generate the widget and return it as string
   * @return string
   */
  protected function index()
  {
    $GLOBALS[ 'TL_JAVASCRIPT' ][] = 'system/modules/framework/js/Toggable.js';
    $GLOBALS[ 'TL_CSS' ][] = 'system/modules/framework/css/Toggable.css';
    $GLOBALS[ 'TL_JAVASCRIPT' ][] = 'system/modules/framework/js/backend/HardRoutesList.js';
    $GLOBALS[ 'TL_CSS' ][] = 'system/modules/framework/css/backend/HardRoutesList.css';
    $route = new Route();
    $routes = $route->routesFromConf;
    $this->Template->routes = $routes;
  }



  /**
   * Load a route in database
   */
  protected function get_load_route()
  {
    $this->sendJson = true;
    $result = false;

    $routeIndex = $this->Input->get( 'routeIndex' );
    $route = new Route();
    $routes = $route->routesFromConf;
    $route = $routes[ $routeIndex ];

    if ( $route and ! $route->inDatabase )
    {
      $route->resolveTo = $route->pageId;
      $result = $route->save();
    }

    if ( $this->isJson )
    {
      $this->Json->result = $result;
    }
  }



  /**
   * Load all routes in database
   */
  protected function post_load_all_routes()
  {
    $route = new Route();
    foreach ( $route->routesFromConf as $route )
    {
      if ( ! $route->inDatabase )
      {
        $route->resolveTo = $route->pageId;
        $route->save();
      }
    }
  }
}

