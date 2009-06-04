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
 * Class Route
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki 
 * @package    Model
 */
class Route extends EModel
{
  protected $strTable = "tl_framework_routes" ;

  /**
   * basic stringification
   */
  public function __toString()
  {
    return sprintf( "%s - %s", $this->name, $this->route ) ;
  }



  /**
   * Test if the given fragment of url match the route
   * if it does, return the reordered fragments
   * @arg mixed
   * @return mixed
   */
  public function match( $arrFragments )
  {
    $lastIndex = count( $arrFragments ) - 1;
    if ( substr( $arrFragments[ $lastIndex ], -5, 5 ) == '.json' )
    {
      $length = strlen( $arrFragments[ $lastIndex ] );
      $arrFragments[ $lastIndex ] = substr( $arrFragments[ $lastIndex ], 0, $length - 5 );
    }

    $arrRouteFragments = explode( '/', $this->route ) ;
    $arrOrderedFragments = array() ;

    /* route only match if it has the good count of fragments */
    if ( count( $arrFragments ) != count( $arrRouteFragments ) )
    {
      return false ;
    }

    /* no need to process any longer if the method don't match */
    if ( ( strlen( $this->POSTroute ) and ! count( $_POST ) ) or ( ! strlen( $this->POSTroute ) and count( $_POST ) ) )
    {
      return false ;
    }

    /* analyze fragments and fill params */
    for ( $i=0; $i<count($arrFragments); $i++ )
    {
      if ( strpos( $arrRouteFragments[$i], ':' ) === 0 )
      {
        $arrOrderedFragments = array_merge( $arrOrderedFragments, array( substr( $arrRouteFragments[$i], 1 ), $arrFragments[$i] ) ) ;
        continue ;
      }

      if ( $arrRouteFragments[$i] != $arrFragments[$i] )
      {
        return false ;
      }
    }

    /* add static params */
    $staticParams = unserialize( $this->staticParams );
    foreach ( $staticParams as $staticParam => $value )
    {
      $arrOrderedFragments = array_merge( $arrOrderedFragments, array( $staticParam, $value ) ) ;
    }


    /* route match, gotta find the page alias */
    if ( is_numeric( $this->resolveTo ) )
    {
      $record = $this->Database->prepare( "select alias from tl_page where id = ?" )
                               ->execute( $this->resolveTo ) ;

      if ( ! $record->next() )
      {
        error_log( sprintf("Error while parsing route : route %i match but page %i doesn't exists", $this->id, $this->resolveTo ) ) ;
        return false ;
      }

      $alias = $record->alias;
    }

    else
    {
      $alias = $this->resolveTo;
    }



    /* add statics params to the fragments */
    if ( $this->addStatic )
    {
      $staticParams = unserialize( $this->staticParams ) ;
      foreach ( $staticParams as $paramPair )
      {
        if ( strlen( $paramPair[ 'param' ] ) and strlen( $paramPair[ 'value' ] ) )
        {
          $arrOrderedFragments = array_merge( $arrOrderedFragments, array( $paramPair[ 'param' ], $paramPair[ 'value' ] ) ) ;
        }
      }
    }

    return array_merge( array( $alias ), $arrOrderedFragments ) ;
  }



  /**
   * find a clean url from a route name
   * @arg string
   * @arg mixed
   * @return string
   */
  public static function compose( $name, $params=array(), $format = 'html' )
  {
    $route = new Route() ;
    if ( ! $route->findBy( 'name', $name ) )
    {
      return '' ;
    }

    $path = ( $GLOBALS['TL_CONFIG']['rewriteURL'] ? '': 'index.php/') . $route->route ;

    foreach ( $params as $param => $value )
    {
      if ( strpos( $path, ':' . $param ) !== false )
      {
        $path = str_replace( ':' . $param, $value, $path ) ;
      }
    }

    switch ( $format )
    {
    case 'html':
      $suffix = $GLOBALS[ 'TL_CONFIG' ][ 'urlSuffix' ];
      break;

    case 'json':
      $suffix = '.json';
      break;
    }

    return $path . $suffix ;
  }



