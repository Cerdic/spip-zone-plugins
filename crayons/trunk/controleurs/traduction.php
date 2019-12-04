<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// un controleur php + html
// html == avec un modele, controleurs/traduction.html)
function controleurs_traduction_dist($regs) {
	list(,$crayon,$type,$champ,$id,$classes) = $regs;
	spip_log("controleurs_traduction_dist $champ", _LOG_INFO_IMPORTANTE);
	
	$valeur = valeur_colonne_table($type, $id, $champ);
	$n = new Crayon(
		"traduction-".$id."-".$champ,
		$valeur,
		array('motif_langue' => $id, 'controleur' => 'controleurs/traduction')
	);
	
	$contexte = array('motif_langue' => $id, 'value'=>$valeur[$id]);
	$html = $n->formulaire($contexte);
	$status = null;

	return array($html, $status);
}


