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
 * Class EModel
 *
 * Enhence the basic Model class
 * @copyright  Olivier El Mekki 2006
 * @author     Olivier El Mekki 
 * @package    Model
 */
abstract class EModel extends Model
{

  /**
   * Constants used by the dynamic finder 
   */
  const FIND_FIRST  = 'find_first_by_';
  const FIND_ALL    = 'find_all_by_';
  const FIND_DELIM  = '(_and_|_or_|_and_not_)';
  const FIND_ORDER  = '_order_by_';

  /**
   * Arrays to manage associations
   */
  protected $belongsTo = array();
  protected $hasOne = array();
  protected $hasMany = array();
  protected $hasOneThrough = array();
  protected $manyToMany = array();

  /**
   * Array to list attributes that can be send through json.
   * Empty by default for security reasons.
   */
  protected $jsonable = array();



  /**
   * Let this be public, instead
   * Let directly find a record if an id is specified
   * @arg id
   */
  public function __construct( $id = false )
  {
    parent::__construct();
    if ( $id !== false )
    {
      $this->findBy( 'id', $id );
    }
  }


  /**
   * Save the timestamp
   */
  public function save()
  {
    $this->tstamp = time();
    return parent::save();
  }


  /**
   * Update the attributes and save the record
   */
  public function update_attributes( $attributes )
  {
    foreach ( $attributes as $attr => $value )
    {
      $this->$attr = $value;
    }

    return $this->save();
  }



  /** 
   * Create from attributes
   */
  public function create( $attributes )
  {
    $this->setData( $attributes );
    return $this->save();
  }



  /**
   * Find all records
   * @param string
   * @return array
   */
  public function findAll( $order = "id" )
  {
    $record = $this->Database->prepare( "select * from " . $this->strTable . " order by " . $order )
                             ->execute();

    $all = array();

    while ( $record->next() )
    {
      $classname = get_class( $this );
      $one = new $classname();
      $one->setFound( $record->row() );
      $all[] = $one;
    }

    return $all;
  }



  /**
   * Get all id's, so we can use in_array
   * @param mixed     collection : an array of models to get the id from.
   * @return mixed    the list of id's
   */
  public function ids( $collection = null )
  {
    $all = ( is_array( $collection ) ? $collection : $this->findAll() );
    $ids = array();

    foreach ( $all as $one )
    {
      $ids[] = $one->id;
    }

    return $ids;
  }



  /**
   * set the current object to the first record
   */
  public function first()
  {
    $record = $this->Database->prepare( 'select * from ' . $this->strTable . ' order by id limit 1' )
                             ->execute();

    if ( $record->next() )
    {
      $this->setFound( $record->row() );
      return true;
    }

    return false;
  }



  /**
   * act as setData but also set protected
   * variables as with findBy
   * @param mixed
   * @return mixed
   */
  public function setFound( $varData )
  {
    if ( is_object( $varData ) )
    {
      $varData = get_object_vars( $varData );
    }

    if ( ! is_array( $varData ) )
    {
      throw new Exception( 'Array required to set data' );
    }

    $this->blnRecordExists = true;
    $this->strRefField = 'id';
    $this->varRefId = $varData[ 'id' ];
    $this->arrData = $varData;
  }



  /**
   * dynamic finders and associations
   */
  public function __call( $stmt, $params )
  {
    $findFirst  = strpos( $stmt, self::FIND_FIRST );
    $findAll    = strpos( $stmt, self::FIND_ALL );

    /* is this a finder? */
    if ( is_numeric( $findFirst ) or is_numeric( $findAll ) )
    {
      return $this->findDynamic( $stmt, $params );
    }

    /* is this an association? */
    if ( in_array( $stmt, $this->belongsTo ) )
    {
      return $this->owner( $stmt );
    }


    if ( in_array( $stmt, $this->hasOne ) )
    {
      return $this->child( $stmt );
    }


    if ( in_array( $stmt, $this->hasMany ) )
    {
      return $this->children( $stmt );
    }

    if ( array_key_exists( $stmt, $this->hasOneThrough ) )
    {
      return $this->oneThrough( $stmt );
    }

    if ( array_key_exists( $stmt, $this->manyToMany ) )
    {
      return $this->manyToMany( $stmt );
    }

    throw new Exception( 'undefined method:' . $stmt );
  }