  /**
   * find a clean url from a route name, internationalized version.
   * Routes should be name as : language_code + '_' + name
   * ex: fr_home
   *
   * @arg string
   * @arg mixed
   * @return string
   */
  public static function composeI18n( $name, $params=array(), $format = 'html' )
  {
    global $objPage;
    $name = ( strlen( $objPage->language ) ? $objPage->language : $GLOBALS[ 'TL_LANGUAGE' ] ) . '_' . $name;
    return Route::compose( $name, $params, $format );
  }



  /**
   * HOOK for getPageIdFromUrl :
   * parse routes
   * @arg array
   */
  public function resolve( $arrFragments )
  {
    $routes = $this->getAll( "sorting" ) ;
    $routes = array_merge( $routes, $this->routesFromConf );

    foreach ( $routes as $route )
    {
      if ( $fragments = $route->match( $arrFragments ) )
      {
        return $fragments ;
      }
    }

    return $arrFragments ;
  }



  /**
   * HOOK for replaceInsertTags :
   * resolve route
   * Insert tag should be formatted as : {{Route:name:param1=value1:param2=value2}}
   * @arg array
   */
  public function insertTags( $strTag )
  {
    if ( strpos( $strTag, 'Route:' ) === 0 )
    {
      $parts = explode( ':', $strTag );
      array_shift( $parts );
      $routeName = array_shift( $parts );
      
      $params = array();
      foreach ( $parts as $part )
      {
        $param = explode( '=', $part );
        if ( count( $param ) == 2 )
        {
          $params[ $param[0] ] = $param[1];
        }
      }

      return Route::compose( $routeName, $params );
    }

    return false;
  }



  /**
   * Let findBy search in the conf file as well
   * @return mixed
   */
  public function findBy( $strRefField, $varRefId )
  {
    if ( parent::findBy( $strRefField, $varRefId ) )
    {
      return true;
    }

    $confRoutes = $this->routesFromConf;
    foreach ( $confRoutes as $route )
    {
      if ( $route->$strRefField == $varRefId )
      {
        $this->setData( $route->getData() );
        return true;
      }
    }

    return false;
  }



  /**
   * Let getAll search in the conf file as well
   * @return mixed
   */
  public function getAll( $order = 'id' )
  {
    $allDb = parent::getAll( $order );

    $confRoutes = $this->routesFromConf;
    return array_merge( $allDb, $confRoutes );
  }



  /**
   * Get routes from the routes config file
   * @return mixed
   */
  public function getRoutesFromConf()
  {
    $routes = array();

    if ( is_array( $GLOBALS[ 'TL_ROUTES' ] ) )
    {
      foreach ( $GLOBALS[ 'TL_ROUTES' ] as $name => $routeDef )
      {
        $route = new Route();
        $route->name = $name;
        $route->route = $routeDef[ 'route' ];
        $route->resolveTo = $routeDef[ 'resolveTo' ];
        $route->POSTroute = $routeDef[ 'POSTroute' ];

        if ( $routeDef[ 'staticParams' ] )
        {
          $route->addStatic = true;
          $route->staticParams = serialize( $routeDef[ 'staticParams' ] );
        }

        $routes[] = $route;
      }
    }

    return $routes;
  }



  /**
   * Return true if a route with the same name exists in the database
   * @return boolean
   */
  public function getInDatabase()
  {
    $record = $this->Database->prepare( 'select * from tl_framework_routes where name = ?' )
                             ->execute( $this->name );

    if ( $record->next() )
    {
      $this->found = $record->row();
      return true;
    }

    return false;
  }



  /**
   * Return the unserialized static params
   */
  public function getParams()
  {
    return unserialize( $this->staticParams );
  }



  /**
   * get the resolveTo page id if it is an alias
   */
  public function getPageId()
  {
    if ( is_numeric( $this->resolveTo ) )
    {
      return $this->resolveTo;
    }

    $record = $this->Database->prepare( 'select id from tl_page where alias = ?' )
                             ->execute( $this->resolveTo );

    if ( $record->next() )
    {
      return $record->id;
    }

    return 0;
  }
}

