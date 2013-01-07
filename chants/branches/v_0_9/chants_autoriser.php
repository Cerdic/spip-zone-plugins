<?php
// declarer la fonction du pipeline
function chant_autoriser(){}

//
// Autoriser a creer un chant :
// Il faut qu'une rubrique existe et qu'on est le statut necessaire pour creer
//
// @return bool
function autoriser_chant_creer_dist($faire, $type, $id, $qui, $opt) {
	return (sql_countsel('spip_rubriques')>0 AND in_array($qui['statut'], array('0minirezo', '1comite')));
}

// Autoriser a modifier le chant $id
// = publierdans rubrique parente
// = ou statut 'prop,prepa' et $qui est auteur
function autoriser_chant_modifier_dist($faire, $type, $id, $qui, $opt) {
	$r = sql_fetsel("id_rubrique,statut", "spip_chants", "id_chant=".sql_quote($id));

	if (!function_exists('auteurs_chant'))
		include_spip('inc/auth'); // pour auteurs_chant si espace public

	return
		$r
		AND
		(
			autoriser('publierdans', 'rubrique', $r['id_rubrique'], $qui, $opt)
			OR (
				(!isset($opt['statut']) OR $opt['statut']!=='publie')
				AND in_array($qui['statut'], array('0minirezo', '1comite'))
				AND in_array($r['statut'], array('prop','prepa', 'poubelle'))
				AND auteurs_chant($id, "id_auteur=".$qui['id_auteur'])
			)
		);
}

function autoriser_chant_voir_dist($faire, $type, $id, $qui, $opt){
	if ($qui['statut'] == '0minirezo') return true;
	// cas des chants : depend du statut du chant et de l'auteur
	if (isset($opt['statut']))
		$statut = $opt['statut'];
	else {
		if (!$id) return false;
		$statut = sql_getfetsel("statut", "spip_chants", "id_chant=".intval($id));
	}

	return
		// si on est pas auteur du chant,
		// seuls les propose et publies sont visibles
		in_array($statut, array('prop', 'publie'))
		// sinon si on est auteur, on a le droit de le voir, evidemment !
		OR
		($id AND $qui['id_auteur']
		     AND (function_exists('auteurs_chant') OR include_spip('inc/auth'))
		     AND auteurs_chant($id, "id_auteur=".$qui['id_auteur']));
}

function autoriser_rubrique_creerchantdans_menu_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	return autoriser('publierdans','rubrique',_request('id_rubrique'));
}


?>