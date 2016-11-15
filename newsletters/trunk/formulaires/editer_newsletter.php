<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_newsletter_identifier_dist($id_newsletter='new', $retour='', $lier_trad=0, $config_fonc='newsletter_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_newsletter)));
}

/**
 * Choix par defaut des options de presentation
 * @param array $row
 * @return array
 */
function newsletter_edit_config($row)
{

	$config = array();
	$config['lignes'] = 4;
	$config['langue'] = $GLOBALS['spip_lang'];

	return $config;
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_newsletter_charger_dist($id_newsletter='new', $retour='', $lier_trad=0, $config_fonc='newsletter_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('newsletter',$id_newsletter,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	$valeurs['selection_edito'] = array();
	$possibles = array('article'=>'*','rubrique'=>'*');
	include_spip("action/editer_liens");
	$liens = objet_trouver_liens(array("newsletter"=>$id_newsletter),$possibles);
	foreach ($liens as $lien){
		$valeurs['selection_edito'][] = $lien['objet'].'|'.$lien['id_objet'];
	}

	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_newsletter_verifier_dist($id_newsletter='new', $retour='', $lier_trad=0, $config_fonc='newsletter_edit_config', $row=array(), $hidden=''){
	$baked = 1;
	$statut = (intval($id_newsletter)?sql_getfetsel('statut','spip_newsletters','id_newsletter='.intval($id_newsletter)):'prepa');
	if (in_array($statut,array('prepa','prop','prog')))
		$baked = _request('baked');

	if (!$baked)
		$obli = array('titre','patron');
	else
		$obli = array('titre','html_email');

	if (_request('adresse_envoi_nom')) {
		$oblis[] = 'adresse_envoi_email';
	}

	return formulaires_editer_objet_verifier('newsletter',$id_newsletter,$obli);
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_newsletter_traiter_dist($id_newsletter='new', $retour='', $lier_trad=0, $config_fonc='newsletter_edit_config', $row=array(), $hidden=''){
	$baked = 1;
	$statut = (intval($id_newsletter)?sql_getfetsel('statut','spip_newsletters','id_newsletter='.intval($id_newsletter)):'prepa');
	if (in_array($statut,array('prepa','prop','prog')))
		$baked = _request('baked');

	if ($baked){
		// pas de modif des contenu editoriaux si on est cuit
		set_request('chapo');
		set_request('texte');
		set_request('patron');
	}
	else {
		// pas de modif des contenues bruts si on est en preparation
		set_request('html_email');
		set_request('html_page');
		set_request('texte_email');
	}
	$res = formulaires_editer_objet_traiter('newsletter',$id_newsletter,'',$lier_trad,$retour,$config_fonc,$row,$hidden);

	if (!$baked AND $res['id_newsletter']) {
		// mettre a jour les liens vers les articles selectionnes
		$possibles = array('article'=>'*','rubrique'=>'*');
		$liens = array();
		if ($selection = _request('selection_edito')){
			foreach ($selection as $s){
				$s = explode("|",$s);
				list($objet,$id_objet) = $s;
				if (isset($possibles[$objet])){
					$liens[$objet][] = $id_objet;
				}
			}
		}
		include_spip("action/editer_liens");
		objet_dissocier(array("newsletter"=>$res['id_newsletter']),$possibles);
		if (count($liens))
			objet_associer(array("newsletter"=>$res['id_newsletter']),$liens);

		// regenerer le html et texte...
		// sauf si c'est une nl prog (statut=prog)
		if(!_request('statut')=='prog'){
			$generer_newsletter = charger_fonction("generer_newsletter","action");
			$generer_newsletter($res['id_newsletter']);
		}
	}
	return $res;
}


?>