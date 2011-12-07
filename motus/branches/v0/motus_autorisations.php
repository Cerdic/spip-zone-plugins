<?php

function motus_autoriser(){}



function autoriser_rubrique_editermots($faire,$quoi,$id,$qui,$opts){

	// premier tri
	if (!autoriser_rubrique_editermots_dist($faire,$quoi,0,$qui,$opts))
		return false;

	// si restriction a une rubrique...
	$row = $opts['groupe_champs'];
	if (isset($row['rubriques_on']) and $rubs = $row['rubriques_on']) {
		include_spip('spip_bonux_fonctions');
		if ($rubs = picker_selected($rubs,'rubrique')) {
			
			// trouver la rubrique de l'objet en question
			if ($quoi != 'rubrique') {
				$id_rub = sql_getfetsel('id_rubrique',table_objet_sql($quoi), id_table_objet($quoi) . '=' . sql_quote($id));
			} else {
				$id_rub = $id;
			}

			$opts['rubriques_on'] = $rubs;
			return autoriser('dansrubrique','groupemots',$id_rub,$qui,$opts);
		}
	}
	return true;
}


function autoriser_groupemots_dansrubrique_dist($faire,$quoi,$id,$qui,$opts){
	static $rubriques = -1;

	// init
	if ($rubriques === -1) $rubriques = array();
	

	if (!$rubs = $opts['rubriques_on']  // pas de liste de rubriques ?
	or !$id  // pas d'info de rubrique... on autorise par defaut...
	or in_array($id, $rubs)) // la rubrique est dedans
		return true;
	
	// la ca se complique...
	// si deja calcule... on le retourne.
	$hash = md5(implode('',$rubs) . '_' . $opts['id_groupe']);
	if (isset($rubriques[$id][$hash]))
		return $rubriques[$id][$hash];
	
	// remonter recursivement les rubriques...
	$id_parent = sql_getfetsel('id_parent','spip_rubriques', 'id_rubrique = '. sql_quote($id));

	// si racine... pas de chance
	if (!$id_parent) {
		$rubriques[$id][$hash] = false;
	} else {
		$rubriques[$id][$hash] = autoriser('dansrubrique','groupemots',$id_parent,$qui,$opts);
	}

	return $rubriques[$id][$hash];
}


function autoriser_article_editermots($faire,$quoi,$id,$qui,$opts){
	return autoriser_rubrique_editermots($faire,$quoi,$id,$qui,$opts);
}

function autoriser_breve_editermots($faire,$quoi,$id,$qui,$opts){
	return autoriser_rubrique_editermots($faire,$quoi,$id,$qui,$opts);
}

function autoriser_syndic_editermots($faire,$quoi,$id,$qui,$opts){
	return autoriser_rubrique_editermots($faire,$quoi,$id,$qui,$opts);
}


?>
