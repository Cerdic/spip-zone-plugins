<?php

// surcharger les boutons d'administration
function minibando_formulaire_admin($flux) {

	include_spip('minibando_fonctions');
	$contexte = definir_barre_contexte();
	$boutons = definir_barre_boutons($contexte, false);
	$minibando = minibando($boutons,$contexte);

	$flux['data'] = preg_replace('%(<!--minibando-->)%is', $minibando.'$1', $flux['data']);

	// SPIP 2.1.x => pas d'extension statistiques...
	if (!function_exists('stats_formulaire_admin')) {
		if (
		 isset($flux['args']['contexte']['objet'])
		 AND $objet = $flux['args']['contexte']['objet']
		 AND isset($flux['args']['contexte']['id_objet'])
		 AND $id_objet = $flux['args']['contexte']['id_objet']
		 ) {
			if ($l = admin_stats($objet, $id_objet, $GLOBALS['var_preview'])) {
				$btn = recuperer_fond('prive/bouton/statistiques', array(
					'visites' => $l[0],
					'popularite' => $l[1],
					'statistiques' => $l[2],
				));
				$flux['data'] = preg_replace('%(<!--extra-->)%is', $btn.'$1', $flux['data']);				
			}
		}
	}

	return $flux;
}

?>