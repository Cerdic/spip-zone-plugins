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

function action_snippet_exporte(){
	global $auteur_session;
	$arg = _request('arg');
	$args = explode("-",$arg);
	$hash = _request('hash');
	$id_auteur = $auteur_session['id_auteur'];
	$redirect = _request('redirect');
	if ($redirect==NULL) $redirect="";
	include_spip("inc/actions");
	if (verifier_action_auteur("snippet_exporte-$arg",$hash,$id_auteur)==TRUE) {
		$table = $args[0];
		$id = $args[1];

		if (substr($table,0,5)=="spip_") $table1 = substr($table,5);
		else $table1 = "spip_$table";
		if (!$f = find_in_path("snippets/$table/exporter.html")){
			$f = find_in_path("snippets/$table1/exporter.html");
		}
		if ($f) {
			include_spip('public/assembler');
			$out = recuperer_fond($f,array('id'=>intval($id)));
			$out = preg_replace(",\n[\s]*(?=\n),","",$out);
			
			$titre=$arg;
			if (preg_match(",<titre>(.*)</titre>,Uims",$out,$regs))
				$titre = preg_replace(',[^-_\w]+,', '_', trim(translitteration(textebrut(typo($reg[1])))));
			$extension = "xml";
			
			Header("Content-Type: text/xml; charset=".$GLOBALS['meta']['charset']);
			Header("Content-Disposition: attachment; filename=$filename.$extension");
			Header("Content-Length: ".strlen($out));
			echo $out;
		}
	}
	redirige_par_entete(str_replace("&amp;","&",urldecode($redirect)));
}

?>