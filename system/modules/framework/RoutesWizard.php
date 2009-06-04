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
 * Class RoutesWizard 
 *
 * Widget to choose among existing routes
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki 
 * @package    Widget
 */
class RoutesWizard extends Widget
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
  protected $strTemplate = 'be_widget';



  /**
   * Add specific attributes
   * @param string
   * @param mixed
   */
  public function __set($strKey, $varValue)
  {
    switch ($strKey)
    {
      case 'value':
        $this->varValue = deserialize($varValue);
        break;

      case 'mandatory':
        $this->arrConfiguration['mandatory'] = $varValue ? true : false;
        break;

      default:
        parent::__set($strKey, $varValue);
        break;
    }
  }



  /**
   * Validate input and set value
   */
  public function validate()
  {
    $mandatory = $this->mandatory;
    $options = deserialize($this->getPost($this->strName));

    if (is_array($options))
    {
      foreach ($options as $key=>$option)
      {
        $options[$key]['routeName'] = trim($option['routeName']);
        $options[$key]['params'] = trim($option['params']);
        $options[$key]['altName'] = trim($option['altName']);

        if (strlen($options[$key]['routeName']) and strlen($options[$key]['altName']))
        {
          $this->mandatory = false;
        }
      }
    }

    $varInput = $this->validator($options);

    if (!$this->hasErrors())
    {
      $this->varValue = $varInput;
    }

    // Reset the property
    if ($mandatory)
    {
      $this->mandatory = true;
    }
  }



  /**
   * Generate the widget and return it as string
   * @return string
   */
  public function generate()
  {
    $strCommand = 'cmd_' . $this->strField;

    // Change the order
    if ($this->Input->get($strCommand) && is_numeric($this->Input->get('cid')) && $this->Input->get('id') == $this->currentRecord)
    {
      $this->import('Database');

      switch ($this->Input->get($strCommand))
      {
        case 'copy':
          array_insert($this->varValue, $this->Input->get('cid'), array($this->varValue[$this->Input->get('cid')]));
          break;

        case 'up':
          $this->varValue = array_move_up($this->varValue, $this->Input->get('cid'));
          break;

        case 'down':
          $this->varValue = array_move_down($this->varValue, $this->Input->get('cid'));
          break;

        case 'delete':
          $this->varValue = array_delete($this->varValue, $this->Input->get('cid'));
          break;
      }

      $this->Database->prepare("UPDATE " . $this->strTable . " SET " . $this->strField . "=? WHERE id=?")
                                 ->execute(serialize($this->varValue), $this->currentRecord);

      $this->redirect(preg_replace('/&(amp;)?cid=[^&]*/i', '', preg_replace('/&(amp;)?' . preg_quote($strCommand, '/') . '=[^&]*/i', '', $this->Environment->request)));
    }

    // Make sure there is at least an empty array
    if (!is_array($this->varValue) || !$this->varValue[0])
    {
      $this->varValue = array(array(''));
    }

    $template = new BackendTemplate( 'be_framework_routes_wizard' );
    $template->values = $this->varValue;
    $template->strId = $this->strId;
    $template->strCommand = $strCommand;
    $template->currentRecord = $this->currentRecord;
    $template->buttons = array('copy', 'up', 'down', 'delete');

    $route = new Route();
    $template->allRoutes = $route->all;

    return $template->parse();
  }
}
