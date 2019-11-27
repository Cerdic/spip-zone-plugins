<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// un controleur php + html
// html == avec un modele, controleurs/traduction.html)
function controleurs_traduction_dist($regs) {
	list(,$crayon,$type,$champ,$id) = $regs;
	$valeur = valeur_colonne_table($type, $id, $champ);
	$n = new Crayon(
		"traduction-".$id."-".$champ,
		$valeur,
		array('module_langue' => $champ, 'motif_langue' => $id, 'controleur' => 'controleurs/traduction')
	);

	$contexte = array('module_langue' => $champ, 'motif_langue' => $id);
	$html = $n->formulaire($contexte);
	$status = null;

	return array($html, $status);
}


