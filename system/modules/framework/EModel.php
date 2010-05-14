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
 * @package    Framework
 */
abstract class EModel extends Model
{

  /**
   * @var array Association "belongs to".
   *
   * Put in this array the parent classes of your class.
   *
   * The table for the current model should have a field <em>modelname</em>_id .
   * The model name in that field must be lowercase.
   * If it does not exist, the pid field will used instead.
   *
   * Takes the same parameters as getAll().
   * 
   * Example for a Comment class:
   * <code>
   * protected $belongsTo = array( 'Post', 'NewsItem' );
   * </code>
   *
   * This array allow to get a parent object by calling its name as method.
   * <code>
   * $post = $comment->Post();
   * </code>
   */
  protected $belongsTo = array();


  /**
   * @var array Association "has one".
   *
   * Put in this array the child classes of your class.
   *
   * This is the opposite of $belongsTo. If a class name is listed there, there must
   * be a field <em>modelname</em>_id or pid in the table of the child.
   *
   * This association is to be used one there is only one child of this class.
   *
   * Takes the same parameters as getAll().
   * 
   * Example for a Post class:
   * <code>
   * protected $hasOne = array( 'Author' );
   * </code>
   *
   * This array allow to get a unique child object by calling its name as method.
   * <code>
   * $author = $post->Author();
   * </code>
   *
   * The return value is an EModel or null.
   */
  protected $hasOne = array();


  /**
   * @var array Association "has many".
   *
   * Put in this array the child classes of your class.
   *
   * Works like $hasOne, except it implies multiple children for a same class.
   *
   * Takes the same parameters as getAll().
   * 
   * Example for a Post class:
   * <code>
   * protected $hasMany = array( 'Comment' );
   * </code>
   *
   * This array allow to get an array of children object by calling their class name as method.
   * <code>
   * $comments = $post->Comment();
   * </code>
   *
   * The return value is an EModel or null.
   */
  protected $hasMany = array();


  /**
   * @var array (associative) Association "has one through".
   *
   * Put in this array the classes you want to access through an other associated class.
   *
   * The result will be found put delegating the call to an other class from $hasOne or $belongsTo
   *
   * Takes the same parameters as getAll().
   * 
   * Example for a Comment class:
   * <code>
   * protected $hasThrough = array( 'Theme' => 'Post' );
   * </code>
   *
   * Can be used like this :
   * <code>
   * $theme = $comment->Theme();
   * </code>
   *
   * The return value is an EModel or null if through class $hasOne or $belongsTo target,
   * it is an array of EModel's or an empty array if through class $hasMany target.
   */
  protected $hasThrough = array();


  /**
   * @var array (associative) Association "many to many".
   *
   * Put in this array the associated class and the jointure table.
   *
   * The jointure table must have two fields formated : <em>modelname</em>_id .
   * The model names in those fields must be lowercase.
   *
   * Takes the same parameters as getAll().
   *
   * Example for a Post class:
   * <code>
   * protected $manyToMany = array( 'Category' => 'tl_posts_categories' );
   * </code>
   *
   * Can be used like this :
   * <code>
   * $categories = $post->Categories();
   * 
   * // and reverse
   * $category = $categories[0];
   * $posts    = $category->Post();
   * </code>
   *
   * The return value is an array of EModel's or an empty array.
   */
  protected $manyToMany = array();



  /**
   * @var bool acts as a tree flag
   *
   * If set to true, the EModel is child and parent of its own class.
   * Its table should have a pid field handling the id of its parent.
   *
   * This enable the use of treeChildren(), treeParent(), getDescendants(), isChildOf() and isParentOf().
   */
  protected $treeAssoc = false;


  /**
   * @var array attributes to validate presence of
   *
   * If an attribute is put in this array, the save() method
   * will be stopped and an error will be set if the value for this
   * attribute is null or an empty string.
   */
  protected $validates_presence_of = array();


  /**
   * @var array attributes to validate uniqueness of
   *
   * If an attribute is put in this array, the save() method
   * will be stopped and an error will be set if the value for this
   * attribute already exists in the same table.
   */
  protected $validates_uniqueness_of = array();


  /**
   * @var array (associative) attributes to validate format of
   *
   * If an attribute is put in this array, the save() method
   * will be stopped and an error will be set if the value for this
   * attribute doesn't match the regex.
   *
   * Example :
   * <code>
   * protected $validates_format_of = array( 'hour' => '/^\d{2}:\d{2} *(?:AM|PM)?$/' );
   * </code>
   */
  protected $validates_format_of = array();


