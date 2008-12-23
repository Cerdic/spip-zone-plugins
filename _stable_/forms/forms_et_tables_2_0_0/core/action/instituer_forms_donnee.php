<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_instituer_forms_donnee_dist() {

	//$securiser_action = charger_fonction('securiser_action', 'inc');
	//$securiser_action();
	$arg = _request('arg');
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
	if (!include_spip("inc/securiser_action"))
		include_spip("inc/actions");
	if (verifier_action_auteur("instituer_forms_donnee-$arg",$hash,$id_auteur)==TRUE) {
	
		list($id_donnee, $statut) = preg_split('/\W/', $arg);
		if (!$statut) $statut = _request('statut_nouv'); // cas POST
		if (!$statut) return; // impossible mais sait-on jamais

		$id_donnee = intval($id_donnee);
		spip_query("UPDATE spip_forms_donnees SET statut="._q($statut)." WHERE id_donnee="._q($id_donnee));
		
		if ($rang_nouv = intval(_request('rang_nouv'))){
			include_spip("inc/forms");
			Forms_rang_update($id_donnee,$rang_nouv);
		}
	}
}

?>