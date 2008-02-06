<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("action/editer_article");
include_spip("inc/securiser_action");

function exec_autosave(){

$titre = _request('titre');
$texte = _request('texte');
$arg = _request('arg');
$id_parent = _request('id_parent');


if (!$id_article = intval($arg)) {
		$id_parent = _request('id_parent');
		$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		if (!($id_parent AND $id_auteur)) redirige_par_entete('./');
		$id_article = insert_article($id_parent);
		
		# cf. GROS HACK ecrire/inc/getdocument
		# rattrapper les documents associes a cet article nouveau
		# ils ont un id = 0-id_auteur

		//spip_query("UPDATE spip_documents_articles SET id_article = $id_article WHERE id_article = ".(0-$id_auteur));
	
		} 

	// Enregistre l'envoi dans la BD
	$err = articles_set($id_article);
	
	// calculer le hash de l'action
	list($id_auteur, $pass) =  caracteriser_auteur();
	$hash = _action_auteur("editer_article-$arg", $id_auteur, $pass, 'alea_ephemere');
	echo "{'id_article':'$id_article', 'date':'".date('h:i:s')."','hash':'$hash'}";	
 
}

?>