<?php
/**
 * Pipelines du plugin Incarner
 *
 * @plugin     Incarner
 * @copyright  2016
 * @author     Michel Bystranowski
 * @licence    GNU/GPL
 */

/**
 * Afficher un lien pour incarner un auteur sur sa page
 *
 * @pipeline affiche_gauche
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function incarner_boite_infos($flux) {

	if (($flux['args']['type'] === 'auteur')
			and (autoriser('incarner'))) {
		include_spip('base/abstract_sql');

		$id_auteur = $flux['args']['id'];
		$login = sql_getfetsel(
			'login',
			'spip_auteurs',
			'id_auteur='.intval($id_auteur)
		);
		$url_self = urlencode(self());
		$url_action = generer_url_action(
			'incarner',
			'login=' . $login . '&redirect=' . $url_self
		);

		$flux['data'] .= '<a href="' . $url_action . '">';
		$flux['data'] .= _T('incarner:incarner_login', array('login' => $login));
		$flux['data'] .= '</a>';
	}

	return $flux;
}
