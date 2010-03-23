<?php

// Minima requis pour le champs password, penser a gerer le passé
//define('_PASS_MIN','8');

include_spip('base/abstract_sql');

/**
 *
 * Déclaration des pipelines introduits par le plugin inscription2
 *
 */

// Sélectionne les champs qui ne doivent pas être créés dans la tables auteurs_elargis
// Notamment l'ensemble de la table spip_auteurs
$GLOBALS['spip_pipeline']['i2_exceptions_des_champs_auteurs_elargis'] = '';

// Sélectionne les champs qui ne doivent pas être chargés dans le formulaire
// Garde les champs de spip_auteurs et ne prends pas en compte les autres
$GLOBALS['spip_pipeline']['i2_exceptions_chargement_champs_auteurs_elargis'] = '';

$GLOBALS['spip_pipeline']['i2_verifications_specifiques'] = '';
$GLOBALS['spip_pipeline']['i2_charger_formulaire'] = '';
$GLOBALS['spip_pipeline']['i2_verifier_formulaire'] = '';
$GLOBALS['spip_pipeline']['i2_traiter_formulaire'] = '';
$GLOBALS['spip_pipeline']['i2_confirmation'] = '';
$GLOBALS['spip_pipeline']['i2_cfg_form'] = '';
$GLOBALS['spip_pipeline']['i2_form_debut'] = '';
$GLOBALS['spip_pipeline']['i2_form_fin'] = '';

/**
 *
 * Surcharge de la boucle auteurs (à l'origine: http://doc.spip.org/@boucle_AUTEURS_dist)
 * <BOUCLE(AUTEURS)>
 * Création d'une jointure automatique avec spip_auteurs_elargis
 *
 * @return
 * @param object $id_boucle
 * @param object $boucles
 */
function boucle_AUTEURS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mstatut = $id_table .'.statut';

	// Restreindre aux elements publies
	if (!isset($boucle->modificateur['criteres']['statut'])) {
		// Si pas de lien avec un article, selectionner
		// uniquement les auteurs d'un article publie
		if (!$GLOBALS['var_preview']){
			fabrique_jointures($boucle, array(
				array($id_table, array('spip_auteurs_elargis'), 'id_auteur')),
				'', true, $boucle->show, $id_table);
			if (!isset($boucle->modificateur['lien']) AND !isset($boucle->modificateur['tout'])) {
				fabrique_jointures($boucle, array(
					array($id_table, array('spip_auteurs_articles'), 'id_auteur'),
					array('', array('spip_articles'), 'id_article')), true, $boucle->show, $id_table);
				$t = array_search('spip_articles', $boucle->from) . '.statut';
				array_unshift($boucle->where,array("'='", "'$t'", "'\\'publie\\''"));
			}
		}
		// pas d'auteurs poubellises
		array_unshift($boucle->where,array("'!='", "'$mstatut'", "'\\'5poubelle\\''"));
	}

	return calculer_boucle($id_boucle, $boucles);
}

/**
 *
 * Autorisation pour la table spip_auteurs_elargis
 * Autorise les visiteurs a modifier leurs infos dans cette table
 *
 * @return
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 */
if (!function_exists('autoriser_spip_auteurs_elargis')) {
	function autoriser_auteurs_elargi($faire, $type, $id, $qui, $opt) {
		$query = sql_getfetsel("id_auteur","spip_auteurs_elargis","id_auteur=".$id);
		if($query['id_auteur']==$qui['id_auteur'])
			$qui['id_auteur'] = $id;
		return autoriser($faire,'auteur', $id, $qui, $opt);
	}
}

/**
 *
 * Autorisation de modification pour la table spip_auteurs
 * Autorise les visiteurs a modifier leurs infos dans cette table
 *
 * @return
 * @param object $faire
 * @param object $type
 * @param object $id
 * @param object $qui
 * @param object $opt
 */
if (!function_exists('autoriser_auteur_modifier')) {
function autoriser_auteur_modifier($faire, $type, $id, $qui, $opt) {
	// Ni admin ni redacteur => non
	if (in_array($qui['statut'], array('0minirezo', '1comite')))
		return autoriser_auteur_modifier_dist($faire, $type, $id, $qui, $opt);
	else
		return
			$qui['statut'] == '6forum'
			AND $id == $qui['id_auteur'];
	}
}

if (!function_exists('revision_auteurs_elargi')) {
	function revision_auteurs_elargi_dist($id, $c=false) {
		return modifier_contenu('auteurs_elargi', $id,
			array(
				'champs' => array('sexe', 'nom_famille', 'prenom', 'adresse', 'ville', 'code_postal', 'pays', 'telephone', 'fax', 'mobile', 'adresse_pro', 'code_postal_pro', 'pays_pro', 'ville_pro', 'telephone_pro', 'fax_pro', 'mobile_pro'),
				'nonvide' => array('nom_email' => _T('info_sans_titre'))
			),
			$c);
	}
}
?>