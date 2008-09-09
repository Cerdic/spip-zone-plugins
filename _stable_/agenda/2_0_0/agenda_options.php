<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function exec_calendrier(){
	$mode = _request('mode');
	if ($mode=='editorial'){
	  include_spip('exec/calendrier');
	  exec_calendrier_dist();
	}
	else {
		$var_f = charger_fonction('agenda_evenements');
		$var_f();
	}
}
?>