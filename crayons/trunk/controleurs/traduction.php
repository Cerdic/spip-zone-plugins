<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// un controleur php + html
// html == avec un modele, controleurs/traduction.html)
function controleurs_traduction_dist($regs) {
	list(,$crayon,$type,$champ,$id,$classes) = $regs;
	
	$valeur = valeur_colonne_table($type, $id, $champ);
	$n = new Crayon(
		"traduction-".$id."-".$champ,
		$valeur,
		array('motif_langue' => $id, 'controleur' => 'controleurs/traduction')
	);
	
	$contexte = array('motif_langue' => $id, 'value'=>$valeur[$id]);
	spip_log("$valeur = valeur_colonne_table($type, $id, $champ);", _LOG_INFO_IMPORTANTE);
	
	include_spip("inc/traduire_texte");
	if ( $contexte["value"] == '' && function_exists("traduire") ){
		include_spip("inc/filtres");
		$mod = substr($champ,0,-3);
		$lang = substr($champ,-2);
		$valeur_lang_site = _T("$mod:$id", array('spip_lang'=>$GLOBALS['meta']['langue_site']));
		$traduction = textebrut(traduire($valeur_lang_site, $lang, $GLOBALS['meta']['langue_site']));
		$contexte["traduction"] = $traduction;
	}
	$html = $n->formulaire($contexte);
	$status = null;

	return array($html, $status);
}


