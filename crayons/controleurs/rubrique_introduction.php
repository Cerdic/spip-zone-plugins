<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// un controleur php + html
// html == avec un modele, controleurs/rubrique_introduction.html)
function controleurs_rubrique_introduction_dist($regs) {
	list(,$crayon,$type,$champ,$id) = $regs;
	$valeur = valeur_colonne_table($type, array('descriptif', 'texte'), $id);
	if ($valeur === false) {
		return array("$type $id $champ: " . _U('crayons:pas_de_valeur'), 6);
	}

	$n = new Crayon(
		'rubrique-introduction-' . $id,
		$valeur,
		array('hauteurMini' => 234, 'controleur' => 'controleurs/rubrique_introduction')
	);

	$contexte = array(
	'h_descriptif' => (int)ceil($n->hauteur*5/13),
		'h_texte' => (int)ceil($n->hauteur*7/13));
	$html = $n->formulaire($contexte);
	$status = null;

	return array($html, $status);
}
