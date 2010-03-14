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
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Framework
 * @license    LGPL
 * @filesource
 */


/**
 * Class ModuleroutedNav
 *
 * Generate navigation from routes.
 * @copyright  Olivier El Mekki, 2009
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Controller
 */
class ModuleRoutedNav extends FrontendController
{
  protected $controller = 'framework_routed_nav';


  /**
   * Index
   */
  public function index()
  {
    global $objPage;

    $routes = array();
    $routesData = unserialize( $this->routes );

    foreach ( $routesData as $routeData )
    {
      $params = array();
      $paramsData = explode( ';', $routeData[ 'params' ] );

      foreach ( $paramsData as $paramData )
      {
        $param = explode( ':', $paramData );
        if ( count( $param ) == 2 )
        {
          $params[ $param[0] ] = $param[1];
        }
      }

      $path = Route::compose( $routeData[ 'routeName' ], $params );
      if ( strlen( $path ) )
      {
        $pageId   = Route::resolveUrl( $path );
        $page     = new FwPage( $pageId );
        $active   = ( $pageId == $objPage->id );
        if ( $pageId and $page->accessible )
        {
          $routes[] = array( 'path' => $path, 'name' => $routeData[ 'altName' ], 'active' => $active );
        }
      }
    }


    $this->Template->routes = $routes;
  }
}
