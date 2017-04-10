<?php
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Portage SPIP3 : Maieul Rouquette d'apres Artego  #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GNU/GPL (c) 2012                       #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_jeu_identifier_dist($id_jeu='new', $id_rubrique=0, $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_jeu), $associer_objet));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_jeu_charger_dist($id_jeu='new', $id_rubrique=0, $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('jeu',$id_jeu,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_jeu_verifier_dist($id_jeu='new', $id_rubrique=0, $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('jeu',$id_jeu, array('titre_prive'));
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_jeu_traiter_dist($id_jeu='new', $id_rubrique=0, $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
    
	$res = formulaires_editer_objet_traiter('jeu',$id_jeu,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
    
	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_jeu = $res['id_jeu']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('jeu' => $id_jeu), array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], "id_lien_ajoute", $id_jeu, '&');
			}
		}
	}
	// Inserer le type de jeu
	include_spip('jeux_utils');
	$type_jeu = jeux_trouver_nom(_request('texte'));
    $type_jeu = strlen($type_jeu)?$type_jeu:_T('jeux:jeu_vide');
    sql_updateq('spip_jeux',array('type_jeu'=>$type_jeu),'id_jeu='.intval($res['id_jeu']));
    
	return $res;

}


?>