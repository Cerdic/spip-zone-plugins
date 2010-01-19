<?php
/*
 * snippets
 * Gestion d'import/export XML de contenu
 *
 * Auteurs :
 * Cedric Morin
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/snippets');

function action_snippet_importe(){
	global $auteur_session;
	$arg = _request('arg');
	$args = explode(":",$arg);
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
	include_spip("inc/securiser_action");
	if (verifier_action_auteur("snippet_importe-$arg",$hash,$id_auteur)==TRUE) {
		$table = $args[0];
		$id = $args[1];
		$contexte = $args[2];
		$source = isset($args[3])?$args[3]:"";
		$unlink = false;
		if (!strlen($source)){
			if (($val = $_FILES['snippet_xml']) AND (isset($val['tmp_name']))) {
				$source = $val['tmp_name'];
				$unlink = true;
			}
		}
		if (($id==$table OR ($id=intval($id))) AND strlen($source)){
			$f = snippets_fonction_importer($table);
			if ($f){
				include_spip('inc/xml');
				$arbre = spip_xml_load($source, false);
				$translations = $f($id,$arbre,$contexte);
				snippets_translate_raccourcis_modeles($translations);
			}
		}
		if ($unlink)
			@unlink($source);
	}
	$redirect = str_replace("ecrire/./","./",$redirect);
	redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

?>