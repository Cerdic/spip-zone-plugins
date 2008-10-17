<?php

// -> dans inc/autoriser.php
// -----------------------------
//
// Recapitulons nos autorisations
//
// - pour les mots d'un groupe
// * mot-voir => voirmots
// * mot-creer => creermots
// * mot-modifier => modifiermots
// * mot-lier => liermots
//
// - pour un groupe
// * voir
// * creer
// * modifier
//
function autoriser_groupemots($faire, $type, $id, $qui, $opt) {

	static $auth = array();
	// deja calcule ? retour.
	if (isset($auth[$faire][$id][$auteur = $qui['id_auteur']][$sopt = serialize($opt)])) {
		return $auth[$faire][$id][$auteur][$sopt];
	}

	if (!isset($auth[$faire])) 					$auth[$faire] = array();
	if (!isset($auth[$faire][$auteur])) 		$auth[$faire][$auteur] = array();
	if (!isset($auth[$faire][$auteur][$sopt])) 	$auth[$faire][$auteur][$sopt] = array();

	// si pas de groupe, admin uniquement :
	if (!$r = sql_fetsel("*", "spip_groupes_mots", "id_groupe=".sql_quote($id))){
		$autoriser = ($qui['statut'] == '0minirezo' AND !$qui['restreint']);
	}
	
	if ($faire == 'voir' 
		OR $faire == 'voirmots') {
			$autoriser = true;
	}	
	if ($faire == 'creer' 
		OR $faire == 'modifier') {
			$autoriser = ($qui['statut'] == '0minirezo' AND !$qui['restreint']);
	}
	if ($faire == 'liermots'		
		OR $faire == 'creermots' 
		OR $faire == 'modifiermots'){
		// chercher le champ 'minirezo', 'comite' ou 'forum'
		if ($r)
			$autoriser = ($r[substr($qui['statut'],1)]=='oui');
	}
		
	$pipe = pipeline('autoriser_groupemots',
			array('args' => array(
				'faire'=>$faire,
				'type'=>$type,
				'id'=>$id,
				'qui'=>$qui,
				'opt'=>$opt,
				'technique' => $r['technique'],
				'row' => $r
				),
			'autoriser' => $autoriser
			)
		);	
	return $auth[$faire][$id][$auteur][$sopt] = $pipe['autoriser'];
}


// Autoriser a modifier un mot $id ; note : si on passe l'id_groupe
// dans les options, on gagne du CPU (c'est ce que fait l'espace prive)
// 
// CHGMT : 'modifier' > 'modifiermots'
function autoriser_mot_modifier($faire, $type, $id, $qui, $opt) {
	// id groupe mot present
	if (isset($opt['id_groupe']) AND $opt['id_groupe']){
		return autoriser('modifiermots', 'groupemots', $opt['id_groupe'], $qui, $opt);
	}
	// id mot present, on retrouve le groupe
	if ($id && ($t = sql_getfetsel("id_groupe", "spip_mots", "id_mot=".sql_quote($id)))){
		return autoriser('modifiermots', 'groupemots', $t, $qui, $opt);
	}
	// sinon defaut
	return autoriser('modifiermots', 'groupemots');
}

// Autoriser a voir un mot $id ; note : si on passe l'id_groupe
// dans les options, on gagne du CPU (c'est ce que fait l'espace prive)
function autoriser_mot_voir($faire, $type, $id, $qui, $opt) {
	// id groupe mot present
	if (isset($opt['id_groupe']) AND $opt['id_groupe']){
		return autoriser('voirmots', 'groupemots', $opt['id_groupe'], $qui, $opt);
	}
	// id mot present, on retrouve le groupe
	if ($id && ($t = sql_getfetsel("id_groupe", "spip_mots", "id_mot=".sql_quote($id)))){
		return autoriser('voirmots', 'groupemots', $t, $qui, $opt);
	}
	// sinon defaut
	return autoriser('voirmots', 'groupemots');
}

?>
