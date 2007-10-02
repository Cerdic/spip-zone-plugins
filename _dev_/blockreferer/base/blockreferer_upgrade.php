<?php

/*
 *   Plugin Blockreferer
 *   Copyright (C) 2007 Pierre Andrews
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


function blockreferer_droptables() {
  spip_query("DROP TABLE IF EXISTS ".$GLOBALS['table_prefix']."_blockreferer_blacklist;");
}

function blockreferer_doupgrade() {
  include_spip('base/blockreferer_db');
  $installe = $GLOBALS['meta']['blockreferer:installe'];
   $uptodate = $installe && ($installe == $GLOBALS['blockrefererdb_version']);
  if(!$uptodate) {
	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();
	ecrire_meta('blockreferer:installe',$GLOBALS['blockrefererdb_version']);
	ecrire_metas();
  }
}

function blockreferer_install($action){
  switch ($action){
	case 'test':
	  include_spip('base/blockreferer_db');
	  //Contrôle du plugin à chaque chargement de la page d'administration
	  $installe = $GLOBALS['meta']['blockreferer:installe'];
	  $uptodate = $installe && ($installe == $GLOBALS['blockrefererdb_version']);
	  return $uptodate;
	  break;
	case 'install':
	  blockreferer_doupgrade();
	  break;
	case 'uninstall':
	  //Appel de la fonction de suppression
	  blockreferer_droptables();
	  effacer_meta('blockreferer:installe');
	  ecrire_metas();
	  break;
  }
}

?>
