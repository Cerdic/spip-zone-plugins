<?php

include_spip("inc/commun");


// ajoute une tache dans la cron liste
function ha_taches_generales_cron($taches_generales)
{
	// tache cron de sauvegarde de la page, toutes les heures
	// si la page existe deja pour la date, on ecrase.
	$taches_generales['histo_archivage'] = 3600; //3600*24;
	return $taches_generales;  
}


function ha_ajbouton($boutons_admin) 
{
  	global $connect_statut, $connect_toutes_rubriques;
  	if (($connect_statut != '0minirezo') OR !$connect_toutes_rubriques)
		return $boutons_admin;

	// on voit les bouton dans la barre "edition"
	$boutons_admin['naviguer']->sousmenu["ha"]= new Bouton(
		_DIR_PLUGIN_HA."/imgs/tag.png",  // icone
		_L('ha:titre') //titre
		);
	return $boutons_admin;
}

function ha_headerprive($head)
{
	$head .= '<link rel="stylesheet" type="text/css" href="'._URL_PLUGIN_HA.'/css/ha.css" />';
	return $head;
}

/*
function ha_ajonglet($flux) 
{
	if($flux['args']=='configuration')
		$flux['data']['mots_partout']= new Bouton(
			"../"._DIR_PLUGIN_HISTOAGENDA."/imgs/tag.png", _L('ha:config'),
			generer_url_ecrire("config_mots_partout"));
	return $flux;
}
*/

?>