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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_framework_routes']['name'] = array('Nom de la route', "Déterminez ici le nom qu'aura la route. Vous pourrez générer automatiquement le chemin de cette route en utilisant ce nom, n'importe où, avec la fonction: Route::compose( nom ).");
$GLOBALS['TL_LANG']['tl_framework_routes']['route'] = array('Définition de la route', "Indiquez le chemin qui doit résoudre cette route. Vous pouvez préciser les paramètres en les précédent de ':'. Exemple: '/blog/page/:id/comments/:comment_id/delete'");
$GLOBALS['TL_LANG']['tl_framework_routes']['resolveTo'] = array('Page de destination', 'Indiquez ici la page vers laquelle la route résolue pointe.');
$GLOBALS['TL_LANG']['tl_framework_routes']['addStatic'] = array('Ajouter des paramètres GET statiques', "Vous pouvez ajouter des paramètres qui seront passés en GET à la page de destination systématique quand cette route sera résolue.");
$GLOBALS['TL_LANG']['tl_framework_routes']['staticParams'] = array('Paramètres', 'Entrez le nom du paramètre et sa valeur pour chaque paramètre que vous voulez ajouter.');
$GLOBALS['TL_LANG']['tl_framework_routes']['POSTroute'] = array('Route en POST', 'Si vous cochez cette case, cette route ne sera valable que s\'il existe au moins un paramètre POST. Cela vous permet de router la même url vers différentes pages.');


/**
 * Reference
 */
$GLOBALS['TL_LANG']['tl_framework_routes']['routeParam'] = 'Nom du paramètre';
$GLOBALS['TL_LANG']['tl_framework_routes']['routeValue'] = 'Valeur';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_framework_routes']['new']    = array('Nouvelle route', 'Nouvelle');
$GLOBALS['TL_LANG']['tl_framework_routes']['edit']   = array('Modifier', 'Modifier');
$GLOBALS['TL_LANG']['tl_framework_routes']['copy']   = array('Dupliquer', 'Dupliquer');
$GLOBALS['TL_LANG']['tl_framework_routes']['delete'] = array('Effacer', 'Effacer');
$GLOBALS['TL_LANG']['tl_framework_routes']['show']   = array('Voir', 'Voir');

?>