  /**
   * @var array attributes to validate numericality of
   *
   * If an attribute is put in this array, the save() method
   * will be stopped and an error will be set if the value for this
   * attribute is not numeric.
   */
  protected $validates_numericality_of = array();


  /**
   * @var array (associative) attributes to validate minimum length of
   *
   * If an attribute is put in this array, the save() method
   * will be stopped and an error will be set if length of the value for this
   * attribute is lower (<)  than specified.
   *
   * Example :
   * <code>
   * protected $validates_min_length_of = array( 'nick' => 4 );
   * </code>
   */
  protected $validates_min_length_of = array();


  /**
   * @var array (associative) attributes to validate maximum length of
   *
   * If an attribute is put in this array, the save() method
   * will be stopped and an error will be set if length of the value for this
   * attribute is greater (>) than specified.
   *
   * Example :
   * <code>
   * protected $validates_max_length_of = array( 'nick' => 16 );
   * </code>
   */
  protected $validates_max_length_of = array();


  /**
   * @var array attributes to validate association with
   *
   * If a class name is put in this array, the save() method
   * will be stopped and an error will be set if the object
   * is not associated with at least one object for this class.
   *
   * Example for a Post model:
   * <code>
   * protected $validates_associated = array( 'Author', 'Category' );
   * </code>
   */
  protected $validates_associated = array();


  /**
   * @var array Array to list attributes that can be send through json.
   *
   * If you want to use the json layer, put the attribute that can be accessed
   * as json in this array.
   * Empty by default for security reasons.
   */
  protected $jsonable = array();


  /**
   * @var array Attributes that are allowed to be set through updateAttributes() and create().
   * If empty, any attribute can be set.
   **/
  protected $filtered_attrs = array();


  /**
   * @var array Language array
   *
   * This array stock languages informations.
   * It is a shortcut for $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ '<em>ModelName</em>' ]
   **/
  public $lang;


  /**
   * @var array uncachable array
   *
   * By default, getters, setters and associations cache their return value for
   * faster retrieval. Caching can still be bypassed par calling a getter by its
   * function name instead that by virtual attribute, but if a attribute must
   * never be cached, you can put it in this array.
   **/
  protected $uncachable = array();



  /**
   * Before save callback.
   *
   * Give a list of method names, in an array.
   * Those methods will be executed before saving the object, either if the record
   * already exists or not.
   * Return false in the method to prevent save.
   **/
   protected $beforeSave = array();



  /**
   * After save callback.
   *
   * Give a list of method names, in an array.
   * Those methods will be executed after saving the object, either if the record
   * already exists or not.
   **/
   protected $afterSave = array();



  /**
   * Before create callback.
   *
   * Give a list of method names, in an array.
   * Those methods will be executed before saving the object, if the record
   * doesn't exist yet.
   * Return false in the method to prevent save.
   **/
   protected $beforeCreate = array();



  /**
   * After create callback.
   *
   * Give a list of method names, in an array.
   * Those methods will be executed after saving the object, if the record
   * doesn't exist yet.
   **/
   protected $afterCreate = array();



  /**
   * Before update callback.
   *
   * Give a list of method names, in an array.
   * Those methods will be executed before saving the object, if the record
   * already exists.
   * Return false in the method to prevent save.
   **/
   protected $beforeUpdate = array();



  /**
   * After update callback.
   *
   * Give a list of method names, in an array.
   * Those methods will be executed after saving the object, if the record
   * already exists.
   **/
   protected $afterUpdate = array();



  /**
   * Before delete callback.
   *
   * Give a list of method names, in an array.
   * Those methods will be executed before deleting the object.
   * Return false in the method to prevent deletion.
   **/
   protected $beforeDelete = array();



  /**
   * After delete callback.
   *
   * Give a list of method names, in an array.
   * Those methods will be executed after deleting the object.
   **/
   protected $afterDelete = array();



  protected $_order_clause;
  protected $arrErrors = array();
  protected $arrCache  = array();
  public    $paginate_page;
  public    $paginate_page_count;

  const FIND_FIRST  = 'find_first_by_';
  const FIND_ALL    = 'find_all_by_';
  const FIND_DELIM  = '(_and_not_|_and_|_or_)';
  const FIND_ORDER  = '_order_by_';



