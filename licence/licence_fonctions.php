<?php
/**
 * Plugin Licence
 * (c) 2007-2013 fanouch
 * Distribue sous licence GPL
 * 	
 * @package SPIP\Licence\Fonctions
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Lister toutes les licences disponibles
 * 
 * @return
 * 		Retourne le tableau de description des licences
 **/
function licence_lister($id_licence=null) {
	include_spip('inc/licence');
	static $licences = null;
	
	if (is_null($licences)) {
		$licences = $GLOBALS['licence_licences'];
		// Pipeline
		$licences = pipeline('licence_licences', $licences);
	}
	
	if (!is_null($id_licence) and isset($licences[$id_licence])) {
		return $licences[$id_licence];
	}
	else {
		return $licences;
	}
}

function licence_affiche($id_licence,$logo_non,$lien_non){
	include_spip('inc/licence');
	$licence = licence_lister($id_licence);
	
	if (isset($licence['icon']) AND $logo_non != 'non')
		$licence['icon'] = "img_pack/".$licence['icon'];
	else
		$licence['icon'] = '';
	if ($lien_non == 'non')
		$licence['link'] = '';
	return recuperer_fond('licence/licence',$licence);
}

/**
 * Fonction tentant de récupérer une licence cachée dans un texte
 * Ne fonctionne que pour les Creative Commons
 * 
 * @param string $texte
 * 		Le texte à analyser
 * @return int $id_licence 
 * 		L'identifiant numérique de la licence trouvée ou false 
 */
function licence_recuperer_texte($texte){
	if(preg_match('/http:\/\/creativecommons.org\/licenses\/(.[a-z|-]*)\//',$texte,$matches)){
		include_spip('inc/licence');
		$licence_id = 'cc-'.$matches[1];
		$licences = licence_lister();
		foreach($licences as $id_licence=>$licence_info){
			if($licence_info['abbr'] == $licence_id){
				return $id_licence;
			}
		}
	}
	return false;
}
?>
