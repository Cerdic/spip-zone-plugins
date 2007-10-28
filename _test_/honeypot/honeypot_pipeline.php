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

/*ajoute un style pour cacher les pieges aux visiteurs*/
function honeypot_insert_head($flux) {
  $flux .= 	'<style type="text/css">
.pluginhp'.lire_config('honeypot/hpfile').' {
display: none;
}
</style>';
  return $flux;
}


function honeypot_ajouter_boutons($flux){
	if ( $GLOBALS['connect_statut'] == "0minirezo" && (lire_config('honeypot/httpbl/stats') == 'on')){
		$flux['statistiques_visites']->sousmenu['honeypot_statistiques']= new Bouton(find_in_path('honeypot_24.png'),_T('honeypothttpbl:stat_bouton'));
	}
    
	return $flux;
}


/*Les onglets pour les stats, n'afficher que les onglets des filtres utilisés*/
function honeypot_ajouter_onglets($flux) {
  if($flux['args']=='honeypot_statistiques') {
	$flux['data']['general']= new Bouton('', _T('honeypothttpbl:stat_gen'),
										 generer_url_ecrire("honeypot_statistiques"));
	$result = spip_query("SELECT DISTINCT filtre FROM spip_honeypot_stats");
	while($row = spip_fetch_array($result)) {
	  $flux['data']['filtre'.$row['filtre']]= new Bouton('', _T('honeypothttpbl:stat_filtre'.$row['filtre']),
										   generer_url_ecrire("honeypot_statistiques","filtre=".$row['filtre']));
	}
  }
  return $flux;
}

/*on ajoute la tache au cron*/
function honeypot_taches_generales_cron($taches) {
  $taches['httpbl_cron'] = 3600*24*lire_config('honeypot/httpbl/cache',7);
  return $taches;
}

?>