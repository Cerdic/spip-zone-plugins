<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

function action_snippet_importe(){
	global $auteur_session;
	$arg = _request('arg');
	$args = explode("-",$arg);
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
	include_spip("inc/actions");
	if (verifier_action_auteur("snippet_importe-$arg",$hash,$id_auteur)==TRUE) {
		$table = $args[0];
		$id = $args[1];
		$source = substr($arg,strlen("$table-$id-"));
		$unlink = false;
		if (!strlen($source)){
			if (($val = $_FILES['snippet_xml']) AND (isset($val['tmp_name']))) {
				$source = $val['tmp_name'];
				$unlink = true;
			}
		}
		if ($id==$table OR ($id=intval($id))&&strlen($source)){
			if (substr($table,0,5)=="spip_") $table1 = substr($table,5);
			else $table1 = "spip_$table";
			if (!$f = charger_fonction("importer","snippets/$table")){
				$f = charger_fonction("importer","snippets/$table1");
			}
			if ($f)
				$f($id,$source);
		}
		if ($unlink)
			@unlink($source);
	}
	redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

?>