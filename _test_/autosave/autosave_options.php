<?php

function action_editer_article() {
include_spip("action/editer_article");
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	spip_log($arg,"zou");
	
	// si id_article n'est pas un nombre, c'est une creation 
	// mais on verifie qu'on a toutes les données qu'il faut.
	if (!$id_article = intval($arg)) {
		$id_parent = _request('id_parent');
		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		if (!($id_parent AND $id_auteur)) redirige_par_entete('./');
		$id_article = insert_article($id_parent);
		
		# cf. GROS HACK ecrire/inc/getdocument
		# rattrapper les documents associes a cet article nouveau
		# ils ont un id = 0-id_auteur

		spip_query("UPDATE spip_documents_articles SET id_article = $id_article WHERE id_article = ".(0-$id_auteur));
	} 

	// Enregistre l'envoi dans la BD
	$err = articles_set($id_article);

	$redirect = parametre_url(urldecode(_request('redirect')),
		'id_article', $id_article, '&') . $err;

	redirige_par_entete($redirect);
}

?>