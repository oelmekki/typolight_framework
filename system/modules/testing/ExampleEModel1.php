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
 * Class ExampleEModel1
 *
 * @copyright  
 * @author     
 * @package    Model
 */

class ExampleEModel1 extends EModel
{
  protected $strTable = 'tl_example_emodel_1';
  protected $forGetter = 0;
  protected $uncachable = array( 'time' );
  protected $filtered_attrs = array( 'id', 'tstamp', 'created_at' );

  public function getForMe()
  {
    $this->forGetter += 1;
    return $this->forGetter;
  }


  public function getTime()
  {
    return time();
  }

  public function setForMe( $value )
  {
    return $this->forGetter = $value . 'abc';
  }


  public function getLanguage()
  {
    return $this->lang;
  }
}

