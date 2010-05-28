<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

if (!defined('_DIR_PLUGIN_BANDO')) {
	function exec_calendrier(){
		$mode = _request('mode');
		// si $echelle dans l'url, c'est une page du mode editorial aussi
		if (($mode == 'editorial') or _request('echelle')){
			include_spip('exec/calendrier');
			exec_calendrier_dist();
		}
		else {
			$var_f = charger_fonction('agenda_evenements');
			$var_f();
		}
	}
}

?>