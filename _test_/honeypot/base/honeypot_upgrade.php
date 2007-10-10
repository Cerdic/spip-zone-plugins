<?php

/*
 *   Plugin HoneyPot
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


function honeypot_droptables() {
  spip_query("DROP TABLE IF EXISTS spip_honeypot_cache, spip_honeypot_stats;");
}

function honeypot_doupgrade() {
  include_spip('base/honeypot_db');
  $installe = $GLOBALS['meta']['honeypot:installe'];
  $uptodate = $installe && ($installe == $GLOBALS['honeypotdb_version']);
  if(!$uptodate) {
	honeypot_droptables();
	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();
	ecrire_meta('honeypot:installe',$GLOBALS['honeypotdb_version']);
	ecrire_metas();
  }
}

function honeypot_install($action){
  switch ($action){
	case 'test':
	  include_spip('base/honeypot_db');
	  //Contrôle du plugin à chaque chargement de la page d'administration
	  $installe = $GLOBALS['meta']['honeypot:installe'];
	  $uptodate = $installe && ($installe == $GLOBALS['honeypotdb_version']);
	  return $uptodate;
	  break;
	case 'install':
	  honeypot_doupgrade();
	  break;
	case 'uninstall':
	  //Appel de la fonction de suppression
	  honeypot_droptables();
	  effacer_meta('honeypot:installe');
	  ecrire_metas();
	  break;
  }
}

?>
