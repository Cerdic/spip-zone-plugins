<?php
/**
 * SPIP-Lettres
 *
 * Copyright (c) 2006-2009
 * Agence Artégo http://www.artego.fr
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
 *
 **/


function lettres_autoriser() {}


function autoriser_lettres_dist($faire, $type, $id, $qui, $opt) {
	switch ($faire) {
		case 'bouton':
		case 'onglet':
		case 'configurer':
		case 'voir':
		case 'exporterabonnes':
		case 'exporterdesabonnes':
		case 'importerabonnes':
		case 'purger':
		case 'joindre':
		case 'editer':
			return ($qui['statut'] == '0minirezo');
			break;
		default:
			return false;
			break;
	}
}


/**
 * Creer une nouvelle lettre : admin ou admins restreints dans leurs rubriques
 */
function autoriser_rubrique_creerlettredans($faire,$quoi,$id,$qui,$opts){
	return autoriser('publierdans','rubrique',$id);
}

function autoriser_lettre_editermots_dist($faire,$quoi,$id,$qui,$opts){
	return autoriser_rubrique_editermots_dist($faire,'lettre',0,$qui,$opts);
}

function autoriser_lettre_modifier_dist($faire, $type, $id, $qui, $opt){
	$statut = sql_getfetsel('statut', 'spip_lettres', 'id_lettre='.intval($id));
	if ($statut=='brouillon')
		return autoriser('editer','lettres');
	// cas des crayons et du controleur message
	// on autorise les webmestre !
	if (isset($opt['champ']) AND $opt['champ']=='message')
		return autoriser('webmestre');
	return false;
}

function autoriser_lettre_instituer_dist($faire, $type, $id, $qui, $opt){
	$id_rubrique = sql_getfetsel('id_rubrique', 'spip_lettres', 'id_lettre='.intval($id));
	return autoriser('publierdans','rubrique',$id_rubrique);
}

function autoriser_lettre_tester_dist($faire, $type, $id, $qui, $opt){
	$statut = sql_getfetsel('statut', 'spip_lettres', 'id_lettre='.intval($id));
	if ($statut=='brouillon')
		return autoriser('editer','lettres');
	return false;
}

function autoriser_lettre_previsualiser($faire, $type, $id, $qui, $opt){
	$statut = sql_getfetsel('statut', 'spip_lettres', 'id_lettre='.intval($id));
	if ($statut=='brouillon')
		return autoriser('editer','lettres');
	return false;
}

function autoriser_lettre_joindrearticle_dist($faire, $type, $id, $qui, $opt){
	$statut = sql_getfetsel('statut', 'spip_lettres', 'id_lettre='.intval($id));
	if ($statut=='brouillon')
		return autoriser('editer','lettres');
	return false;
}

function autoriser_dater($faire, $type, $id, $qui, $opt) {
	if (!isset($opt['statut'])){
		$table = table_objet($type);
		$trouver_table = charger_fonction('trouver_table','base');
		$desc = $trouver_table($table);
		if (!$desc)
			return false;
		if (isset($desc['field']['statut'])){
			$statut = sql_getfetsel("statut", $desc['table'], id_table_objet($type)."=".intval($id));
		}
		else
			$statut = 'publie'; // pas de statut => publie
	}
	else
		$statut = $opt['statut'];

	if ($statut == 'publie'
	 OR ($statut == 'prop' AND $type=='article' AND $GLOBALS['meta']["post_dates"] == "non"))
		return autoriser('modifier', $type, $id);

	if ($type=='lettre' AND($statut == 'brouillon' OR $statut == 'en_ligne' OR $statut == 'hors_ligne'))
		return autoriser('modifier', $type, $id);

	return false;
}

?>