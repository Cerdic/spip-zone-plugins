<?php
/**
 * Plugin Déclinaisons Prix
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION'))
	return;
function declinaisons_recuperer_fond($flux) {
	$fond = $flux['args']['fond'];
	$contexte = $flux['args']['contexte'];

	// inclure le champ déclinaison
	if ($fond == 'formulaires/prix') {
		include_spip('inc/config');
		$afficher_prix = recuperer_fond('formulaires/inc-prix_affichage', $contexte);
		$declinaison_champs = recuperer_fond('formulaires/inc-prix_champ', $contexte);

		$patterns = array(
			'/<!--fini champs!-->/',
			'/<div class="liste prix">(.*?)<\/div>/ims'
		);
		$replacements = array(
			'<!--fini champs!-->' . $declinaison_champs,
			$afficher_prix
		);
		$rep = preg_replace($patterns, $replacements, $flux['data']['texte'], 1);

		$flux['data']['texte'] = $rep;
	}

	return $flux;
}

function declinaisons_formulaire_charger($flux) {
	$form = $flux['args']['form'];

	// cré un contact si pas encore existant
	if ($form == 'prix') {
		$flux['data']['_hidden'] .= '<input type="hidden" name="objet_titre" value="declinaison">';
	}
	return ($flux);
}

// declare l'object pour le Plugin shop https://github.com/abelass/shop
function declinaisons_shop_objets($flux) {
	$flux['data']['declinaisons'] = array(
		'action' => 'declinaisons',
		'nom_action' => _T('declinaison:titre_declinaisons'),
		'icone' => 'declinaisons-16.png'
	);

	return $flux;
}
