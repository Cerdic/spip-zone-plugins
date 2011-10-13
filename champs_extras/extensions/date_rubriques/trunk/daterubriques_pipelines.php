<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function daterubriques_pre_edition($flux){

	// recuperer les champs crees par les plugins
	if ($extras = cextras_get_extras_match($flux['args']['table'])) {
		// recherchons un eventuel prefixe utilise pour poster les champs
		$type = objet_type(table_objet($flux['args']['table']));
		$prefixe = _request('prefixe_champs_extras_' . $type);
		if (!$prefixe) {
			$prefixe = '';
		}
		foreach ($extras as $c) {
			if ( $c->champ == "date_utile" ) {
				//On met la date saisie au format MySql AAAA-MM-JJ
				if ($date = recup_date($flux['data']['date_utile'])) {				
					$flux['data']['date_utile'] = date("Y-m-d",mktime($date[3],$date[4],0,$date[1],$date[2],$date[0]));
				} else {
					$flux['data']['date_utile'] = date("Y-m-d");
				}
			}
		}
	}
	return $flux;
}


?>