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
 * @license    LGPL
 * @copyright  Leo Feyer 2005
 * @author     Leo Feyer <leo@typolight.org>
 * @package    Frontend
 * @license    LGPL
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['routedNav'] = 'name,type,headline;routes;align,space,cssID;';

$GLOBALS['TL_DCA']['tl_module']['fields']['routes']  = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_module']['routes'],
  'exclude'                 => true,
  'inputType'               => 'routesWizard',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['defaultRoutedAction']  = array(
  'label'                   => &$GLOBALS['TL_LANG']['tl_module']['defaultRoutedAction'],
  'exclude'                 => true,
  'inputType'               => 'select',
  'options_callback'        => array( 'framework_tl_module', 'getActions' ),
  'eval'                    => array( 'submitOnChange' => true ),
);

class framework_tl_module extends Backend
{
  public function getActions( $dca )
  {
    $record = $this->Database->prepare( 'select * from tl_module where id = ?' )
                             ->execute( $dca->id );

    if ( $record->next() )
    {
      $module = $record->row();
      $type = $module[ 'type' ];

      foreach ( $GLOBALS[ 'FE_MOD' ] as $package => $modules )
      {
        if ( strlen( $modules[ $type ] ) )
        {
          $class = $modules[ $type ];
          $instance = new $class( $record );
          $options = $instance->actions;
          break;
        }
      }
    }


    return $options;
  }
}

