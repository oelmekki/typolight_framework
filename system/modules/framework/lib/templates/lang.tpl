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
 * Fields
 */
<?php foreach ( $this->fields as $field => $type ) : ?>
$GLOBALS['TL_LANG']['<?php echo $this->table ?>']['<?php echo $field ?>'] = array('<?php echo $field ?>', '');
<?php endforeach ?>


/**
 * Reference
 */
$GLOBALS['TL_LANG']['<?php echo $this->table ?>'][''] = '';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['<?php echo $this->table ?>']['new']    = array('new', 'new');
$GLOBALS['TL_LANG']['<?php echo $this->table ?>']['edit']   = array('edit', '%s');
$GLOBALS['TL_LANG']['<?php echo $this->table ?>']['copy']   = array('copy', '%s');
$GLOBALS['TL_LANG']['<?php echo $this->table ?>']['delete'] = array('delete', '%s');
$GLOBALS['TL_LANG']['<?php echo $this->table ?>']['show']   = array('show', '%s');

