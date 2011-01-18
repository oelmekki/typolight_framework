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
 *
 * For the documentation, see : http://wiki.github.com/oelmekki/typolight_framework/route
 */
class Route extends EModel
{
  protected $strTable = "tl_framework_routes" ;



  /**
   * Find a clean url from a route name
   *
   * This method is a mean to compute an url from a route.
   * You can pass in the params array the parameter the route
   * expect. For example, if you have a books_show route that is
   * expressed as : 'books/:book_id/show', you can compose it with :
   * $href = Route::compose( 'books_show', array( 'book_id' => $book->id ) );
   *
   * If your application handles multiple formats ( eg: html, json, xml ),
   * you can pass the required format as third parameter.
   * 
   * @param string the route name
   * @param mixed optional parameters for the route
   * @param string the format required
   * @return string the matching url
   */
  public static function compose( $name, $params=array(), $format = 'html' )
  {
    if ( $format == 'html' )
    {
      $suffix = $GLOBALS[ 'TL_CONFIG' ][ 'urlSuffix' ];
    }

    else
    {
      $suffix = '.' . $format;
    }

    $route = new Route() ;
    if ( $route->findBy( 'name', $name ) )
    {
      $path        = ( $GLOBALS['TL_CONFIG']['rewriteURL'] ? '': 'index.php/') . $route->route ;
      $additionals = array();

      foreach ( $params as $param => $value )
      {
        if ( strpos( $path, ':' . $param ) !== false )
        {
          $path = str_replace( ':' . $param, $value, $path ) ;
        }

        else
        {
          $additionals[ $param ] = $value;
        }
      }

      if ( count( $additionals ) )
      {
        $addStr = '?';
        foreach ( $additionals as $param => $value )
        {
          if ( strlen( $value ) )
          {
            $addStr .= sprintf( '%s=%s&', $param, $value );
          }
        }

        $addStr = substr( $addStr, 0, strlen( $addStr ) - 1 );
      }

      else
      {
        $addStr = '';
      }

      return $path . $suffix . $addStr;
    }

    else
    {
      $paramStr = '';

      if ( count( $params ) )
      {
        $paramStr = '?';
        foreach ( $params as $param => $value )
        {
          $paramStr .= $param . '=' . $value . ';';
        }
      }

      $page = new FwPage();

      if ( $page->findBy( 'alias', $name ) and $page->accessible )
      {
        $path = ( $GLOBALS['TL_CONFIG']['rewriteURL'] ? '': 'index.php/') . $name;
        return $path . $suffix . $paramStr;
      }

      else
      {
        $env = Environment::getInstance();
        $path = $env->url . TL_PATH . '/' . $paramStr;
        return $path;
      }
    }
  }



  /**
   * Just like Route::compose, but let use various routes depending on the current language.

   * Routes should be name as : language_code + '_' + name
   * ex: fr_home
   *
   * You can then do :
   * $href = Route::composeI18n( 'home' );
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
   * Resolve a url to a route.
   *
   * Give it an url, and it will try to guess to which page it resolves
   * ( return the id of the page ).
   * 
   * @arg string        the url to resolve
   * @return integer    the page id
   */
  public static function resolveUrl( $url )
  {
    // isolate the relative path
    $regex = sprintf( '/^(https?:\/\/%s%s)?\/?(.*?)%s\??/', 
        preg_quote( $_SERVER[ 'SERVER_NAME' ], '/' ), 
        preg_quote( $GLOBALS[ 'TL_CONFIG' ][ 'websitePath' ], '/' ),
        preg_quote( $GLOBALS[ 'TL_CONFIG' ][ 'urlSuffix' ], '/' )
    );

    $matches = array();
    if ( preg_match( $regex, $url, $matches ) )
    {
      $url = $matches[2];
    }

    $arrFragments = explode( '/', $url );
    $method = ( count( $_POST ) ? 'POST' : 'GET' );

    if ( $GLOBALS[ 'TL_CONFIG' ][ 'cacheRoutes' ] )
    {
      $database = Database::getInstance();
      $record = $database->prepare( 'select * from tl_framework_cached_routes where route = ? and method = ? limit 1' )
                         ->execute( $url, $method );

      if ( $record->next() )
      {
        return $record->pageId;
      }
    }

    $route = new Route();
    $routes = $route->getAll( "sorting" ) ;
    $routes = array_merge( $routes, $route->routesFromConf );

    foreach ( $routes as $route )
    {
      if ( $fragments = $route->match( $arrFragments ) )
      {
        if ( $GLOBALS[ 'TL_CONFIG' ][ 'cacheRoutes' ] )
        {
          $database->prepare( 'insert into tl_framework_cached_routes( tstamp, route, method, fragments, pageId ) values( ?, ?, ?, ?, ? )' )
                   ->execute( time(), $url, $method, serialize( $fragments ), $route->pageId );
        }

        return $route->pageId;
      }
    }

    return false;
  }



  /*
   * basic stringification
   */
  public function __toString()
  {
    return sprintf( "%s - %s", $this->name, $this->route ) ;
  }



