<?php

// inc/exau_api.php

/**
 * Copyright (c) 2009 Christian Paulus
 * Dual licensed under the MIT and GPL licenses.
 * */

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/**
 * Exporter les champs specifies de la table des auteurs.
 * Pour ajouter/retirer un champ : modifier lister_auteurs.html
 * Le fichier/squelette "lister_auteurs.html" peut etre dans votre rep. de squelettes.
 * @return boolean
 * @param string $statut
 */
function exau_exporter ($statut) {
	
	if(exau_statut_correct ($statut)) {
		
		$skel = find_in_path("lister_auteurs.html");
		
		$filename = exau_generer_nom_fichier ($statut);
		
		$contexte = array(
			'filename' => $filename
			, 'zstatut' => (($statut=='6forum') ? "6" : "0-1")
		);
		
		include_spip('public/assembler');
		$str_export = recuperer_fond("lister_auteurs", $contexte);	
		
		// envoyer le fichier
		header("Content-type: text/html");
		header("Content-Disposition: attachment; filename=\"$filename\"");
		print ($str_export);
		
		return(true);
	}
	
	return(false);
}

/**
 * Nom du fichier transmis
 * @return string
 * @param object $statut
 */
function exau_generer_nom_fichier ($statut) {
	
	$filename = 
		isset($GLOBALS['meta']['nom_site'])
		? preg_replace(",\W,is","_", substr(trim($GLOBALS['meta']['nom_site']),0,16))
		: 'export'
		;
	$ii =  (($statut=='6forum') ? _T('info_visiteurs') : _T('icone_auteurs'));
	
	$filename = $filename . "-" . strtolower($ii) . "-".date("Y-m-d").".html";
	
	return($filename);
}

/**
 * Verifier que le staut demande' est correct.
 * Le statut doit être un des statuts donnes par EXAU_PERMET_STATUTS (cf: mes_options.php)
 * Si $statut est vide, mais que EXAU_PERMET_STATUTS autorise les auteurs, renvoie true
 * @return boolean
 * @param string $statut
 */
function exau_statut_correct ($statut) {
	static $statuts_array;
	static $avec_auteurs;
	
	if($statuts_array === null) {
		$statuts_array = explode(',', EXAU_PERMET_STATUTS);
		$avec_auteurs = in_array('0minirezo', $statuts_array) || in_array('1comite', $statuts_array);
	}
	
	$present = in_array($statut, $statuts_array);
	
	return($present XOR (!$statut && $avec_auteurs));

}

?>