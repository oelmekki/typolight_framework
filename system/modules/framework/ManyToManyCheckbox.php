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
 * Class ManyToManyCheckbox
 * Provide methods to handle check boxes in the many to many relationship
 *
 * This widget can be used to handle the many to many relationship.
 * It will use the two models specified as thisModel ( for the current
 * table ) and thatModel ( for the related ) in the eval field.
 * A Jointure table must be defined in the $manyToMany array in each model.
 *
 * @copyright  Olivier El Mekki 2009
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Widget
 */
class ManyToManyCheckbox extends Widget
{

  /**
   * Submit user input
   * @var boolean
   */
  protected $blnSubmitInput = true;

  /**
   * Template
   * @var string
   */
  protected $strTemplate = 'be_widget_chk';

  /**
   * Options
   * @var array
   */
  protected $arrOptions = array();



  public function __construct( $arrAttributes = false )
  {
    parent::__construct( $arrAttributes );
  }


  /**
   * Add specific attributes
   * @param string
   * @param mixed
   */
  public function __set($strKey, $varValue)
  {
    switch ($strKey)
    {
      case 'mandatory':
        $this->arrConfiguration['mandatory'] = $varValue ? true : false;
        break;

      case 'thisModel':
        $this->arrConfiguration[ 'thisModel' ] = $varValue;
        break;

      case 'thatModel':
        $this->arrConfiguration[ 'thatModel' ] = $varValue;
        break;

      case 'foreignRef':
        $this->arrConfiguration[ 'foreignRef' ] = $varValue;
        break;

      default:
        parent::__set($strKey, $varValue);
        break;
    }
  }


  /**
   * Update the jointure table and set a ( not yet used ) got many boolean flag
   */
  public function validate()
  {
    parent::validate();

    if (!array_key_exists($this->strName, $_POST))
    {
      $this->varValue = 'false';
    }

    else
    {
      $values = $this->varValue;
      $id = $this->Input->get( 'id' );
      $modelClass = $this->thisModel;
      $model = new $modelClass( $id );
      $model->setManyToMany( $this->thatModel, $values );
      $this->varValue = true;
    }
  }


  /**
   * Generate the widget and return it as string
   * @return string
   */
  public function generate()
  {
    $modelThisClass = $this->thisModel;
    $modelThatClass = $this->thatModel;
    $arrOptions = array();

    $that = new $modelThatClass();
    $thats = $that->all;
    $ref = ( strlen( $this->foreignRef ) ? $this->foreignRef : 'name' );
    foreach ( $thats as $that )
    {
      $arrOptions[] = array( 'value' => $that->id, 'label' => $that->$ref );
    }
    $this->arrOptions = $arrOptions;


    $arrOptions = array();

    $model = new $modelThisClass();
    if ( $model->findBy( 'id', $this->Input->get( 'id' ) ) )
    {
      $thats = $model->$modelThatClass();

      foreach ( $thats as $that )
      {
        $arrOptions[] = $that->id; 
      }
    }

    $this->varValue = $arrOptions;

    $arrOptions = array();

    $state = $this->Session->get('checkbox_groups');

    // Toggle checkbox group
    if ($this->Input->get('cbc'))
    {
      $state[$this->Input->get('cbc')] = (isset($state[$this->Input->get('cbc')]) && $state[$this->Input->get('cbc')] == 1) ? 0 : 1;
      $this->Session->set('checkbox_groups', $state);

      $this->redirect(preg_replace('/(&(amp;)?|\?)cbc=[^& ]*/i', '', $this->Environment->request));
    }

    $blnFirst = true;
    $blnCheckAll = true;

    foreach ($this->arrOptions as $i=>$arrOption)
    {
      // Single dimension array
      if (is_numeric($i))
      {
              $arrOptions[] = $this->generateCheckbox($arrOption, $i);
              continue;
      }

      $id = 'cbc_' . $this->strId . '_' . standardize($i);

      $img = 'folPlus';
      $display = 'none';

      if ($state[$id] || !is_array($state) || !array_key_exists($id, $state))
      {
              $img = 'folMinus';
              $display = 'block';
      }

      $arrOptions[] = '<div class="checkbox_toggler' . ($blnFirst ? '_first' : '') . '"><a href="' . $this->addToUrl('cbc=' . $id) . '" onclick="AjaxRequest.toggleCheckboxGroup(this, \'' . $id . '\'); Backend.getScrollOffset(); return false;"><img src="system/themes/' . $this->getTheme() . '/images/' . $img . '.gif" alt="toggle checkbox group" /></a>' . $i .	'</div><div id="' . $id . '" class="checkbox_options" style="display:' . $display . ';"><input type="checkbox" id="check_all_' . $id . '" class="tl_checkbox" onclick="Backend.toggleCheckboxGroup(this, \'' . $id . '\')" /> <label for="check_all_' . $id . '" style="color:#a6a6a6;"><em>' . $GLOBALS['TL_LANG']['MSC']['selectAll'] . '</em></label>';

      // Multidimensional array
      foreach ($arrOption as $k=>$v)
      {
        $arrOptions[] = $this->generateCheckbox($v, $i.'_'.$k);
      }

      $arrOptions[] = '</div>';
      $blnFirst = false;
      $blnCheckAll = false;
    }

    // Add a "no entries found" message if there are no options
    if (!count($arrOptions))
    {
      $arrOptions[]= '<p class="tl_noopt">'.$GLOBALS['TL_LANG']['MSC']['noResult'].'</p>';
      $blnCheckAll = false;
    }

    $template = new BackendTemplate( 'be_widget_m2m_items' );
    $template->strId = $this->strId;
    $template->strClasses = 'tl_checkbox_container' . 
                            (strlen($this->strClass) ? ' ' . $this->strClass : '');

    $template->checkAll = $blnCheckAll;
    $template->options  = $arrOptions;

    return $template->parse();
  }


  /**
   * Generate a checkbox and return it as string
   * @param array
   * @param integer
   * @return string
   */
  protected function generateCheckbox($arrOption, $i)
  {
    $template = new BackendTemplate( 'be_widget_m2m_item' );
    $template->strName = $this->strName . '[]';
    $template->strId   = $this->strId . '_' . $i;
    $template->value   = specialchars($arrOption['value']);
    $template->checked = ((is_array($this->varValue) && in_array($arrOption['value'] , $this->varValue) || $this->varValue == $arrOption['value']) ? ' checked="checked"' : '');
    $template->attrs   = $this->getAttributes();
    $template->label   = $arrOption[ 'label' ];

    return $template->parse();
  }
}