  /**
   * Constructor
   *
   * There are two way to use this constructor :
   *
   * <em>1°) In order to create a new object</em>
   * You can simply call the constructor to get a new object. Then, set its attributes
   * and save it to have a new record.
   *
   * <code>
   * $myModel = new MyModel();
   * $myModel->anAttr = 'aValue';
   * $myModel->save();
   * </code>
   *
   * <em>2°) In order to find a record</em>
   * You can also use the constructor to directly retrieve a record by its id.
   * Please note that there is no mean to know if the record actually exists
   * when the constructor return. So if you're unsure of its existence, please
   * use findBy() instead.
   *
   * <code>
   * $author = new Author( 10 );
   *
   * // make sense in a dca callback, until typolight 2.8 :
   * public function onSubmit( $dca )
   * {
   *   $author = new Author( $dca->id );
   * }
   * </code>
   *
   * @param interger id of the record to retrieve
   */
  public function __construct( $id = false )
  {
    $this->uncachable[] = 'data';
    parent::__construct();

    if ( ! isset( $GLOBALS[ 'TL_LANG' ][ 'MSC' ] ) )
    {
      $this->loadLanguageFile( 'default', $GLOBALS[ 'TL_LANGUAGE' ] );
    }

    $this->lang = $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ get_class( $this ) ];

