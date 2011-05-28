<?php
/**
 * Plugin tradrub
 * Licence GPL (c) 2008-2010 Stephane Laurent (Bill), Matthieu Marcillaud
 * 
 */

/**
 * ajouter la liste des traductions et le formulaire pour definir une traduction
 *
 * @param array $flux
 * @return array
 */
function tradrub_affiche_milieu($flux) {
	if (($type = $flux['args']['exec'])=='naviguer'){
		$id = $flux['args']['id_rubrique'];
		// on affiche uniquement si la rubrique est une traduction
		// OU si on a le droit de la modifier (pour en declarer une)
		$trad = recuperer_fond('prive/traduire/rubrique', array('id_rubrique' => $id), array('ajax'=>true));
		$flux['data'] .= $trad;
	}
	return $flux;
}

/**
 * enregistrer les liens entre traductions au moment d'une creation
 * de rubrique traduite
 *
 * @param array $flux
 * @return array
 */
function tradrub_post_edition($flux) {
	if ((($table = $flux['args']['table']) == 'spip_rubriques')
	and ($id_objet = $flux['args']['id_objet'])
	and ($flux['args']['action'] == "modifier")
	and ($id_trad = _request('lier_trad'))) {
		// seulement si id_trad n'est pas deja defini (nouvelle rubrique donc absolument)
		$existant = sql_getfetsel('id_trad', 'spip_rubriques', 'id_rubrique='. sql_quote($id_objet));
		if (!$existant) {
			rubrique_referente($id_objet, array('lier_trad' => $id_trad));
		}
	}
	return $flux;	
}


// ~= ecrire/action/editer_article
// Poser un lien de traduction vers un rubrique de reference
function rubrique_referente($id_rubrique, $c) {

	if (!$c = intval($c['lier_trad'])) return;

	// selectionner la rubrique cible, qui doit etre different de nous-meme,
	// et quitter s'il n'existe pas
	$id_lier = sql_getfetsel('id_trad', 'spip_rubriques', array(
		"id_rubrique = " . sql_quote($c),
		"id_rubrique <>" . sql_quote($id_rubrique))
	);

	if ($id_lier === NULL)
	{
		spip_log("echec lien de trad vers rubrique incorrect ($lier_trad)");
		return '&trad_err=1';
	}

	// $id_lier est le numero du groupe de traduction
	// Si la rubrique visee n'est pas deja traduite, son identifiant devient
	// le nouvel id_trad de ce nouveau groupe et on l'affecte aux deux
	// articles
	if ($id_lier == 0) {
		sql_updateq("spip_rubriques", array("id_trad" => $c), sql_in("id_rubrique", array($c, $id_rubrique)));
	}
	// sinon ajouter notre rubrique dans le groupe
	else {
		sql_updateq("spip_rubriques", array("id_trad" => $id_lier), "id_rubrique = ". sql_quote($id_rubrique));
	}

	return ''; // pas d'erreur
}

?>
