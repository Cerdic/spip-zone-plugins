<?php
/**
 * Gestion des URLs propres : surcharge (voir @notes pour le pourquoi et le comment)
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


/**
 * Retrouve le fond et les paramètres d'une URL propre
 * ou produit l'URL propre d'un objet, en fonction des paramètres passés.
 *
 * @note
 * À défaut de pipeline dont on pourrait se servir, on est obligé de surcharger
 * la fonction urls_propres_dist pour gérer correctement les URLS des pages.
 * Néammoins, une fois le cas des pages évacué, c'est toujours la fonction dist qui est utilisée.
 *
 * La fonction dist est prévue pour gérer les URLs des objets.
 * Pour un objet donné, elle va chercher l'URL la plus récente dans `spip_urls`.
 * Or toutes les URLs des pages seraient identifiées comme se rapportant au même objet, avec type='' et id_objet=0,
 * et du coup seraient redirigées vers la même URL : celle la plus récente.
 *
 * On ne serait pas obligé de surcharger on utilisant type=`page` et id_objet=N pour les URLs des pages,
 * mais le contexte retourné serait erroné : `id_page = N` au lieu de `page = X`
 *
 * Valeurs des paramètres quand il s'agit d'une page :
 * $i      = URL personnalisée
 * $entite = ''
 *
 * @param integer | string
 *     URL si on veut retourner son fond et ses paramètres
 *     Numéro d'un objet si on veut retourner son URL propre
 * @param string $entite
 *     Fond si on veut retourner le fond et les paramètres d'une URL
 *     Type d'un objet si on veut retourner son URL propre
 * @param array | string $args
 * @param string $ancre
 * @return array
 *     Fond et paramètres d'une URL propre : [contexte],[type],[url_redirect],[fond]
 *     ou URL décodée de l'objet donné
 */
function urls_propres($i, $entite, $args = '', $ancre = '') {
	include_spip('base/abstract_sql');
	// 1) Gestion des pages (voir @note)
	if (is_string($i) // c'est une URL et pas un id
		and strlen($i)
		and strpos($i, '/') === false // ce n'est pas une URL arborescente
		and !$entite // ce n'est pas l'URL d'un objet
		and $url = strtok($i, '?') // retirer les query strings
		and $ligne = sql_fetsel('page, url', 'spip_urls', array('url = ' . sql_quote($url), 'page != \'\''))
	) {
		$fond = $page = $ligne['page'];
		// récupérer le contexte
		if (is_array($args)){
			$contexte = $args;
		} else {
			$contexte = array();
		}
		$contexte['page'] = $page;
		$retour = array(
			$contexte,
			$entite,
			'',
			$fond,
		);

	// 2) S'il ne s'agit pas d'une page, appel de la fonction dist
	} else {
		include_spip(_DIR_PLUGIN_URLS.'urls/propres');
		$retour = urls_propres_dist($i, $entite, $args, $ancre);
	}

	return $retour;
}