    if ( $id !== false )
    {
      $this->findBy( 'id', $id );
    }
  }


  /**
   * Getters can be set to use some virtual attributes.
   * If something as you work with an object represents a state or a value,
   * rather than being an action, it should definitivly be an attribute,
   * not a method, wether the return value is computed or not.
   *
   * This implementation of getters let you declare virtual attributes as
   * simply as this :
   * <code>
   * public function getSomething()
   * {
   *   $part1 = strtolower( $this->anAttr );
   *   $part2 = trim( $this->anOther );
   *   return $part1 . ' ' . $part2;
   * }
   * </code>
   *
   * You can then call your virtual attribute as any regular one :
   * <code>
   * echo $myModel->something;
   * </code>
   *
   * Please note the format : getSomething() has a capital 'S' that becomes
   * lowercase 's' in the virtual attribute name.
   *
   * Result will be cached, so any further call will product the same
   * result. If you want to prevent this, add "something" in the
   * $uncachable array.
   *
   * As a bonus, a setter method will be automatically created as well. It
   * simply replace or set the cached value with the argument. If you need
   * something more elaborated, see __set().
   */
  public function __get( $key )
  {
    if ( array_key_exists( $key, $this->arrData ) )
    {
      return $this->arrData[ $key ];
    }

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
   * Setters are used, as getters from __get(), to manipulate virtual attributes.
   * If you have defined a virtual attribute with a getter, a setter method
   * corresponding will automatically be created to change the cached value.
   *
   * But you may still need to do some special computing before storing it.
   * This is what setters are about.
   *
   * In the model:
   * <code>
   * public function setSomething( $value )
   * {
   *   return trim( $value );
   * }
   * </code>
   *
   * Then:
   * <code>
   * $myModel->something = ' some text ';
   * </code>
   */
  public function __set( $key, $value )
  {
    $firstLetter = substr( $key, 0, 1 );
    $rest        = substr( $key, 1 );
    $setter      = 'set' . strtoupper( $firstLetter ) . $rest;

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
   * Use this method to save your object in the database.
   *
   * Before being save, validations will be tested against the object.
   * If the object does not validates, false will be return and the
   * hasError() method will return true.
   * You can use the virtual attribute "errors" to find the error,
   * or errorsOn().
   *
   * On success, record id will be returned.

   * save() also update the tstamp field, a set the created_at field
   * if it exists and the object is a new record.
   *
   * @return false|integer record id on success or false
   */
  public function save( $blnForceInsert = null )
  {
    $update = !! $this->id;

    if ( $update )
    {
      if ( count( $this->beforeUpdate ) )
      {
        foreach ( $this->beforeUpdate as $method )
        {
          $return = $this->$method();
          if ( $return === false )
          {
            return false;
          }
        }
      }
    }

    else
    {
      if ( count( $this->beforeCreate ) )
      {
        foreach ( $this->beforeCreate as $method )
        {
          $return = $this->$method();
          if ( $return === false )
          {
            return false;
          }
        }
      }
    }

    if ( count( $this->beforeSave ) )
    {
      foreach ( $this->beforeSave as $method )
      {
        $return = $this->$method();
        if ( $return === false )
        {
          return false;
        }
      }
    }

    $this->tstamp = time();
    $this->validate();
    if ( ! $this->hasError() )
    {
      if ($this->blnRecordExists && !$blnForceInsert)
      {
        $rows = $this->Database->prepare("UPDATE `" . $this->strTable . "` %s WHERE `" . $this->strRefField . "` = ?")
                               ->set($this->arrData)
                               ->execute($this->varRefId)
                               ->affectedRows;

        if ( $rows > 0 )
        {
          return $this->id;
        }

        else
        {
          return false;
        }
      }

      else
      {
        if ( $this->Database->fieldExists( 'created_at', $this->strTable ) )
        {
          $this->created_at = time();
        }

        $id = $this->Database->prepare("INSERT INTO `" . $this->strTable . "` %s")
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

    if ( $update )
    {
      if ( count( $this->afterUpdate ) )
      {
        foreach ( $this->afterUpdate as $method )
        {
          $this->$method();
        }
      }
    }

    else
    {
      if ( count( $this->afterCreate ) )
      {
        foreach ( $this->afterCreate as $method )
        {
          $this->$method();
        }
      }
    }

    if ( count( $this->afterSave ) )
    {
      foreach ( $this->afterSave as $method )
      {
        $this->$method();
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
   * Update the attributes and save the record.
   * 
   * @param array (associative) attributes and their value
   * @return bool|integer the id of the saved record or false if error
   */
  public function updateAttributes( $attributes )
  {
    $this->attributes = $attributes;
    return $this->save();
  }



  /**
   * Set attributes without saving, but by filtering against filtered_attributes
   *
   * @param array (associative) attributes and their value
   **/
   public function setAttributes( $attributes )
   {
     foreach ( $attributes as $attr => $value )
     {
       if ( ! in_array( $attr, $this->filtered_attrs ) )
       {
         $this->$attr = $value;
       }
     }
   }



  /** 
   * Create from attributes
   * 
   * @param array (associative) attributes and their value
   * @return bool|integer the id of the saved record or false if error
   * @throws Exception if a filtered attribute is passed in the array
   */
  public function create( $attributes )
  {
    $this->setData( $attributes );
    return $this->save();
  }



  /**
   * Remove a record from database.
   * If the model has a many to many association,
   * the jointure table will be clean up for this record.
   *
   * @return bool success
   */
  public function delete()
  {
    if ( count( $this->beforeDelete ) )
    {
      foreach ( $this->beforeDelete as $method )
      {
        $return = $this->$method();
        if ( $return === false )
        {
          return false;
        }
      }
    }

    if ( count( $this->manyToMany ) )
    {
      $this->cleanupAssociation();
    }

    $return = !! parent::delete();

    if ( $return )
    {
      if ( count( $this->afterDelete ) )
      {
        foreach ( $this->afterDelete as $method )
        {
          $this->$method();
        }
      }

      return true;
    }

    return false;
  }



  /**
   * Validatations are automatically computed from the validates arrays.
   * Each attribute can have errors, that can be retrieve with errorsOn( $attr ).
   * You can customize errors message using $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ '<em>YourModel</em>' ]
   * with those keys :
   *  validates_presence_of: <em>attr</em>_required 
   *  validates_uniqueness_of: <em>attr</em>_uniqueness 
   *  validates_format_of: <em>attr</em>_format 
   *  validates_numericality_of: <em>attr</em>_numericality 
   *  validates_min_length_of: <em>attr</em>_min_length 
   *  validates_max_length_of: <em>attr</em>_max_length 
   *  validates_associated: <em>attr</em>_associated 
   *
   */
  public function validate()
  {
    foreach ( $this->validates_presence_of as $attr )
    {
      if ( is_null( $this->$attr ) or ( is_string( $this->$attr ) and ! strlen( $this->$attr ) ) )
      {
        $message = $this->lang[ $attr . '_required' ];
        if ( ! strlen( $message ) )
        {
          $message = sprintf( $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'EModel' ][ 'validates_presence_of' ], $attr );
        }

        $this->setError( $message, $attr );
      }
    }


    foreach ( $this->validates_uniqueness_of as $attr )
    {
      $class = get_class( $this );
      $model = new $class();

      if ( $model->getCount( array( "$attr = ?", $this->$attr ) ) > 0 )
      {
        $message = $this->lang[ $attr . '_uniqueness' ];
        if ( ! strlen( $message ) )
        {
          $message = sprintf( $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'EModel' ][ 'validates_uniqueness_of' ], $this->$attr );
        }

        $this->setError( $message, $attr );
      }
    }


    foreach ( $this->validates_format_of as $attr => $format )
    {
      if ( ! preg_match( $format, $this->$attr ) )
      {
        $message = $this->lang[ $attr . '_format' ];
        if ( ! strlen( $message ) )
        {
          $message = sprintf( $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'EModel' ][ 'validates_format_of' ], $attr );
        }

        $this->setError( $message, $attr );
      }
    }


    foreach ( $this->validates_numericality_of as $attr )
    {
      if ( ! is_numeric( $this->$attr ) )
      {
        $message = $this->lang[ $attr . '_numericality' ];
        if ( ! strlen( $message ) )
        {
          $message = sprintf( $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'EModel' ][ 'validates_numericality_of' ], $attr );
        }

        $this->setError( $message, $attr );
      }
    }


    foreach ( $this->validates_min_length_of as $attr => $min_length )
    {
      if ( strlen( $this->$attr ) < $min_length )
      {
        $message = $this->lang[ $attr . '_min_length' ];
        if ( ! strlen( $message ) )
        {
          $message = sprintf( $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'EModel' ][ 'validates_min_length_of' ], $attr, $min_length );
        }

        $this->setError( $message, $attr );
      }
    }


    foreach ( $this->validates_max_length_of as $attr => $max_length )
    {
      if ( strlen( $this->$attr ) > $max_length )
      {
        $message = $this->lang[ $attr . '_max_length' ];
        if ( ! strlen( $message ) )
        {
          $message = sprintf( $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'EModel' ][ 'validates_max_length_of' ], $attr, $max_length );
        }

        $this->setError( $message, $attr );
      }
    }


    foreach ( $this->validates_associated as $attr )
    {
      $error = false;
      try
      {
        $associated = $this->$attr();
        if ( ! $associated or ( is_array( $associated ) and ! count( $associated ) ) )
        {
          $error = true;
        }
      }

      catch ( Exception $e )
      {
        $error = true;
      }

      if ( $error )
      {
        $message = $this->lang[ $attr . '_associated' ];
        if ( ! strlen( $message ) )
        {
          $message = sprintf( $GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'EModel' ][ 'validates_associated' ], get_class( $this ), $attr );
        }

        $this->setError( $message, $attr );
      }
    }

    $this->customValidate();

    return ! $this->hasErrors();
  }


  /**
   * Override this method to add custom validations.
   *
   * The return value of this function is ignored, but
   * you can use setError() to prevent record from being saved.
   **/
  protected function customValidate()
  {
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
   * Can be called as $model->errors
   * @return array
   */
  public function getErrors()
  {
    return $this->arrErrors;
  }



  /**
   * Get the errors on a particular attribute
   * @param string  the attribute
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
   * Find all records
   *
   * Return an array of instantiated models.
   *
   * Optional parameters let you define clauses :
   * - The order clause should be the sorting field and optionaly  "desc"
   * - The where clause is formed by an array with the string clause at
   *   first with optional '?' placeholders, and the values for those place
   *   holders as other items. Placeholder values will be escaped.
   * - limit is an integer which limit the number of returned objects
   *
   * If you don't need any clause, you can simply use : $model->all
   *
   * Examples:
   * <code>
   * $carbon  = new Post();
   * $posts   = $carbon->all;
   * $latests = $carbon->getAll( 'created_at desc', array( 'start < ? and stop > ? and published = 1', time(), time() ), 5 );
   * </code>
   *
   * @param string order clause
   * @param array|null condition clause
   * @param integer|null limit clause
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

    $record = $this->Database->prepare( "select * from `" . $this->strTable . '` ' . $where_clause . " order by " . $order . ( $limit ? ' limit ' . $limit  : '' ) )
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
   * Get a paginated collection
   *
   * If you want to do some pagination, this method is for you.
   * Simply give an order and a where clause, just like getAll()
   * and say how many item per page you want, and optionnaly on
   * which page we are.
   *
   * You can then use, for instance, FrontendController#preparePagination()
   * To create the necessary html.
   *
   * @param string order clause
   * @param array|null condition clause
   * @param integer number of items per page
   * @param integer starting page
   * @return array
   **/
  public function getPaginate( $order = "id", $where = null, $perPage = 10, $startPage = 1 )
  {
    $collection = $this->getAll( $order, $where, $perPage * ($startPage - 1) . ',' . $perPage );
    $this->paginate_page        = $startPage;
    $this->paginate_page_count  = ceil( $this->getCount( $where ) / $perPage );

    return $collection;
  }



  /**
   * Get the count of items
   *
   * Get the count of rows for this table.
   * Accept a condition as parameter, formated like the getAll() one.
   * 
   * @param array|null condition clause
   * @return integer
   */
  public function getCount( $where = null )
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

    $record = $this->Database->prepare( 'select count(*) from ' . $this->strTable . ' ' . $where_clause )->execute( $where_values );
    $record->next();
    $row    = $record->row();
    return $row[ 'count(*)' ];
  }



  /**
   * Get all id's, so we can use in_array
   *
   * Optionaly, you can pass an array of models as parameter.
   * Id from items in the array will be returned, with no regard
   * of the current model.

   * @param array|null collection : an array of models to get the id from.
   * @return array the list of id's
   */
  public function ids( $collection = null )
  {
    $ids = array();

    if ( is_array( $collection ) )
    {
      foreach ( $collection as $item )
      {
        $ids[] = $item->id;
      }
    }

    else
    {
      $record = $this->Database->execute( 'select id from ' . $this->strTable );
      while ( $record->next() )
      {
        $ids[] = $record->id;
      }
    }


    return $ids;
  }



  /**
   * Set the current object to the first record
   *
   * Optionaly, you can pass an order clause and a condition
   * clause, just like with getAll().
   * 
   * @param string|null order clause
   * @param array|null condition clause
   * @return boolean return false if there are no records
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

    $record = $this->Database->prepare( 'select * from `' . $this->strTable . '` ' . $where_clause . ' order by ' . $order . ' limit 1' )
                             ->execute( $where_values );

    if ( $record->next() )
    {
      $this->setFound( $record->row() );
      return true;
    }

    return false;
  }



  /**
   * Set the current object to the last record
   *
   * Optionaly, you can pass an order clause and a condition
   * clause, just like with getAll().
   * 
   * @param string|null order clause
   * @param array|null condition clause
   * @return boolean return false if there are no records
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

    $record = $this->Database->prepare( 'select * from `' . $this->strTable . '` ' . $where_clause . ' order by ' . $order . ' desc limit 1' )
                             ->execute( $where_values );

    if ( $record->next() )
    {
      $this->setFound( $record->row() );
      return true;
    }

    return false;
  }



  /**
   * Act as setData from Model, but also set protected
   * variables as with findBy.
   *
   * This let you set a object from an array.
   * Example:
   *
   * <code>
   * $post        = new Post();
   * $post->found = $row;
   * </code>
   *
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

    $this->blnRecordExists  = true;
    $this->strRefField      = 'id';
    $this->varRefId         = $varData[ 'id' ];
    $this->arrData          = $varData;
  }



  /**
   * Additionnaly to managing associations, magic method
   * __call() also handle dynamic finders.
   *
   * Dynamic finders let you express sql finders as method
   * name. Variables for conditions are passed as parameter.
   *
   * A find_first statement set the current object to the find
   * one or return false, just like first().
   *
   * A find_all statement return an array, just like getAll().
   *
   * You can additionnaly use _and_, _and_not_ and _or_.
   *
   * Finaly, you can use _order_by_.
   *
   * Example:
   * <code>
   * $post    = new Post();
   * $member  = new Member();
   * $posts   = $post->find_all_by_published( 1 );
   * $howtos  = $post->find_all_by_published_and_theme( 1, 'howto' );
   * $socials = $post->find_all_by_theme_or_theme( 'politic', 'science' );
   * $actives = $member->find_all_by_login_and_not_banned( 1, 1 );
   * $news    = $post->find_all_by_published_order_by_created_at_desc( 1 );
   * $success = $post->find_first_by_id( 12 );
   * $success = $post->find_first_by_id_and_published( 12, 1 );
   * $success = $post->find_first_by_id_and_not_published( 12, 1 );
   * $success = $post->find_first_by_published_order_by_id_desc( 1 );
   * </code>
   * 
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

    if ( array_key_exists( $stmt, $this->hasThrough ) )
    {
      return $this->through( $stmt );
    }

    if ( array_key_exists( $stmt, $this->manyToMany ) )
    {
      return $this->manyToMany( $stmt, $params );
    }

    throw new Exception( 'undefined method:' . $stmt );
  }



  /**
   * Determine is the model has the given field
   * @param string the field to test
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
   *
   * @param boolean recursivity
   * @return array the json ready array
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

        foreach ( $this->hasThrough as $through )
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
   * All cached results are dropped
   */
  public function flushCache()
  {
    $this->arrCache = array();
  }



  /*
   * Find parent - treeAssoc relationship
   * @return obj
   */
  public function treeParent()
  {
    if ( $this->arrCache[ 'associations' ][ 'treeParent' ] )
    {
      return $this->arrCache[ 'associations' ][ 'treeParent' ];
    }

    $class        = get_class( $this );
    $owner_field  = 'pid';
    $parent       = new $class( $this->pid );

    $this->arrCache[ 'associations' ][ 'treeParent' ] = $parent;
    return $parent;
  }



  /*
   * Find children - treeAssoc relationship
   * @arg   mixed    the where clause array
   * @return mixed
   */
  public function treeChildren( $clauses = array() )
  {
    if ( $this->arrCache[ 'associations' ][ 'treeChildren' ] and ! count( $clauses ) )
    {
      return $this->arrCache[ 'associations' ][ 'treeChildren' ];
    }

    $class          = get_class( $this );
    $carbon         = new $class();

    $where_clause = array( 'pid = ?', $this->id );
    if ( count( $clauses ) )
    {
      if ( is_array( $clauses[1] ) and count( $clauses[1] ) )
      {
        $where_clause[0] .= ' and ' . $clauses[1][0];
        unset( $clauses[1][0] );
        $where_clause = array_merge( $where_clause, $clauses[1] );
      }

      $children = $carbon->getAll( $clauses[0], $where_clause, $clauses[2] );
    }

    else
    {
      $children = $carbon->getAll( 'id', $where_clause );
      $this->arrCache[ 'associations' ][ 'treeChildren' ] = $children;
    }

    return $children;
  }



  /**
   * Say if current object is child of given object - treeAssoc relationship
   *
   * @param EModel the relative to test
   * @return boolean the result
   **/
  public function isChildOf( $relative )
  {
    if ( ! $this->treeAssoc )
    {
      throw new Exception( get_class( $this ) . ' is not set as a tree association ( protected $treeAssoc = false; )' );
    }

    $children = $relative->descendants;
    array_shift( $children );
    $ids      = $relative->ids( $children );

    return in_array( $this->id, $ids );
  }



  /**
   * Say if current object is parent of given object - treeAssoc relationship
   *
   * @param EModel the relative to test
   * @return boolean the result
   **/
  public function isParentOf( $relative )
  {
    if ( ! $this->treeAssoc )
    {
      throw new Exception( get_class( $this ) . ' is not set as a tree association ( protected $treeAssoc = false; )' );
    }

    $children = $this->descendants;
    array_shift( $children );
    $ids      = $this->ids( $children );

    return in_array( $relative->id, $ids );
  }



  /**
   * Get all descendants of current object, including itself - treeAssoc relationship
   *
   * @param EModel the relative to test
   * @return boolean the result
   **/
  public function getDescendants()
  {
    if ( ! $this->treeAssoc )
    {
      throw new Exception( get_class( $this ) . ' is not set as a tree association ( protected $treeAssoc = false; )' );
    }

    $descendants = array( $this );
    foreach ( $this->treeChildren() as $child ) 
    {
      $descendants = array_merge( $descendants, $child->descendants );
    }

    return $descendants;
  }



  /*
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



  /*
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
    $child_field = strtolower( $class ) . '_pid';

    if ( ! $child->hasField( $child_field ) )
    {
      $child_field = 'pid';
    }

    $find = "find_first_by_" . $child_field;
    $child->$find( $this->id );
    $this->arrCache[ 'associations' ][ $class ] = $child;
    return $child;
  }



  /*
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
      if ( is_array( $clauses[1] ) and count( $clauses[1] ) )
      {
        $where_clause[0] .= ' and ' . $clauses[1][0];
        unset( $clauses[1][0] );
        $where_clause = array_merge( $where_clause, $clauses[1] );
      }

      $children = $carbon->getAll( $clauses[0], $where_clause, $clauses[2] );
    }

    else
    {
      $children = $carbon->getAll( 'id', $where_clause );
      $this->arrCache[ 'associations' ][ $class ] = $children;
    }

    return $children;
  }



  /*
   * Find a related through an other
   * @return mixed
   */
  public function through( $class )
  {
    if ( $this->arrCache[ 'associations' ][ $class ] )
    {
      return $this->arrCache[ 'associations' ][ $class ];
    }

    $through = $this->hasThrough[ $class ];
    $step    = $this->$through();
    $target  = $step->$class();
    $this->arrCache[ 'associations' ][ $class ] = $target;
    return $target;
  }



  /*
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

    $relateds           = array();
    $currentClassField  = strtolower( get_class( $this ) ) . '_id';
    $targetClassField   = strtolower( $class ) . '_id';
    $table              = $this->manyToMany[ $class ];

    $record = $this->Database->prepare( sprintf( "select * from `%s` where `%s` = ?", $table, $currentClassField ) )
                             ->execute( $this->id );

    $carbon = new $class();
    $i = 0;

    while ( $record->next() )
    {
      $related = clone $carbon;
      $where_clause = array( 'id = ?', $record->$targetClassField );

      // order, where and/or limit clauses have been defined
      if ( count( $clauses ) )
      {
        // check if there is a where clause
        if ( is_array( $clauses[1] ) and count( $clauses[1] ) )
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

        elseif ( is_string( $clauses[2] ) )
        {
          $limits = explode( ',', $clauses[2] );
          if ( $i < (int) $limits[0] )
          {
            $i++;
            continue;
          }

          if ( $i == ((int) $limits[0]) + ((int) $limits[1]) )
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


    if ( strlen( $clauses[0] ) )
    {
      $chunks = explode( ' ', $clauses[0] );
      $this->_order_clause = $chunks[0];

      if ( $chunks[1] == 'desc' )
      {
        usort( $relateds, array( $this, 'manySortDesc' ) );
      }

      else
      {
        usort( $relateds, array( $this, 'manySortAsc' ) );
      }
    }

    if ( ! count( $clauses ) )
    {
      $this->arrCache[ 'associations' ][ $class ] = $relateds;
    }

    return $relateds;
  }



  /*
   * Sort many to many results ascendingly
   */
  protected function manySortAsc( $first, $second )
  {
    $order = $this->_order_clause;

    if ( is_numeric( $first->$order ) and is_numeric( $second->$order ) )
    {
      if ( $first->$order == $second->$order )
      {
        return 0;
      }

      return ( $first->$order < $second->$order ) ? -1 : 1;
    }

    else
    {
      return strnatcmp( $first->$order, $second->$order );
    }
  }



  /*
   * Sort many to many results descendingly
   */
  protected function manySortDesc( $first, $second )
  {
    $order = $this->_order_clause;

    if ( is_numeric( $first->$order ) and is_numeric( $second->$order ) )
    {
      if ( $first->$order == $second->$order )
      {
        return 0;
      }

      return ( $second->$order < $first->$order ) ? -1 : 1;
    }

    else
    {
      return strnatcmp( $second->$order, $first->$order );
    }
  }



  /*
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
      $hereField  = strtolower( get_class( $this ) ) . '_id';
      $thereField = strtolower( $associated ) . '_id';

      $this->Database->prepare( sprintf( "delete from `%s` where `%s` = ?", $table, $hereField ) )
                     ->execute( $this->id );

      foreach ( $ids as $id )
      {
        $this->Database->prepare( sprintf( "insert into `%s`( `%s`, `%s` ) values( ?, ? )", $table, $hereField, $thereField ) )
                       ->execute( $this->id, (int) $id );
      }
    }
  }



  /*
   * Add a related - manyToMany relationship
   * @param string
   * @param mixed
   * @return boolean
   * TODO : set the got many flag
   */
  public function addManyToMany( $associated, $id )
  {
    if ( array_key_exists( $associated, $this->manyToMany ) )
    {
      $table      = $this->manyToMany[ $associated ];
      $hereField  = strtolower( get_class( $this ) ) . '_id';
      $thereField = strtolower( $associated ) . '_id';

      $record = $this->Database->prepare( sprintf( "select * from `%s` where `%s` = ? and `%s` = ?", $table, $hereField, $thereField ) )
                               ->execute( $this->id, $id );

      if ( ! $record->next() and is_numeric( $id ) )
      {
        $this->Database->prepare( sprintf( "insert into `%s`( `%s`, `%s` ) values( ?, ? )", $table, $hereField, $thereField ) )
                       ->execute( $this->id, $id );

        return true;
      }
    }

    return false;
  }



  /*
   * Remove a related - manyToMany relationship
   * @param string
   * @param mixed
   * @return boolean
   * TODO : set the got many flag
   */
  public function removeManyToMany( $associated, $id )
  {
    if ( array_key_exists( $associated, $this->manyToMany ) )
    {
      $table = $this->manyToMany[ $associated ];
      $record = $this->Database->prepare( sprintf( "select * from `%s` where `%s` = ? and `%s` = ?", $table, get_class( $this ), $associated ) )
                               ->execute( $this->id, $id );

      if ( $record->next() and is_numeric( $id ) )
      {
        $this->Database->prepare( sprintf( "delete from `%s` where  `%s` = ? and `%s` = ?", $table, get_class( $this ), $associated ) )
                       ->execute( $this->id, $id );

        return true;
      }
    }

    return false;
  }



  /*
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
    preg_match_all( self::FIND_DELIM, $where_clause_str, $delims ) ;

    if ( count( $fields ) != count( $params ) )
    {
      error_log( sprintf( "The number of fields and of params does not match: %s," . $params, $stmt ) ) ;
      return false ;
    }

    /* build the query */

    $query  = sprintf( "select * from `%s` where ", $this->strTable ) ;

    foreach( $fields as $i => $field )
    {
      if ( $i == 0 ) // no delimiter for now
      {
        $query .= sprintf( "`%s` = ? ", $field ) ;
      }

      else
      {
        $delim = $delims[$i-1][0] ;
        $delim = str_replace( '_', ' ', $delim ) ;
        $query .= sprintf( "%s `%s` = ? ", $delim, $field ) ;
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
        $this->Database->prepare( 'delete from `' . $table . '` where `' . get_class( $this ) . '` = ?' )
                       ->execute( $this->id );
      }
    }
  }


}

