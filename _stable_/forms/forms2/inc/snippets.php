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


function liste_snippets($table){
	$pattern = $table;
	if (substr($table,0,5)=="spip_") $table = substr($table,5);
	
	$pattern = ".*[.]xml$";
	$snippets = find_all_in_path("snippets/$table/",$pattern);
	return $snippets;
}

function boite_snippets($table,$id,$retour = ""){
	if (!strlen($retour))
		$retour = _DIR_RESTREINT_ABS . self();
	$out = debut_boite_info(true);
	
	$liste = liste_snippets($table);
	foreach($liste as $snippet){
		if (!_DIR_RESTREINT) $snippet = substr($snippet,strlen(_DIR_RACINE));
		$action = generer_action_auteur('snippet_importe',"$table-$id-$snippet",$retour);
		$out .= "<a href='$action'>".basename($snippet)."</a><br/>";
	}

	$out .= fin_boite_info(true);
	return $out;
}


?>