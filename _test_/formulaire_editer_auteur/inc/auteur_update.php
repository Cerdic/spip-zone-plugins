<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/abstract_sql');
include_spip('inc/filtres');

spip_connect();

function inc_auteur_update() {

	// Ne pas se laisser polluer par les pollueurs de globales
	$id_auteur = intval(_request('id_auteur'));
	$nom = _request('nom');
	$email = _request('email');
	$bio = _request('bio');
	$pgp = _request('pgp');
	$nom_site = _request('nom_site');
	$url_site = _request('url_site');

	$query = "UPDATE spip_auteurs SET
		nom = ".spip_abstract_quote(corriger_caracteres($nom)).",
		email = ".spip_abstract_quote(corriger_caracteres($email)).",
		bio = ".spip_abstract_quote(corriger_caracteres($bio)).",
		pgp = ".spip_abstract_quote(corriger_caracteres($pgp)).",
		nom_site = ".spip_abstract_quote(corriger_caracteres($nom_site)).",
		url_site = ".spip_abstract_quote(corriger_caracteres($url_site))."
	WHERE id_auteur = $id_auteur";
	spip_query($query);

	charger_generer_url();
	return generer_url_public('editer_profil',
	$args="id_auteur=$id_auteur", true);
}

?>