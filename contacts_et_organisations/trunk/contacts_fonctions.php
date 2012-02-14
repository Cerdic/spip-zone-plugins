<?php
/**
 * Plugin Contacts & Organisations pour Spip 2.0
 * Licence GPL (c) 2009 - 2010- Ateliers CYM
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/***
 * Cette balise s'emploie dans une boucle (CONTACTS).
 * Elle retourne le champ #NOM du spip_auteur d'un contact.
 *
 */
function balise_NOM_AUTEUR_dist($p) {
	$connect = !$p->id_boucle ? '' : $p->boucles[$p->id_boucle]->sql_serveur;
	
	$p->code = "recuperer_fond('modeles/nom_auteur',
		array('id_contact' => ".champ_sql('id_contact', $p)
		."), array('trim'=>true), "
		. _q($connect)
		.")";
	$p->interdire_scripts = false; // securite apposee par recuperer_fond()
	return $p;
}


/***
 * Cette balise s'emploie dans une boucle (CONTACTS).
 * Elle retourne les champs #NOM des spip_organisations liés au contact.
 *
 */
function balise_ORGANISATIONS_dist($p) {

	$connect = !$p->id_boucle ? '' : $p->boucles[$p->id_boucle]->sql_serveur;

	$p->code = "recuperer_fond('modeles/lesorganisations',
		array('id_contact' => ".champ_sql('id_contact', $p)
		."), array('trim'=>true), "
		. _q($connect)
		.")";
	$p->interdire_scripts = false; // securite apposee par recuperer_fond()

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
