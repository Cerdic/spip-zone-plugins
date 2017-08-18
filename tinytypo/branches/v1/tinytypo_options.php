<?php

/**
 * Tiny Typo pour SPIP
 * (c) 2014 MIT License
 * by Romy Duhem-Verdière
 * http://tinytypo.tetue.net
 *
 */

$GLOBALS['spip_pipeline']['affichage_final'] .= '|tinytypo';

// Utiliser les class de Tiny Typo dans SPIP :
function tinytypo($texte) {
//	$texte = str_replace('i class="spip"', 'i', $texte);
//	$texte = str_replace('strong class="spip"', 'strong', $texte);
//	$texte = str_replace('p class="spip"', 'p', $texte);
	$texte = str_replace('hr class="spip"', 'hr', $texte);
//	$texte = str_replace('h3 class="spip"', 'h3', $texte);
//	$texte = str_replace('h2 class="spip"', 'h2', $texte);
	$texte = str_replace(' class="spip_in"', '', $texte);
	$texte = str_replace('spip_in', '', $texte);
	$texte = str_replace('spip_out', 'external', $texte);
	$texte = str_replace('spip_url', 'external', $texte);
	$texte = str_replace('spip_glossaire', 'external', $texte);
	$texte = str_replace('spip_mail', 'mailto', $texte);
	$texte = str_replace('blockquote class="spip"', 'blockquote', $texte);
	$texte = str_replace('code class="spip_code"', 'code', $texte);
//	$texte = str_replace('spip_code', 'spip_code p font3', $texte); // dirty
//	$texte = str_replace('spip_cadre', 'spip_cadre p font3', $texte); // dirty
	$texte = str_replace('<table class="spip', '<table class="table spip', $texte);
	$texte = str_replace('spip_logos', 'spip_logo', $texte);
	$texte = str_replace('spip_logo', 'thumb spip_logo', $texte);
	$texte = str_replace('spip_logo_center', 'center', $texte);
	$texte = str_replace('spip_documents_center', 'center', $texte);
	$texte = str_replace('center center', 'center', $texte); // éviter les doublons
	$texte = str_replace('margin-right:auto;margin-left:auto;text-align:center;', '', $texte); // grrr corrigé notamment ici : https://zone.spip.org/trac/spip-zone/changeset/105778/_plugins_
	$texte = str_replace('spip_logo_left', 'left', $texte);
	$texte = str_replace('spip_documents_left', 'left', $texte);
	$texte = str_replace('left left', 'left', $texte); // éviter les doublons
	$texte = str_replace('spip_logo_right', 'right', $texte);
	$texte = str_replace('spip_documents_right', 'right', $texte);
	$texte = str_replace('right right', 'right', $texte); // éviter les doublons
	$texte = str_replace('<p><figcaption', '<figcaption', $texte); // sale bug (vieux SPIP 2 ?)
	$texte = str_replace('spip_surligne', 'mark', $texte);
	return $texte;
}

?>