[?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005-2009 Leo Feyer
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
 * Class <?php echo $this->backendClass ?> 
 *
 * @copyright  
 * @author     
 * @package    Controller
 */
class <?php echo $this->backendClass ?> extends BackendController
{
  protected $controller = '<?php echo str_replace( 'tl_', '', $this->table ) ?>';
  protected $arrActions = array();



  /**
   * default action
   */
  protected function index()
  {
  }



  public function setCreatedAt( $dca )
  {
    $record = $this->Database->prepare( 'select created_at from <?php echo $this->table ?> where id = ?' )
                             ->execute( $dca->id );

    $record->next();

    if ( ! $record->created_at )
    {
      $this->Database->prepare( 'update <?php echo $this->table ?> set created_at = ? where id = ?' )
                     ->execute( time(), $dca->id );
    }
  }
}

