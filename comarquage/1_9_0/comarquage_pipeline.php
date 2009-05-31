<?php

/*
 * Copyright (C) 2006 Cedric Morin
 * Licence GPL
 *
 * Plugin SPIP 1.9 (c) 2006 par Notre-ville.net
 * Web : http://www.notre-ville.net
 * Cedric MORIN (cedric.morin@notre-ville.net)
 *
 */

function comarquage_taches_generales_cron($taches_generales){
	$taches_generales['comarquage_update_xml'] = 600; // mettre a jour une fois par heure
	return $taches_generales;
}

?>