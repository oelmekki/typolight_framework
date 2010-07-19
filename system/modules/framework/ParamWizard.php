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
 * Class ParamWizard 
 *
 * Provide methods to handle routes static parameters
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki 
 * @package    Controller
 */
class ParamWizard extends Widget
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


        public function __get( $key )
        {
          switch ( $key ) 
          {
            case 'values':
              $values = deserialize( $this->varValue );
              $return = array();

              foreach ( $values as $key => $value ) 
              {
                $return[] = array( 'param' => $key, 'value' => $value );
              }

              return $return;
              break;
            
            default:
              return parent::__get( $key );
              break;
          }
        }


        public function validate()
        {
          parent::validate();

          if ( ! $this->hasErrors )
          {
            $this->varValue = $this->format_values( $this->varValue );
          }
        }


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$arrButtons = array('copy', 'delete');
		$strCommand = 'cmd_' . $this->strField;

                $values = $this->values;

		// Change the order
		if ($this->Input->get($strCommand) && is_numeric($this->Input->get('cid')) && $this->Input->get('id') == $this->currentRecord)
		{
			$this->import('Database');

			switch ($this->Input->get($strCommand))
			{
				case 'copy':
					array_insert($values, $this->Input->get('cid'), array($values[$this->Input->get('cid')]));
					break;

				case 'delete':
					$values = array_delete($values, $this->Input->get('cid'));
					break;
			}

			$this->Database->prepare( "UPDATE " . $this->strTable . " SET " . $this->strField . "=? WHERE id=?" )
                                       ->execute( $this->format_values( $values ), $this->currentRecord );

			$this->redirect(preg_replace('/&(amp;)?cid=[^&]*/i', '', preg_replace('/&(amp;)?' . preg_quote($strCommand, '/') . '=[^&]*/i', '', $this->Environment->request)));
		}

		// Make sure there is at least an empty array
		if ( ! is_array( $values ) or empty( $values ) )
		{
                  $values = array( array( 'param' => '', 'value' => '' ) );
		}

                var_export( $values );
                $template           = new BackendTemplate( 'widget_framework_routes_params' );
                $template->strId    = $this->strId;
                $template->strTable = $this->strTable;
                $template->values   = $values;
                $template->buttons  = $arrButtons;

		return $template->parse();
	}


        public function format_values( $val )
        {
          $values = array();

          foreach ( $val as $set ) 
          {
            $values[ $set[ 'param' ] ] = $set[ 'value' ];
          }

          return serialize( $values );
        }
}

