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
   * Arrays to manage validation
   */
  protected $validates_presence_of = array();
  protected $validates_uniqueness_of = array();
  protected $validates_format_of = array();
  protected $validates_numericality_of = array();
  protected $validates_min_length_of = array();
  protected $validates_max_length_of = array();
  protected $validates_associated = array();


  /**
   * Array to list attributes that can be send through json.
   * Empty by default for security reasons.
   */
  protected $jsonable = array();


  /**
   *  language array 
   */
  public $lang;


  /**
   *  errors 
   */
  protected $arrErrors = array();


  /**
   *  cache 
   */
  protected $arrCache = array();
  protected $uncachable = array();



  /**
   * Let this be public, instead
   * Let directly find a record if an id is specified
   * @arg id
   */
  public function __construct( $id = false )
  {
    $this->uncachable[] = 'data';
    parent::__construct();

    $language = ( $objPage and strlen( $objPage->language ) ) ? $objPage->language : $GLOBALS[ 'TL_LANGUAGE' ];
    if ( ! isset( $GLOBALS[ 'TL_LANG' ][ 'MSC' ] ) )
    {
      $this->loadLanguageFile( 'default', $language );
    }

    $this->lang = $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ get_class( $this ) ];

    if ( $id !== false )
    {
      $this->findBy( 'id', $id );
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
    if ( array_key_exists( $key, $this->arrData ) )
    {
      return $this->arrData[ $key ];
    }

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
    if ( array_key_exists( $key, $this->arrData ) )
    {
      $this->arrData[ $key ] = $value;
      return;
    }

    $firstLetter = substr( $key, 0, 1 );
    $rest = substr( $key, 1 );
    $setter = 'set' . strtoupper( $firstLetter ) . $rest;

    if ( method_exists( $this, $setter ) )
    {
      $this->arrCache[ $key ] = $this->$setter( $value );
      return true;
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
   * Save the timestamp
   */
  public function save( $blnForceInsert = null )
  {
    $this->tstamp = time();
    if ( $this->validate() )
    {
      if ($this->blnRecordExists && !$blnForceInsert)
      {
        $rows = $this->Database->prepare("UPDATE " . $this->strTable . " %s WHERE " . $this->strRefField . "=?")
                               ->set($this->arrData)
                               ->execute($this->varRefId)
                               ->affectedRows;

        if ( $rows > 0 )
        {
          return $rows;
        }

        else
        {
          return false;
        }
      }
      else
      {
        $id = $this->Database->prepare("INSERT INTO " . $this->strTable . " %s")
                             ->set($this->arrData)
                             ->execute()
                             ->insertId;

        if ( is_numeric( $id ) )
        {
          $this->blnRecordExists = true;
          $this->strRefField = 'id';
          $this->varRefId = $id;
          $this->id = $id;
        }

        else
        {
          return false;
        }
      }

      return $this->id;
    }

    else
    {
      return false;
    }
  }



  /**
   * Delete many to many associations and record
   */
  public function delete()
  {
    if ( count( $this->manyToMany ) )
    {
      $this->cleanupAssociation();
    }

    return parent::delete();
  }



  /**
   * Override this method to add some validation
   * You can add errors with setError() 
   *
   * @return boolean
   */
  public function validate()
  {
    foreach ( $this->validates_presence_of as $attr )
    {
      if ( ! strlen( $this->$attr ) )
      {
        $this->setError( $this->lang[ $attr . '_required' ], $attr );
      }
    }


    foreach ( $this->validates_uniqueness_of as $attr )
    {
      $class = get_class( $this );
      $model = new $class();
      $finder = 'find_all_by_' . $attr;
      $others = $model->$finder( $this->$attr );
      foreach ( $others as $other )
      {
        if ( $other->id != $this->id )
        {
          $this->setError( $this->lang[ $attr . '_uniqueness' ], $attr );
          break;
        }
      }
    }


    foreach ( $this->validates_format_of as $attr => $format )
    {
      if ( ! preg_match( $format, $this->$attr ) )
      {
        $this->setError( $this->lang[ $attr . '_format' ], $attr );
      }
    }


    foreach ( $this->validates_numericality_of as $attr )
    {
      if ( ! is_numeric( $this->$attr ) )
      {
        $this->setError( $this->lang[ $attr . '_numericality' ], $attr );
      }
    }


    foreach ( $this->validates_min_length_of as $attr => $min_length )
    {
      if ( strlen( $this->$attr ) < $min_length )
      {
        $this->setError( $this->lang[ $attr . '_min_length' ], $attr );
      }
    }


    foreach ( $this->validates_max_length_of as $attr => $max_length )
    {
      if ( strlen( $this->$attr ) < $max_length )
      {
        $this->setError( $this->lang[ $attr . '_max_length' ], $attr );
      }
    }


    foreach ( $this->validates_associated as $attr )
    {
      if ( ! $this->$attr() )
      {
        $this->setError( $this->lang[ $attr . '_associated' ], $attr );
      }
    }

    return ! $this->hasErrors();
  }



  /**
   * Add an error to the error array
   * @param string  the error message
   * @param string  the attribute on which the error occurs
   */
  public function setError( $msg, $attribute = 'main' )
  {
    $this->arrErrors[ $attribute ][] = $msg;
  }



  /**
   * Get the errors
   * @return array
   */
  public function getErrors()
  {
    return $this->arrErrors;
  }



  /**
   * Get the errors on a particular attribute
   * @arg string    the attribute
   * @return array  the errors
   */
  public function errorsOn( $attribute )
  {
    return $this->arrErrors[ $attribute ];
  }



  /**
   * Return true if there are errors
   * @param string    the attribute to test
   * @return boolean
   */
  public function hasErrors( $attribute = false )
  {
    if ( $attribute )
    {
      return ( count( $this->arrErrors[ $attribute ] ) ? true : false );
    }

    else
    {
      foreach ( $this->arrErrors as $attr )
      {
        if ( count( $attr ) ) return true;
      }

      return false;
    }
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
  public function getAll( $order = "id", $where = null, $limit = null )
  {
    $where_clause = '';
    $where_values = array();
    if ( is_array( $where ) )
    {
      $where_clause = $where[0];
      $where_values = array_slice( $where, 1 );

      if ( strpos( $where_clause, 'where' ) !== 0 )
      {
        $where_clause = 'where ' . $where_clause;
      }
    }

    if ( ! is_numeric( $limit ) )
    {
      $limit = null;
    }

    $record = $this->Database->prepare( "select * from " . $this->strTable . ' ' . $where_clause . " order by " . $order . ( $limit ? ' limit ' . $limit  : '' ) )
                             ->execute( $where_values );

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
    $all = ( is_array( $collection ) ? $collection : $this->all );
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
  public function first( $order = 'id', $where = null )
  {
    $where_clause = '';
    $where_values = array();
    if ( is_array( $where ) )
    {
      $where_clause = $where[0];
      $where_values = array_slice( $where, 1 );

      if ( strpos( $where_clause, 'where' ) !== 0 )
      {
        $where_clause = 'where ' . $where_clause;
      }
    }

    $record = $this->Database->prepare( 'select * from ' . $this->strTable . ' ' . $where_clause . ' order by ' . $order . ' limit 1' )
                             ->execute( $where_values );

    if ( $record->next() )
    {
      $this->setFound( $record->row() );
      return true;
    }

    return false;
  }



  /**
   * set the current object to the last record
   */
  public function last( $order = 'id', $where = null )
  {
    $where_clause = '';
    $where_values = array();
    if ( is_array( $where ) )
    {
      $where_clause = $where[0];
      $where_values = array_slice( $where, 1 );

      if ( strpos( $where_clause, 'where' ) !== 0 )
      {
        $where_clause = 'where ' . $where_clause;
      }
    }

    $record = $this->Database->prepare( 'select * from ' . $this->strTable . ' ' . $where_clause . ' order by ' . $order . ' desc limit 1' )
                             ->execute( $where_values );

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
      return $this->children( $stmt, $params );
    }

    if ( array_key_exists( $stmt, $this->hasOneThrough ) )
    {
      return $this->oneThrough( $stmt );
    }

    if ( array_key_exists( $stmt, $this->manyToMany ) )
    {
      return $this->manyToMany( $stmt, $params );
    }

    throw new Exception( 'undefined method:' . $stmt );
  }



  /**
   * Find owner - belongsTo relationship
   * @return obj
   */
  protected function owner( $class )
  {
    if ( $this->arrCache[ 'associations' ][ $class ] )
    {
      return $this->arrCache[ 'associations' ][ $class ];
    }

    $owner_field = strtolower( $class ) . '_id';
    if ( ! $this->hasField( $owner_field ) )
    {
      $owner_field = 'pid';
    }

    $owner = new $class( $this->$owner_field );
    $this->arrCache[ 'associations' ][ $class ] = $owner;
    return $owner;
  }



  /**
   * Find child - hasOne relationship
   * @return obj
   */
  protected function child( $class )
  {
    if ( $this->arrCache[ 'associations' ][ $class ] )
    {
      return $this->arrCache[ 'associations' ][ $class ];
    }

    $child = new $class();
    $find = "find_first_by_" . strtolower( get_class( $this ) ) . "_id";
    $child->$find( $this->id );
    $this->arrCache[ 'associations' ][ $class ] = $child;
    return $child;
  }



  /**
   * Find children - hasMany relationship
   * @arg   string      the related class name
   * @arg   mixed       the where clause array
   * @return mixed
   */
  protected function children( $class, $clauses )
  {
    if ( $this->arrCache[ 'associations' ][ $class ] and ! count( $clauses ) )
    {
      return $this->arrCache[ 'associations' ][ $class ];
    }

    $carbon = new $class();
    $children_field = strtolower( get_class( $this ) ) . "_id";

    if ( ! $carbon->hasField( $children_field ) )
    {
      $children_field = 'pid';
    }


    $where_clause = array( $children_field . ' = ?', $this->id );
    if ( count( $clauses ) )
    {
      $where_clause[0] .= ' and ' . $clauses[1][0];
      unset( $clauses[1][0] );
      $where_clause = array_merge( $where_clause, $clauses[1] );
      $children = $carbon->getAll( $clauses[0], $where_clause, $clauses[2] );
    }

    else
    {
      $children = $carbon->getAll( 'id', $where_clause );
      $this->arrCache[ 'associations' ][ $class ] = $children;
    }

    return $children;
  }



  /**
   * Find a related through an other
   * @return mixed
   */
  public function oneThrough( $class )
  {
    if ( $this->arrCache[ 'associations' ][ $class ] )
    {
      return $this->arrCache[ 'associations' ][ $class ];
    }

    $through = $this->hasOneThrough[ $class ];
    $step    = $this->$through();
    $one     = $step->$class();
    $this->arrCache[ 'associations' ][ $class ] = $one;
    return $one;
  }



  /**
   * Find a related - manyToMany relationship
   * @return mixed
   * TODO : check the got many flag ( no need to query db any longer if it is false )
   */
  public function manyToMany( $class, $clauses )
  {
    if ( $this->arrCache[ 'associations' ][ $class ] and ! count( $clauses ) )
    {
      return $this->arrCache[ 'associations' ][ $class ];
    }

    $relateds = array();

    $table = $this->manyToMany[ $class ];
    $record = $this->Database->prepare( sprintf( "select * from %s where %s = ?", $table, get_class( $this ) ) )
                             ->execute( $this->id );

    $carbon = new $class();
    $i = 0;

    while ( $record->next() )
    {
      $related = clone $carbon;
      $where_clause = array( 'id = ?', $record->$class );

      // order, where and/or limit clauses have been defined
      if ( count( $clauses ) )
      {
        // check if there is a where clause
        if ( count( $clauses[1] ) )
        {
          $tmp_clauses = $clauses;
          $where_clause[0] .= ' and ' . $tmp_clauses[1][0];
          unset( $tmp_clauses[1][0] );
          $where_clause = array_merge( $where_clause, $tmp_clauses[1] );
        }

        $related->first( $clauses[0], $where_clause );

        // check if limit is defined and reached
        if ( is_numeric( $clauses[2] ) )
        {
          if ( $i == $clauses[2] )
          {
            break;
          }

          elseif ( is_numeric( $related->id ) )
          {
            $i++;
            $relateds[] = $related;
          }
        }

        // there is not limit, checks if record matches
        elseif ( is_numeric( $related->id ) )
        {
          $relateds[] = $related;
        }
      }

      // simply get the related record
      else
      {
        $related->first( 'id', $where_clause );
        if ( is_numeric( $related->id ) )
        {
          $relateds[] = $related;
        }
      }

    }

    if ( ! count( $clauses ) )
    {
      $this->arrCache[ 'associations' ][ $class ] = $relateds;
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
        $delim = $delims[$i-1][0] ;
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



  /**
   * Flush the cash
   */
  public function flushCache()
  {
    $this->arrCache = array();
  }



  /**
   * Resize an image by width
   * @param int     width
   * @param string  the path of the file to resize
   * return string  path to the renderer
   */
  public function resizeImageByWidth( $width, $path = null )
  {
    if ( ! $path )
    {
      $path = $this->imageSRC;
    }

    return $GLOBALS[ 'TL_CONFIG' ][ 'websitePath' ] . '/system/modules/framework/ImageRenderer.php?action=resizer&type=width&file=' . urlencode( $path ) . '&value=' . $width;
  }



  /**
   * Resize an image by height
   * @param int     height
   * @param string  the path of the file to resize
   * return string  path to the renderer
   */
  public function resizeImageByHeight( $height, $path = null )
  {
    if ( ! $path )
    {
      $path = $this->imageSRC;
    }

    return $GLOBALS[ 'TL_CONFIG' ][ 'websitePath' ] . '/system/modules/framework/ImageRenderer.php?action=resizer&type=height&file=' . urlencode( $path ) . '&value=' . $height;
  }



  public function cleanupAssociation( $dca = null )
  {
    if ( is_object( $dca ) )
    {
      $this->findBy( 'id', $dca->id );
    }

    if ( is_numeric( $this->id ) )
    {
      foreach ( $this->manyToMany as $class => $table )
      {
        $this->Database->prepare( 'delete from ' . $table . ' where ' . get_class( $this ) . ' = ?' )
                       ->execute( $this->id );
      }
    }
  }
}

