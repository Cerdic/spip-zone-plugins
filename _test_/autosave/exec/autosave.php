<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("action/editer_article");
include_spip("inc/securiser_action");

function exec_autosave(){

$titre = _request('titre');
$texte = _request('texte');
$arg = _request('arg');
$id_parent = _request('id_parent');
$arg_document = _request('arg_document');
$arg_vignette = _request('arg_vignette');

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
	
	// calculer le hash de l'action
	list($id_auteur, $pass) =  caracteriser_auteur();
	$hash = _action_auteur("editer_article-$arg", $id_auteur, $pass, 'alea_ephemere');
	
	
    $new_arg_document = split('/',$arg_document);
    $fin_arg = '';
   
    for($i=1; $i < sizeof($new_arg_document) ; $i++){ 
    	$fin_arg = $fin_arg . '/' . $new_arg_document[$i] ;
    }
    
    $new_arg_document = $id_article . $fin_arg ;
  
    $new_arg_vignette = split('/',$arg_vignette);
    $fin_arg = '';
    for($i=1; $i < sizeof($new_arg_vignette) ; $i++){ 
    $fin_arg = $fin_arg . '/' . $new_arg_vignette[$i] ;
    }
    $new_arg_vignette = $id_article . $fin_arg ;
	
	$hash_document = _action_auteur("joindre-$new_arg_document", $id_auteur, $pass, 'alea_ephemere');
	$hash_vignette = _action_auteur("joindre-$arg_vignette", $id_auteur, $pass, 'alea_ephemere');
	echo "{'id_article':'$id_article', 'date':'".date('h:i:s')."','hash':'$hash','hash_document':'$hash_document','hash_vignette':'$hash_vignette','arg_vignette':'$new_arg_vignette','arg_document':'$new_arg_document'}";	
 
}

?>