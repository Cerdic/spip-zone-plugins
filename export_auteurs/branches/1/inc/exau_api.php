<?php

// inc/exau_api.php

/**
 * Copyright (c) 2009 Christian Paulus
 * Dual licensed under the MIT and GPL licenses.
 * */

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Exporter les champs specifies de la table des auteurs.
 * Pour ajouter/retirer un champ : modifier lister_auteurs.html
 * Le fichier/squelette "lister_auteurs.html" peut etre dans votre rep. de squelettes.
 * @return boolean
 * @param string $statut
 */
function exau_exporter ($statut) {
	
	$statut = trim($statut);
	
	if($statut = exau_statut_correct ($statut)) {
		
		$skel = find_in_path("lister_auteurs.html");

		$not = ((strpos($statut, '!') === 0) ? '^' : '');
		$statut = trim($statut, '!');
		
		$filename = exau_generer_nom_fichier ($statut);

		$contexte = array(
			'filename' => $filename
			, 'zstatut' => $not . $statut
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
 * Verifie que le statut demande' est correct.
 * Si $statut est vide, mais que EXAU_EXPORTER_TOUT autorise les auteurs, renvoie true
 * sinon, verifie si la selection est correcte
 * @return boolean OR string
 * @param string $statut
 */
function exau_statut_correct ($statut) {
	
	$btn_partout = (($ii = lire_config('exau/btneverywhere')) && ($ii == 'on'));
	
	//$complet = (defined('EXAU_EXPORTER_TOUT') && EXAU_EXPORTER_TOUT);
	$complet = $btn_partout;
	
	if(!$statut && $complet) 
	{
		$statut = EXAU_STATUTS_AUTEURS;
		return($statut);
	}
	else
	{
		if(($statut == EXAU_STATUTS_INVITES) || ($statut == EXAU_STATUTS_INVITES2))
		{
			return(EXAU_STATUTS_INVITES);
		}
		if($complet && ($statut == EXAU_STATUTS_AUTEURS)
		) {
			return($statut);
		}
	}
	return (false);
}

