<?php

/*
 *  Plugin Bouquinerie pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

// Compatibilites
if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) @define('_SPIP19300', 1);
if (version_compare($GLOBALS['spip_version_code'],'1.9200','>=')) @define('_SPIP19200', 1);
else @define('_SPIP19100', 1);

// Compatibilite 1.9.2
if (version_compare($GLOBALS['spip_version_code'],'1.9300','<'))
  include_spip('inc/compat_bouq');

// Declaration des tables
include_spip('base/bouq');

define('_SPIP_PAGE','page'); 


/*// Droits pour la bouquinerie
function bouq_autoriser() {
  return function_exists('autoriser')
    ?autoriser('configurer', 'plugins')
    :$GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"];
}*/

// Logs de tmp/spip.log
function bouq_log($variable, $prefixe='', $stat='') {
  static $rand;
  if($stat) $rand = $stat;
  if((!defined('_LOG_BOUQ') && !defined('_BOUQ_REPORTALL')) || !strlen($variable)) return;
  if (!is_string($variable)) $variable = var_export($variable, true);
  spip_log($variable = $rand.$prefixe.$variable);
  if (defined('_BOUQ_REPORTALL')) echo '<br/>',htmlentities($variable);
}


?>
