<?php
/**
 * Utilisations de pipelines par Déconnexion Automatique
 *
 * @plugin     Déconnexion Automatique
 * @copyright  2019
 * @author     tofulm
 * @licence    GNU/GPL
 * @package    SPIP\Decoauto\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function decoauto_jquery_plugins($scripts){
	$scripts[] = produire_fond_statique('js/decoauto_deconnecter.js');
	return $scripts;
}

function decoauto_formulaire_traiter($flux){
	if ($flux['args']['form'] === 'login') {
		$tps_deconnexion = intval(lire_config('decoauto/tps_deconnexion'));
		if ($tps_deconnexion) {
			session_set('tps_deconnexion', $tps_deconnexion);
		}
	}
	return $flux;
}
