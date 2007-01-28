<?php

/*
	Balise #MINICAL_ALTER
	Auteur James (c) 2006
	Plugin pour spip 1.9.1
	Licence GNU/GPL
*/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_MINICAL_ALTER($p) {
	return calculer_balise_dynamique($p,'MINICAL_ALTER', array(VAR_DATE_CAL, 'id_rubrique','id_article', 'id_mot'));
}
 
function balise_MINICAL_ALTER_stat($args, $filtres) {
 //les parametres passe en {...}, les filtres sont des vraiss filtres
	return $args;
}
 
function balise_MINICAL_ALTER_dyn($date, $id_rubrique = 0, $id_article = 0, $id_mot = 0, $url = '') {
	/* tenir compte de la langue, c'est pas de la tarte */
	return array('formulaires/minical_alternatives', 3600, 
		array(
			'date' => $date?$date:date('Y-m'),
			'var_date' => VAR_DATE_CAL,
			'self' => $url?$url:self(),
			'id_rubrique' => $id_rubrique,
			'id_article' => $id_article,
			'id_mot' => $id_mot
		));
}

?>