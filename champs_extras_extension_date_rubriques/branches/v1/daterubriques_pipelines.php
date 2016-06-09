<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function daterubriques_pre_edition($flux){

	// recuperer les champs crees par les plugins
	if ($extras = cextras_get_extras_match($flux['args']['table'])) {

		foreach ($extras as $c) {
			if ( $c->champ == "date_utile" ) {
				//On met la date saisie au format MySql AAAA-MM-JJ
				if ($date = recup_date($flux['data']['date_utile'])) {	
					$flux['data']['date_utile'] = date("Y-m-d 00:00:00",mktime($date[3],$date[4],0,$date[1],$date[2],$date[0]));
				} 
			}
		}
	}
	return $flux;
}


?>