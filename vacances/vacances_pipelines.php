<?php
/**
 * Utilisations de pipelines par Vacances
 *
 * @plugin     Vacances
 * @copyright  2017
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\Vacances\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Pipeline qui signale que le mode vacances est activé
 *
 *
 * @param array $flux
 * @return array
 */
function vacances_alertes_auteur($flux) {

	include_spip('inc/config');
	if (lire_config('vacances/mode_vacances')) {
		include_spip('inc/autoriser');
		if (autoriser('voir', 'vacances')) {
        	include_spip('inc/filtres');
            $balise_img = chercher_filtre('balise_img');
			$alerte_str = $balise_img(chemin_image('vacances-16.png'));

			$alerte_str .= " "._T('vacances:alerte_mode_active');
			if ($GLOBALS['visiteur_session']['statut'] == '0minirezo') {
            	$alerte_str .= ' &nbsp; [<a href=\''. generer_url_ecrire('configurer_vacances')  .'\'>' . _T('vacances:alerte_mode_modifier'). '</a>]';
			}

            $flux['data'][] = $alerte_str;
		}
	}
	return $flux;
}