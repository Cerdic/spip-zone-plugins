<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Dissocier une donnée
 * $arg fournit les arguments de la fonction dissocier_document
 * sous la forme
 * $id_objet-$document-suppr-safe
 *
 * 4eme arg : suppr = true, false sinon
 * 5eme arg : safe = true, false sinon
 * 
 * @return void
 */
function action_dissocier_donnee_dist($arg=null){
	if(is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// attention au cas ou id_objet est negatif !
	if (strncmp($arg,'-',1)==0){
		$arg = explode('-',substr($arg,1));
		list($id_objet, $donnee) = $arg;
		$id_objet = -$id_objet;
	}
	else {
		$arg = explode('-',$arg);
		list($id_objet, $donnee) = $arg;
	}

	if ($id_objet=intval($id_objet)
		AND (
			($id_objet<0 AND $id_objet==-$GLOBALS['visiteur_session']['id_auteur'])
			OR autoriser('modifier','infographies_data',$id_objet)
		)){
			dissocier_donnee($donnee, $id_objet);
			spip_log("$donnes $id_objet","test."._LOG_ERREUR);
		}
	else
		spip_log("Interdit de modifier $id_objet","spip");
}

/**
 * Supprimer un lien entre un document et un objet
 *
 * @param int $id_document
 * @param int $id_objet
 * @param bool $supprime
 *   si true, le document est supprime si plus lie a aucun objet
 * @param bool $check
 *   si true, on verifie les documents references dans le texte de l'objet
 *   et on les associe si pas deja fait
 * @return bool
 */
function supprimer_lien_donnee($id_donnee, $id_objet) {
	if (!$id_donnee = intval($id_donnee))
		return false;

	// D'abord on ne supprime pas, on dissocie
	sql_delete('spip_infographies_donnees','id_infographies_donnee='.$id_donnee.' AND id_infographies_data ='.intval($id_objet));
}

/**
 * Dissocier un ou des documents
 *
 * @param int|string $document
 *   id_document a dissocier
 *   I/image pour dissocier les images en mode Image
 *   I/document pour dissocier les images en mode document
 *   D/document pour dissocier les documents non image en mode document
 * @param  $id_objet
 *   id_objet duquel dissocier
 * @param bool $supprime
 *   supprimer les documents orphelins apres dissociation
 * @param bool $check
 *   verifier le texte des documents et relier les documents references dans l'objet
 * @return void
 */
function dissocier_donnee($donnee, $id_objet, $check = false){
	if ($id_donnee=intval($donnee)) {
		supprimer_lien_donnee($donnee, $id_objet);
	}
	else {
		spip_log("$donnee $id_objet",'test.'._LOG_ERREUR);
		$s = sql_select('id_infographies_donnee',
			"spip_infographies_donnees",
			"id_infographies_data = ".intval($id_objet));
		while ($t = sql_fetch($s)) {
			supprimer_lien_donnee($t['id_infographies_donnee'], $id_objet);
		}
	}
}
?>
