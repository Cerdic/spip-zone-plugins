<?php
/**
 * Editer une societe (action apres creation/modif de societe)
 *
 * @return array
 */
function action_editer_societe_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_societe n'est pas un nombre, c'est une creation 
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_societe = intval($arg)) {
		if (!$id_societe = i2_societe_action_insert_societe())
			return array(false,_L('echec'));
	}
	
	$err = action_societe_set($id_societe);
	return array($id_societe,$err);
}

/**
 * Mettre a jour une societe
 *
 * @param int $id_societe
 * @return string
 */
function action_societe_set($id_societe){
	$err = '';

	$c = array();
	foreach (array(
		'nom', 'secteur','adresse','ville','code_postal','id_pays','telephone','fax'
	) as $champ)
		$c[$champ] = _request($champ);

	include_spip('inc/modifier');
	i2_societe_revision_societe($id_societe, $c);
	//accesrestreint_revision_zone_objets_lies($id_zone, _request('rubriques'),'rubrique','set');

	return $err;
}

/**
 * Creer une nouvelle societe
 *
 * @return int
 */
function i2_societe_action_insert_societe(){
	//include_spip('inc/autoriser');
	//if (!autoriser('creer','societe'))
	//	return false;
	// nouvel zone
	$id_societe = sql_insertq("spip_societes", array("maj"=>"NOW()"));

	if (!$id_societe){
		spip_log("I2_SOCIETE action : impossible d'ajouter une societe");
		return false;
	} 
	return $id_societe;
}

/**
 * Enregistre la revision d'une société
 *
 * @param int $id_societe
 * @param array $c
 * @return string
 */
function i2_societe_revision_societe($id_societe, $c=false) {

	modifier_contenu('societe', $id_societe,
		array(
			'nonvide' => array('nom' => _T('info_sans_titre')),
		),
		$c);

	return ''; // pas d'erreur
}

/**
 * Supprimer une société
 *
 * @param unknown_type $supp_societe
 * @return unknown
 */
function i2_societe_supprime_societe($id_societe){
	$supp_societe = sql_getfetsel("id_societe", "spip_societes", "id_societe=" . intval($id_societe));
	if (intval($id_societe) AND	intval($id_societe) == intval($supp_societe)){
		// Mettre vide les societes des auteurs correspondantes...
		$auteurs_societe = sql_select('id_auteur','spip_auteurs_elargis','id_societe='.intval($id_societe));
		while($aut = sql_fetch($auteurs_societe)){
			sql_updateq('spip_auteurs_elargis',array('id_societe'=>''),'id_auteur='.intval($aut['id_auteur']));
		}
		// puis la societe
		sql_delete("spip_societes", "id_societe=".intval($id_societe));
	}
	$id_societe = 0;
	return $id_societe;
}


?>