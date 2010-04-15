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
 * Class Json 
 * To use json, you must add at the bottom of the root .htaccess :
 * RewriteRule .*\.json$ index.php [L]
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki 
 * @package    Model
 */
class Json extends System
{
  protected $arrData;
  protected static $objInstance;

  protected function __construct(){}
  final private function __clone(){}



  /**
   * Setter
   * @param string
   * @param string
   */
  public function __set( $key, $value )
  {
    $this->arrData[ $key ] = $value;
  }



  /**
   * Getter
   * @param string
   * @param string
   */
  public function __get( $key )
  {
    return $this->arrData[ $key ];
  }



  /**
   * Instantiation of the singleton
   * @return obj
   */
  public static function getInstance()
  {
    if (!is_object(self::$objInstance))
    {
      self::$objInstance = new Json();
    }

    return self::$objInstance;
  }



  /**
   * return the data of the json object as array
   * @return array
   */
  public function getData()
  {
    return $this->arrData;
  }



  /**
   * Set the data
   * If data is json encoded, decode it
   * @param mixed
   * @return boolean
   */
  public function setData( $data )
  {
    if ( is_string( $data ) )
    {
      $data = json_decode( $data );
    }

    if ( is_array( $data ) )
    {
      $this->arrData = $data;
      return true;
    }

    return false;
  }



  /**
   * Merge data with current data
   * If data is json encoded, decode it
   * @param mixed
   * @return boolean
   */
  public function mergeData( $data )
  {
    if ( is_string( $data ) )
    {
      $data = json_decode( $data );
    }

    if ( is_array( $data ) )
    {
      $this->arrData = array_merge( $this->arrData, $data );
      return true;
    }

    return false;
  }



  /**
   * Encode the data as json string
   * @return string
   */
  public function encode()
  {
    return json_encode( $this->arrData );
  }



  /**
   * Decode a json string
   * @param string
   * @return array
   */
  public static function decode( $data )
  {
    return json_decode( $data, true );
  }
}

