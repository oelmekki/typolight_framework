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
 * @package    Language
 * @license    LGPL 
 * @filesource
 */


/**
 * Miscellaneous
 */
$GLOBALS['TL_LANG']['MSC']['addNewFile'] = 'Ajouter un nouveau fichier';

$GLOBALS['TL_LANG']['MSC']['framework'] = array(
  'routeName'   => 'Nom de la route',
  'routeParams' => 'Paramètres de la route',
  'altName'     => 'nom alternatif "propre"',
  'copy'        => 'Dupliquer',
  'up'          => 'Monter',
  'down'        => 'Descendre',
  'delete'      => 'Effacer',
);


$GLOBALS['TL_LANG']['MSC']['framework_pagination'] = array(
  'previous'  => 'Précédent',
  'next'      => 'Suivant',
);


$GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'HardRoutesList' ] = array(
  'noRoute'             => 'Pas de routes pour l\instant',
  'inDatabase'          => 'présente dans la base de donnée',
  'none'                => 'Aucun',
  'definition'          => 'Définition',
  'resolveTo'           => 'Dirige vers',
  'staticParams'        => 'Paramètres statiques',
  'method'              => 'Quelle method correspond à cette route?',
  'true'                => 'Oui',
  'false'               => 'Non',
  'loadInDb'            => 'charger dans la base de donnée',
  'loadAll'             => 'Charger toutes les routes dans la base de donnée',
  'toggle-show'         => 'Voir',
  'toggle-hide'         => 'Cacher',
  'route_loaded'        => 'La route a été chargée dans la base de donnée',
  'route_not_loaded'    => 'La route n\'a pu être chargée dans la base de donnée',
  'routes_loaded'       => 'Les routes ont été chargées dans la base de donnée',
  'routes_not_loaded'   => 'Les routes n\'ont pu être chargées dans la base de donnée',
);


$GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'EModel' ] = array(
  'validates_presence_of'     => '%s doit être présent',
  'validates_uniqueness_of'   => '"%s" est déjà pris',
  'validates_format_of'       => '%s n\'est pas formaté comme attendu',
  'validates_numericality_of' => '%s doit être numérique',
  'validates_min_length_of'   => '%s doit compter au moins %s lettres',
  'validates_max_length_of'   => '%s doit compter au plus %s lettres',
  'validates_associated'      => '%s doit être associé à %s',
);

$GLOBALS[ 'TL_LANG' ][ 'MSC' ][ 'upload_errors' ] = array( 
  UPLOAD_ERR_INI_SIZE => 'Le fichier téléchargé excède la taille autorisée par le serveur',
  UPLOAD_ERR_FORM_SIZE => 'Le fichier téléchargé excède la taille autorisée par l\'application',
  UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement téléchargé',
  UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été téléchargé',
  UPLOAD_ERR_NO_TMP_DIR => 'Un dossier temporaire est manquant',
  UPLOAD_ERR_CANT_WRITE => 'Échec de l\'écriture du fichier sur le disque',
  UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrété l\'envoi de fichier',
);
