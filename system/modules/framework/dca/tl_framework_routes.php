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
 * Table tl_framework_routes 
 */
$GLOBALS['TL_DCA']['tl_framework_routes'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 5,
		),
		'label' => array
		(
			'fields'                  => array('name', 'route'),
			'format'                  => '%s - %s'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_framework_routes']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_framework_routes']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_framework_routes']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_framework_routes']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
                        'cut' => array
                        (
                                'label'               => &$GLOBALS['TL_LANG']['tl_page']['cut'],
                                'href'                => 'act=paste&amp;mode=cut',
                                'icon'                => 'cut.gif',
                                'attributes'          => 'onclick="Backend.getScrollOffset();"',
                        ),
		)
	),

	// Palettes
	'palettes' => array
	(
                '__selector__'                => array('addStatic'),
		'default'                     => 'name;route,method;resolveTo,addStatic;'
	),

        // Subpalettes
        'subpalettes' => array
        (
                'addStatic'                   => 'staticParams'
        ),

	// Fields
	'fields' => array
	(
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_framework_routes']['name'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255)
		),
		'route' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_framework_routes']['route'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('mandatory'=>true, 'maxlength'=>255)
		),
		'resolveTo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_framework_routes']['resolveTo'],
			'exclude'                 => true,
			'inputType'               => 'pageTree',
			'eval'                    => array('mandatory'=>true, 'fieldType'=>'radio')
		),
		'addStatic' => array
		(
                        'label'                   => &$GLOBALS['TL_LANG']['tl_framework_routes']['addStatic'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange'=>true)
		),
		'staticParams' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_framework_routes']['staticParams'],
			'exclude'                 => true,
			'inputType'               => 'paramWizard',
                        'options'                  => array()
		),
		'method' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_framework_routes']['method'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array( 'GET', 'POST', 'GET/POST'),
		),
	)
);

