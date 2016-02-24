<?php
include_spip('inc/config');

function secteur_explicite($crit) {
	foreach($crit as $critere){
		if (
			!empty($critere->param[0][0]->texte) 
			and $critere->param[0][0]->texte == 'id_secteur' 
			and $critere->not != '!'
		) {
			switch ($critere->op){
				case '=' :
					return true;
				case '==':
					return true;
				case 'IN':
					return true;
			}
		}
	}
	return false;
}

function id_explicite($crit, $type) {
	//test de cfg
	$cfg = lire_config('secteur/idexplicite');


	if ($cfg == false) {
		return false;
	}
	$id = 'id_'.substr($type, 0, -1);


	foreach($crit as $critere) {
		if (
			!empty($critere->param[0][0]->texte)
			and $critere->param[0][0]->texte == $id
			and $critere->not!='!'
		) {
			switch ($critere->op){
				case '=' :
					return true;
				case '==':
					return true;
				case 'IN':
					return true;
			}
		}
		if ($critere->op == $id and $critere->not != '!') {
			return true;
		}
	}
	return false;

}

function exclure_sect_choisir($crit, $type) {
	$cfg =lire_config('secteur/exclure_sect');
	if ($cfg == null) {
		$cfg = array();
	}
	$sect_afficher = secteur_explicite($crit);
	$id_explicite = id_explicite($crit,$type); //l'id de la table sur laquelle on boucle est-il explicite ?
	if ($cfg = array_map('sql_quote', $cfg) and !$sect_afficher and !$id_explicite) {
		$cfg = implode($cfg, ',');
	}
	else {
		$cfg = 'z';
	}

	return $cfg;
}
