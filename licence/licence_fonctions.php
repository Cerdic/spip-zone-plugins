<?php
/**
 * Plugin Licence
 * (c) 2007-2013 fanouch
 * Distribue sous licence GPL
 * 	
 * @package SPIP\Licence\Fonctions
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function licence_affiche($id_licence,$logo_non,$lien_non){
	include_spip('inc/licence');
	$licence = $GLOBALS['licence_licences'][$id_licence];
	if (isset($licence['icon']) AND $logo_non != 'non')
		$licence['icon'] = "img_pack/".$licence['icon'];
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
		foreach($GLOBALS['licence_licences'] as $id_licence=>$licence_info){
			if($licence_info['abbr'] == $licence_id){
				return $id_licence;
			}
		}
	}
	return false;
}
?>