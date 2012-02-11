<?php
/**
 * Plugin Contacts & Organisations pour Spip 2.0
 * Licence GPL (c) 2009 - 2010- Ateliers CYM
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_PSEUDO_dist($p) {
	/***
	 * Cette balise s'emploie dans une boucle (CONTACTS).
	 * Elle retourne le champ #NOM du spip_auteur d'un contact.
	 *
	 */
	$p->code = "recuperer_fond('modeles/pseudo',
		array('id_contact' => ".champ_sql('id_contact', $p)
		."), array('trim'=>true), "
		. _q($connect)
		.")";
	$p->interdire_scripts = false; // securite apposee par recuperer_fond()
	return $p;
	}

function balise_ORGANISATIONS_dist($p) {
	/***
	 * Cette balise s'emploie dans une boucle (CONTACTS).
	 * Elle retourne les champs #NOM des spip_organisations liés au contact.
	 *
	 */
	$_lesorganisations = champ_sql('lesorganisations', $p); 

	// si le champ 'lesorganisations' n'existe pas	
	 
	if ($_lesorganisations
	AND $_lesorganisations != '@$Pile[0][\'lesorganisations\']') {
		// on applique le modele lesorganisations.html en passant id_contact dans le contexte;
		$p->code = "safehtml($_lesorganisations)";
		$p->interdire_scripts = false;
	} else {
		// sinon on prend le champ 'lesorganisations' 
		$connect = !$p->id_boucle ? '' 
		  : $p->boucles[$p->id_boucle]->sql_serveur;

		$p->code = "recuperer_fond('modeles/lesorganisations',
			array('id_contact' => ".champ_sql('id_contact', $p)
			."), array('trim'=>true), "
			. _q($connect)
			.")";
		$p->interdire_scripts = false; // securite apposee par recuperer_fond()
	}

	return $p;
}

// Balise #PRENOM_AUTEUR
// a modifier pour les appeler "dist"
function trouve_prenom($id_auteur) {
	
	// $prenom = sql_getfetsel("prenom","spip_contacts LEFT JOIN spip_contacts_liens ON (spip_contacts.id_contact=spip_contacts_liens.id_contact AND objet='auteur')", "id_objet=" . intval($id_auteur));
	$prenom = sql_getfetsel("prenom","spip_contacts", "id_auteur=" . intval($id_auteur));
	
	if (!empty($prenom))
		return $prenom;

	return '';
}
function balise_PRENOM_AUTEUR($p) {
	$id_auteur = champ_sql('id_auteur', $p);
	$p->code = "trouve_prenom(".$id_auteur.")";
	$p->statut = 'php';
	return $p;
}

// Balise #CIVILITE_AUTEUR
// a modifier pour les appeler "dist"
function trouve_civilite($id_auteur) {
	
	// $civilite = sql_getfetsel("civilite","spip_contacts LEFT JOIN spip_contacts_liens ON (spip_contacts.id_contact=spip_contacts_liens.id_contact AND objet='auteur')", "id_objet=" . intval($id_auteur));
	$civilite = sql_getfetsel("civilite","spip_contacts", "id_auteur=" . intval($id_auteur));
	
	if (!empty($civilite))
		return $civilite;

	return '';
}
function balise_CIVILITE_AUTEUR($p) {
	$id_auteur = champ_sql('id_auteur', $p);
	$p->code = "trouve_civilite(".$id_auteur.")";
	$p->statut = 'php';
	return $p;
}


?>
