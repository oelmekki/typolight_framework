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
 * Back end modules
 */
$GLOBALS['TL_LANG']['MOD']['routes'] = array( 'Routes', 'Utilisez ce module pour définir des routes.' );
$GLOBALS['TL_LANG']['MOD']['hardRoutes'] = array( 'Liste des routes en dur', 'Ce module présente une liste des routes définies dans la configuration des modules.' );


/**
 * Front end modules
 */
$GLOBALS['TL_LANG']['FMD']['messages']          = array('Messages', 'Montre les messages passés par l\'action précédente');
$GLOBALS['TL_LANG']['FMD']['routedBreadcrumb']  = array('File d\'ariane routé', 'Comme le fil d\'ariane classique, mais inclu l\'action pour les FrontendControllers');
$GLOBALS['TL_LANG']['FMD']['routedNav']         = array('Navigation routée', 'Permet de faire une navigation en utilisant des routes');
