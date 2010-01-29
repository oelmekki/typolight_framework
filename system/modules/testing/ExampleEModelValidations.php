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
 * @copyright  
 * @author    
 * @package    
 * @license    
 * @filesource
 */


/**
 * Class ExampleEModelValidations
 *
 * @copyright  
 * @author     
 * @package    Model
 */

class ExampleEModelValidations extends EModel
{
  protected $strTable = 'tl_example_emodel_validations';

  protected $validates_presence_of      = array( 'name' );
  protected $validates_uniqueness_of    = array( 'name' );
  protected $validates_format_of        = array( 'phone' => '/^\d+$/' );
  protected $validates_numericality_of  = array( 'phone' );
  protected $validates_min_length_of    = array( 'name' => 2 );
  protected $validates_max_length_of    = array( 'name' => 12 );
  protected $validates_associated       = array( 'ExampleEModel1' );


  public function customValidate()
  {
    if ( $this->name == 'bad name' )
    {
      $this->setError( 'bad name iz bad name', 'name' );
    }
  }
}

