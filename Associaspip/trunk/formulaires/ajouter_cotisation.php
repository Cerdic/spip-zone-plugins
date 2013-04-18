<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_ajouter_cotisation_charger_dist($id_auteur, $nom_prenom, $id_categorie, $validite, $editable) {
	if ($id_categorie) { // si le membre a une categorie
		$categorie = sql_fetsel('duree, prix_cotisation', 'spip_asso_categories', "id_categorie=". intval($id_categorie));
		list($annee, $mois, $jour) = explode('-', $validite);
		if ($jour==0 OR $mois==0 OR $annee==0)
			list($annee, $mois, $jour) = explode('-',date('Y-m-d'));
		$mois += $categorie['duree'];
		$contexte['validite'] = date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
		$contexte['montant'] = $categorie['prix_cotisation'];
	} else { // le membre n'a pas de categorie
		$contexte['validite'] = date('Y-m-d');
		$contexte['montant'] = 0;
	} // validite et montant de cotisation
	$contexte['justification'] = _T('asso:cotisation') ." [$nom_prenom"."->membre$id_auteur]"; // la justification
	$contexte['readonly'] = $editable ? '' : ' readonly="readonly"';
	$contexte['_action'] = array('ajouter_cotisation',$id_auteur); // pour passer securiser action
	association_chargeparam_destinations('cotisations', $contexte); // les destinations

	return $contexte;
}

function formulaires_ajouter_cotisation_verifier_dist($id_auteur, $nom_prenom, $categorie, $validite) {
	$erreurs = array();

	if ($GLOBALS['association_metas']['comptes'] && $GLOBALS['association_metas']['pc_cotisations']) {
		if ($erreur = association_verifier_montant('montant') )
			$erreurs['montant'] = $erreur;
		if ($erreur = association_verifier_date('date') )
			$erreurs['date'] = $erreur;
		if ($erreur = association_verifier_destinations('montant') )
			$erreurs['destinations'] = $erreur;
	}
	if ($erreur = association_verifier_date('validite') )
		$erreurs['validite'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_ajouter_cotisation_traiter_dist($id_auteur, $nom_prenom, $categorie, $validite) {
	// partie de code grandement inspiree du code de formulaires_editer_objet_traiter dans ecrire/inc/editer.php
	$res = array();
	// eviter la redirection forcee par l'action...
	set_request('redirect');
	$action_cotisation = charger_fonction('ajouter_cotisation', 'action');
	list($id_auteur,$err) = $action_cotisation($id_auteur);
	if ($err OR !$id_auteur) {
		$res['message_erreur'] = ($err?$err:_T('erreur_traite'));
	} else {
		$res['message_ok'] = '';
		$res['redirect'] = generer_url_ecrire('adherents'); // on renvoit sur la page adherents mais on perd a l'occasion d'eventuel filtres inseres avant d'arriver au formulaire de cotisation...
	}
	return $res;
}

?>