  /**
   * Find owner - belongsTo relationship
   * @return obj
   */
  protected function owner( $class )
  {
    $owner = new $class();
    $owner_field = strtolower( $class ) . '_id';
    if ( ! $this->hasField( $owner_field ) )
    {
      $owner_field = 'pid';
    }

    return new $class( $this->$owner_field );
  }



  /**
   * Find child - hasOne relationship
   * @return obj
   */
  protected function child( $class )
  {
    $child = new $class();
    $find = "find_first_by_" . strtolower( get_class( $this ) ) . "_id";
    $child->$find( $this->id );
    return $child;
  }



  /**
   * Find children - hasMany relationship
   * @return mixed
   */
  protected function children( $class )
  {
    $children = new $class();
    $children_field = strtolower( get_class( $this ) ) . "_id";

    if ( ! $children->hasField( $children_field ) )
    {
      $children_field = 'pid';
    }

    $find = "find_all_by_" . $children_field;
    $children = $children->$find( $this->id );
    return $children;
  }



  /**
   * Find a related through an other
   * @return mixed
   */
  public function oneThrough( $search )
  {
    $through = $this->hasOneThrough[ $search ];
    $step    = $this->$through();
    return $step->$search();
  }



  /**
   * Find a related - manyToMany relationship
   * @return mixed
   * TODO : check the got many flag ( no need to query db any longer if it is false )
   */
  public function manyToMany( $search )
  {
    $relateds = array();

    $table = $this->manyToMany[ $search ];
    $record = $this->Database->prepare( sprintf( "select * from %s where %s = ?", $table, get_class( $this ) ) )
                             ->execute( $this->id );

    while ( $record->next() )
    {
      $related = new $search();
      if ( $related->findBy( 'id', $record->$search ) )
      {
        $relateds[] = $related;
      }
    }

    return $relateds;
  }



  /**
   * Set a related - manyToMany relationship
   * @param string
   * @param mixed
   * @return mixed
   * TODO : set the got many flag
   */
  public function setManyToMany( $associated, $ids )
  {
    if ( array_key_exists( $associated, $this->manyToMany ) )
    {
      $table = $this->manyToMany[ $associated ];
      $this->Database->prepare( sprintf( "delete from %s where %s = ?", $table, get_class( $this ) ) )
                     ->execute( $this->id );

      foreach ( $ids as $id )
      {
        $this->Database->prepare( sprintf( "insert into %s( %s, %s ) values( ?, ? )", $table, get_class( $this ), $associated ) )
                       ->execute( $this->id, (int) $id );
      }
    }
  }



  /**
   * Add a related - manyToMany relationship
   * @param string
   * @param mixed
   * @return mixed
   * TODO : set the got many flag
   */
  public function addManyToMany( $associated, $id )
  {
    if ( array_key_exists( $associated, $this->manyToMany ) )
    {
      $table = $this->manyToMany[ $associated ];
      $record = $this->Database->prepare( sprintf( "select * from %s where %s = ? and %s = ?", $table, get_class( $this ), $associated ) )
                               ->execute( $this->id, $id );

      if ( ! $record->next() and is_int( $id ) )
      {
        $this->Database->prepare( sprintf( "insert into %s( %s, %s ) values( ?, ? )", $table, get_class( $this ), $associated ) )
                       ->execute( $this->id, $id );
      }
    }
  }