  /*
   * Test if the given fragment of url match the route
   * if it does, return the reordered fragments
   * @arg mixed
   * @return mixed
   */
  public function match( $arrFragments )
  {
    $lastIndex = count( $arrFragments ) - 1;
    foreach ( $GLOBALS[ 'DOC_SUP_FORMATS' ] as $sup )
    {
      $sup_ln = strlen( $sup );
      if ( in_array( substr( $arrFragments[ $lastIndex ], - ( $sup_ln ), $sup_ln ), $GLOBALS[ 'DOC_SUP_FORMATS' ] ) )
      {
        $length = strlen( $arrFragments[ $lastIndex ] );
        $arrFragments[ $lastIndex ] = substr( $arrFragments[ $lastIndex ], 0, $length - ( $sup_ln + 1 ) );
      }
    }


    $arrRouteFragments = explode( '/', $this->route ) ;
    $arrOrderedFragments = array() ;

    /* route only match if it has the good count of fragments */
    if ( count( $arrFragments ) != count( $arrRouteFragments ) )
    {
      return false ;
    }

    /* no need to process any longer if the method don't match */
    if ( ( $this->method == 'POST' and ! count( $_POST ) ) or ( $this->method == 'GET' and count( $_POST ) ) )
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
    if ( is_array( $staticParams ) )
    {
      foreach ( $staticParams as $staticParam => $value )
      {
        $arrOrderedFragments = array_merge( $arrOrderedFragments, array( $staticParam, $value ) ) ;
      }
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

		// route is relative to the current domain
		elseif ( strpos( $this->resolveTo, 'domain:' ) === 0 )
		{
			$search = str_replace( 'domain:', '', $this->resolveTo );
			$root = new FwPage();

			// retrieve root
			$host = str_replace( 'www.', '', $this->Environment->httpHost );
			$record = $this->Database->prepare( 'select * from tl_page where dns like ?' )
															 ->execute( '%' . $host . '%' );

			if ( $record->next() )
			{
				$root->found = $record->row();
			}

			else
			{
				if ( ! $root->findBy( 'fallback', true ) )
				{
					$root->first( 'id', array( 'type = "root"' ) );
				}
			}

			// search by title
			if ( strpos( $search, 'title:' ) === 0 )
			{
				$search = str_replace( 'title:', '', $search );
				$record = $this->Database->prepare( 'select * from tl_page where title like ?' )
																 ->execute( '%' . $search . '%' );
			}

			// search by alias
			else 
			{
				$record = $this->Database->prepare( 'select * from tl_page where alias like ?' )
																 ->execute( '%' . $search . '%' );
			}

			$page = new FwPage();

			while ( $record->next() )
			{
				$page->found = $record->row();
				if ( $page->getRoot()->id == $root->id )
				{
					$alias = $page->alias;
					break;
				}
			}

		}

		// resolveTo is the alias of the target page
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



  /*
   * HOOK for getPageIdFromUrl :
   * parse routes
   * @arg array
   */
  public function resolve( $arrFragments )
  {
    $definition = implode( '/', $arrFragments );
    $method = ( count( $_POST ) ? 'POST' : 'GET' );

    if ( $GLOBALS[ 'TL_CONFIG' ][ 'cacheRoutes' ] )
    {

      $record = $this->Database->prepare( 'select * from tl_framework_cached_routes where route = ? and method = ? limit 1' )
                               ->execute( $definition, $method );

      if ( $record->next() )
      {
        return unserialize( $record->fragments );
      }
    }

    $routes = $this->getAll( "sorting" ) ;
    $routes = array_merge( $routes, $this->routesFromConf );

    foreach ( $routes as $route )
    {
      if ( $fragments = $route->match( $arrFragments ) )
      {
        if ( $GLOBALS[ 'TL_CONFIG' ][ 'cacheRoutes' ] )
        {
          $this->Database->prepare( 'insert into tl_framework_cached_routes( tstamp, route, method, fragments, pageId ) values( ?, ?, ?, ?, ? )' )
                         ->execute( time(), $definition, $method, serialize( $fragments ), $route->pageId );
        }

        return $fragments ;
      }
    }

    return $arrFragments ;
  }



  /*
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



  /*
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



  /*
   * Let getAll search in the conf file as well
   * @return mixed
   */
  public function getAll( $order = 'id', $where = null, $limit = null )
  {
    $allDb = parent::getAll( $order );

    $confRoutes = $this->routesFromConf;
    return array_merge( $allDb, $confRoutes );
  }



  /*
   * Get routes from the routes config file
   * @return mixed
   */
  public function getRoutesFromConf()
  {
    $routes = array();

    if ( is_array( $GLOBALS[ 'TL_ROUTES' ] ) )
    {
      $carbon = new Route();
      foreach ( $GLOBALS[ 'TL_ROUTES' ] as $name => $routeDef )
      {
        $route = clone $carbon;
        $data = array( 
          'name'        => $name,  
          'route'       => $routeDef[ 'route' ],
          'resolveTo'   => $routeDef[ 'resolveTo' ],
          'method'      => $routeDef[ 'method' ],
        );

        if ( $routeDef[ 'staticParams' ] )
        {
          $data[ 'addStatic' ] = true;
          $data[ 'staticParams' ] = serialize( $routeDef[ 'staticParams' ] );
        }

        $route->data = $data;
        $routes[] = $route;
      }
    }

    return $routes;
  }



  /*
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



  /*
   * Return the unserialized static params
   */
  public function getParams()
  {
    return unserialize( $this->staticParams );
  }



  /*
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



  /*
   * Purge the route cache of more than one week old records
   */
  public function purgeCache()
  {
    $oneWeekAgo = time() - 604800;
    $this->Database->prepare( 'delete from tl_framework_cached_routes where tstamp < ?' )
                   ->execute( $oneWeekAgo );
  }
}

