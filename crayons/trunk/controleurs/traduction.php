<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// un controleur php + html
// html == avec un modele, controleurs/traduction.html)
function controleurs_traduction_dist($regs) {
	list(,$crayon,$type,$champ,$id,$classes) = $regs;
	preg_match('|\slang_[a-z]{2}\s|', $classes, $classe_lang);
	$lang = substr($classe_lang[0],6,2);	
	$valeur = _T("$champ:$id",array('spip_lang'=>$lang));
	$n = new Crayon(
		"traduction-".$id."-".$champ,
		$valeur,
		array('motif_langue' => $id, 'controleur' => 'controleurs/traduction')
	);
	
	$contexte = array('valeur'=>$valeur);
	$html = $n->formulaire($contexte);
	$status = null;

	return array($html, $status);
}