  /**
   * dynamic finder
   */
  protected function findDynamic( $stmt, $params )
  {
    $findFirst  = strpos( $stmt, self::FIND_FIRST ) ;
    $findAll    = strpos( $stmt, self::FIND_ALL ) ;
    $orderBy    = strpos( $stmt, self::FIND_ORDER ) ;

    /* isolates where clause and order clause */
    $start = ( $findFirst !== false ? strlen( self::FIND_FIRST ) : strlen( self::FIND_ALL ) ) ;
    $clauses_str = substr( $stmt, $start ) ;

    if ( $orderBy !== false )
    {
      $where_clause_str = substr( $clauses_str, 0, $orderBy-$start ) ;
      $order_clause_str = str_replace( '_', ' ', substr( $clauses_str, $orderBy-$start ) ) ;
    }
    else
    {
      $where_clause_str = $clauses_str ;
      $order_clause_str = 'order by id' ;
    }

    /* parses the field list */
    $fields = preg_split( self::FIND_DELIM, $where_clause_str ) ;
    $delims = array() ;
    preg_match_all( self::FIND_DELIM, $where_clause_str, &$delims ) ;

    if ( count( $fields ) != count( $params ) )
    {
      error_log( sprintf( "The number of fields and of params does not match: %s," . $params, $stmt ) ) ;
      return false ;
    }

    /* build the query */

    $query  = sprintf( "select * from %s where ", $this->strTable ) ;

    foreach( $fields as $i => $field )
    {
      if ( $i == 0 ) // no delimiter for now
      {
        $query .= sprintf( "%s = ? ", $field ) ;
      }

      else
      {
        $delim = $delims[$i-1] ;
        $delim = str_replace( '_', ' ', $delim ) ;
        $query .= sprintf( "%s %s = ? ", $delim, $field ) ;
      }
    }

    $query .= $order_clause_str ;

    if ( $findFirst !== false )
    {
      $query .= " limit 1" ;
    }

    /* build the objects */

    $record = $this->Database->prepare( $query )
                             ->execute( $params ) ;

    if ( $findFirst !== false )
    {
      if ( $record->next() )
      {
        $this->setFound( $record->row() ) ;
        return true;
      }

      return false ;
    }

    else
    {
      $all = array() ;

      while ( $record->next() )
      {
        $classname = get_class( $this ) ;
        $one = new $classname() ;
        $one->setFound( $record->row() );
        $all[] = $one;
      }

      return $all ;
    }
  }



  /**
   * Determine is the model has the given field
   * @param string
   * @return boolean
   */
  public function hasField( $field )
  {
    return $this->Database->fieldExists( $field, $this->strTable );
  }



  /**
   * Convert model to array for json
   * If recursive is true, associated EModel are converted recursively.
   * Non-EModel object attributes are ignored.
   * @return mixed
   */
  public function toJson( $recursive = false )
  {
    $json = array();
    foreach ( $this->jsonable as $key )
    {
      $value = $this->$key;

      if ( $recursive )
      {
        if ( $recursive && ( in_array( $key, $this->belongsTo ) || in_array( $key, $this->hasOne ) ) )
        {
          $json[ $key ] = $this->$key()->toJson();
          continue;
        }

        foreach ( $this->hasOneThrough as $through )
        {
          if ( $key == $through[0] )
          {
            $json[ $key ] = $this->key()->toJson();
            $continue = true;
            break;
          }
        }

        if ( $continue ) continue;
      }

      if ( @unserialize( $value ) !== false )
      {
        $json[ $key ] = unserialize( $value );
        continue;
      }

      $json[ $key ] = $value;
    }

    if ( $recursive )
    {
      foreach ( $this->hasMany as $association )
      {
        $models = $this->$association();
        $json[ $association ] = array();

        foreach ( $models as $model )
        {
          $json[ $association ][] = $model->toJson();
        }
      }

      foreach ( $this->manyToMany as $association )
      {
        $models = $this->$association();
        $json[ $association ] = array();

        foreach ( $models as $model )
        {
          $json[ $association ][] = $model->toJson();
        }
      }
    }

    return $json;
  }
}

?>
