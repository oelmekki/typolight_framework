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
class HardRoutesList extends BackendController
{
  /**
   * Template
   * @var string
   */
  protected $controller = 'framework_routes';



  /**
   * Generate the widget and return it as string
   * @return string
   */
  protected function action_index()
  {
    $GLOBALS[ 'TL_JAVASCRIPT' ][] = 'system/modules/framework/js/Toggable.js';
    $GLOBALS[ 'TL_CSS' ][] = 'system/modules/framework/css/Toggable.css';
    $GLOBALS[ 'TL_JAVASCRIPT' ][] = 'system/modules/framework/js/backend/HardRoutesList.js';
    $GLOBALS[ 'TL_CSS' ][] = 'system/modules/framework/css/backend/HardRoutesList.css';
    $route = new Route();
    $routes = $route->routesFromConf;
    $this->Template->routes   = $routes;
    $this->Template->messages = ( ( is_array( $GLOBALS[ 'TL_MSG' ] ) and count( $GLOBALS[ 'TL_MSG' ] ) ) ? $GLOBALS[ 'TL_MSG' ] : array() );
  }



  /**
   * Load a route in database
   */
  protected function action_load_route()
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

    $this->passMessage( ( $result ? $this->lang[ 'route_loaded' ] : $this->lang[ 'route_not_loaded' ] ) );
    $this->redirect( 'typolight/main.php?do=hardRoutes' );
  }



  /**
   * Load all routes in database
   */
  protected function action_load_all_routes()
  {
    $route    = new Route();
    $hasError = false;

    foreach ( $route->routesFromConf as $route )
    {
      if ( ! $route->inDatabase )
      {
        $route->resolveTo = $route->pageId;
        if ( ! $route->save() )
        {
          $hasError = true;
        }
      }
    }

    $this->passMessage( ( $hasError ? $this->lang[ 'routes_not_loaded' ] : $this->lang[ 'routes_loaded' ] ) );
    $this->redirect( 'typolight/main.php?do=hardRoutes' );
  }
}

