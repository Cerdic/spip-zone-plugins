<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_FORMULAIRE_EDITER_AUTEUR ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_EDITER_AUTEUR', array('id_auteur'));
}

function balise_FORMULAIRE_EDITER_AUTEUR_stat($args, $filtres) {
	if(!($id = intval($args[0])))
		return '';
	if(!($r = spip_abstract_fetsel(
		array('id_auteur', 'nom', 'bio', 'email', 'nom_site', 'url_site', 'pgp'),
		'spip_auteurs', "id_auteur = $id")))
		return '';
	return $r;
}

function balise_FORMULAIRE_EDITER_AUTEUR_dyn(
$id_auteur, $nom, $bio, $email, $nom_site, $url_site, $pgp
) {
	return 
		array('formulaires/formulaire_editer_auteur', 0,
			array(
			'id_auteur' => $id_auteur,
			'nom' => $nom,
			'bio' => $bio,
			'email' => $email,
			'nom_site' => $nom_site,
			'url_site' => $url_site,
			'pgp' => $pgp
			)
		);
}

?>