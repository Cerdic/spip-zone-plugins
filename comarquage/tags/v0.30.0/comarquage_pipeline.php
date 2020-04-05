<?php

/* Plugin Comarquage -flux V2- pour SPIP 1.9
 * Copyright (C) 2006 Cedric Morin
 * Copyright (C) 2010 Vernalis Interactive
 *
 * Licence GPL
 *
 */

function comarquage_taches_generales_cron($taches_generales){
	$taches_generales['comarquage_update_xml'] = 60*60; // mettre a jour une fois par heure
	return $taches_generales;
}

// insertion de la feuille de style utilisé par le comarquage
function comarquage_insert_head($flux) {
	$flux .= '<link rel="stylesheet" href="'.find_in_path('comarquage.css').'" media="all" />';
	return $flux;
}

?>