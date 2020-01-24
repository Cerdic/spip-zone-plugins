<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Enregistrer la date d'inscription lors de l'insertion d'un auteur en base
 *
 * @param array $flux
 * @return array
 */
function date_inscription_pre_insertion($flux){
	if ($flux['args']['table']=='spip_auteurs'){
		$flux['data']['date_inscription'] = date('Y-m-d H:i:s');
	}
	return $flux;
}

/**
 * Afficher la date d'inscription sur la fiche de l'auteur
 * @param array $flux 
 */
function date_inscription_afficher_contenu_objet($flux){
	if ($flux['args']['type']=='auteur'
		AND $id_auteur = $flux['args']['id_objet']
		AND $date_inscription = sql_getfetsel('date_inscription','spip_auteurs','id_auteur='.intval($id_auteur))
	){
		$date_inscription = ($date_inscription == '0000-00-00 00:00:00') ? _T('date_inscription:non_renseignee') : affdate($date_inscription);
		$flux['data'] .= "<div class='champ afficher afficher_date_inscription'>"
			. "<strong class='label'>" . _T('date_inscription:date_inscription') . " : </strong>"
			. "<div class='valeur'>" . propre($date_inscription) . "</div></div>";
	}
	return $flux;
}
