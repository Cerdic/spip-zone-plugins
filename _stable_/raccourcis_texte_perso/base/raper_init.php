<?php

// raper_init.php

	/*****************************************************
	Copyright (C) 2009 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of RaPer.
	
	RaPer is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	RaPer is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with RaPer; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de RaPer. 
	
	RaPer est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	RaPer est distribue' car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spe'cifique. Reportez-vous a' la Licence Publique Ge'ne'rale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a' la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$


include_spip('inc/raper_api_globales');

function raper_install ($action) {

	switch($action) {
		case 'test':
		// si renvoie true, c'est que la base est a jour, inutile de re-installer
		// la valise plugin "effacer tout" apparait.
			$result = isset($GLOBALS['meta'][_RAPER_META_PREFS]);
			raper_log("TEST " . _RAPER_PREFIX . " : " . ($result ? "TRUE" : "FALSE"));
			return($result);
			break;
		case 'install':
		// inutile. Les prefs sont enregistres par appel pipeline.
		// Laisse' ici pour exemple traditionnel.
			$prefs = raper_lire_preferences(true);
			$result = isset($GLOBALS['meta'][_RAPER_META_PREFS]);
			raper_log("INSTALL " . _RAPER_PREFIX . " : " . ($result ? "OK" : "ERROR"));
			return($result);
			break;
		case 'uninstall':
		// est appelle lorsque "Effacer tout" dans exec=admin_plugin
			// effacer les prefs raccourcis
			raper_effacer_fichiers_raper_local();
			// effacer le meta
			include_spip('base/abstract_sql');
			// $sql_query = "DELETE FROM spip_meta WHERE nom='"._RAPER_META_PREFS."' LIMIT 1"
			$result = sql_delete('spip_meta', "nom=" . sql_quote(_RAPER_META_PREFS) . " LIMIT 1");
			unset($GLOBALS['meta'][_RAPER_META_PREFS]);
			raper_ecrire_metas();
			raper_log("UNINSTALL " . _RAPER_META_PREFS . " : " . ($result ? "OK" : "ERROR"));
			return($result);
	}
}

?>