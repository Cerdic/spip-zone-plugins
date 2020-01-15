<?php
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Action pour remplir un champ donné sur un objet donné
 *
 * @param null|string $arg
 *     string `quoi/champ/objet/id_objet/` tel que `1898-10-14 00:00:00/date_naissance/contact/8/`
 *     string `quoi/champ/objet/id_objet/` tel que `1980-10-08 00:00:00/date_deces/contact/8/`
 * utilisation dans un squelette
 * [(#BOUTON_ACTION{
   '<:remplir_champ_date_naissance:>',
    #URL_ACTION_AUTEUR{remplir_champ,1898-10-14 00:00:00/date_naissance/#OBJET/#ID_OBJET,#SELF},
    ajax})]
 *
 * @return array
 *     Liste (identifiant de l'objet, Texte d'erreur éventuel)
 */
function action_remplir_champ_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	
	//`quoi/champ/objet/id_objet/`
	$args = explode('/', $arg);
	$quoi = $args[0];
	$champ = $args[1];
	$objet = $args[2];
	$id_objet = $args[3];
	
	
	// Enregistrer dans la BDD
	if ($id_objet > 0) {
		$err = remplir_champ($quoi,$champ,$objet,$id_objet);
	}

	return array($id_objet, $err);
}

function remplir_champ($quoi,$champ,$objet,$id_objet){
	
	$id_table_objet = id_table_objet($objet); //date_naissance
	$table = table_objet_sql($objet); //spip_contacts
	
	// verifier que le champ existe pour continuer
	if ($verifier = sql_getfetsel($champ, $table, "$id_table_objet = ".sql_quote($id_objet))){
		
		$set = array(
			$champ => $quoi
		);
			
		include_spip('action/editer_objet');
		objet_modifier($objet, $id_objet, $set);
	}

	return $id_objet;
}