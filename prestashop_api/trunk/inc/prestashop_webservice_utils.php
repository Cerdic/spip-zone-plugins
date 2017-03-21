<?php

include_spip('inc/memoization');


/**
 * Doit-on mettre à jour le cache (de mémoization) ?
 *
 * Mode recalcul et autorisation de recalculer.
 */
function prestashop_ws_cache_update() {
	return (_request('var_mode') === 'recalcul' AND include_spip('inc/autoriser') AND autoriser('recalcul'));
}

/**
 * Retourne la liste des différentes boutiques
 * connues dans ce Prestashop.
 *
 * @return array Liste des boutiques et leurs URLs
 */
function prestashop_ws_list_shops() {
	static $shops = null;
	if (!is_null($shops)) {
		return $shops;
	}

	if (!prestashop_ws_cache_update() and cache_exists(__FUNCTION__)) {
		$shops = cache_get(__FUNCTION__);
		return $shops;
	}

	try {
		// On utilise l'URL de configuration pour retrouver toutes les boutiques / urls.
		$wsps = new \SPIP\Prestashop\Webservice();
	} catch (PrestaShopWebserviceException $ex) {
		spip_log('Erreur Webservice Prestashop : ' . $ex->getMessage());
		return [];
	}

	$shops = [];

	// Est-on en SSL ?
	$xml = $wsps->get([
		'resource' => 'configurations',
		'display' => 'full',
		'filter[name]' => 'PS_SSL_ENABLED'
	]);
	$ssl = (bool)((string)$xml->configurations->configuration->value);

	// Description des magasins
	$xml = $wsps->get([
		'resource' => 'shops',
		'display' => 'full',
		'filter[active]' => 1
	]);

	if ($xml) {
		foreach ($xml->shops->shop as $s) {
			$shops[(int)$s->id] = [
				'id' => (int)$s->id,
				'nom' => (string)$s->name,
				'id_category' => (string)$s->id_category,
				'id_theme' => (string)$s->id_theme,
			];
		}

		// Obtenir les URLs des magasins.
		$xml = $wsps->get([
			'resource' => 'shop_urls',
			'display' => 'full',
			'filter[active]' => 1
		]);

		if ($xml) {
			// On va supposer qu'il n'existe qu'une seule URL par shop…
			// ce qui est loin d'être évident.
			foreach ($xml->shop_urls->shop_url as $u) {
				$id_shop = (int)$u->id_shop;
				$domain = (string)$u->domain;
				$domain_ssl = (string)$u->domain_ssl;
				$physical_uri = (string)$u->physical_uri;
				if ($ssl and $domain_ssl) {
					$shops[$id_shop]['url'] = 'https://' . $domain_ssl . $physical_uri;
				} else {
					$shops[$id_shop]['url'] = 'http://' . $domain . $physical_uri;
				}
			}
		}
	}

	cache_set(__FUNCTION__, $shops, 24 * 3600);

	return $shops;
}



/**
 * Retourne la liste des différentes langues
 * connues dans ce Prestashop.
 *
 * Le truc ennuyant, c'est que sur les «multi boutiques»,
 * typiquement lorsqu'il y a une boutique pour chaque langue,
 * partagées dans le même Prestashop, alors, les produits (par exemple)
 * ont des traductions dans N langues (l'identifiant des langues pour ces
 * traductions est retournée par l'API), mais l'API pour lister les langues
 * (api/languages) ne liste que les langues utilisées par la boutique
 * que l'on appelle sur l'API. Du coup, on ne peut pas savoir simplement
 * à quel code de langue correspond la traduction avec une langue d'identifiant 2,
 * si cette langue n'est pas utilisée dans cette boutique.
 *
 * Il faut parcourir toutes les boutiques du prestashop pour calculer tous les identifants de langue.
 *
 * @return array Liste des langues (codes & urls)
 */
function prestashop_ws_list_languages() {
	static $langues = null;
	if (!is_null($langues)) {
		return $langues;
	}

	if (!prestashop_ws_cache_update() and cache_exists(__FUNCTION__)) {
		$langues = cache_get(__FUNCTION__);
		return $langues;
	}

	$shops = prestashop_ws_list_shops();
	$langues = [];

	foreach ($shops as $s) {
		$url = $s['url'];
		try {
			$wsps = new \SPIP\Prestashop\Webservice($url);
		} catch (PrestaShopWebserviceException $ex) {
			spip_log('Erreur Webservice Prestashop : ' . $ex->getMessage());
			return [];
		}

		// Description des langues du magasin
		$xml = $wsps->get([
			'resource' => 'languages',
			'display' => 'full'
		]);

		if ($xml) {
			// On va supposer qu'il n'existe qu'une seule URL par shop…
			// ce qui est loin d'être évident.
			foreach ($xml->languages->language as $l) {
				$id = (int)$l->id;
				$code = (string)$l->iso_code;
				$langues[$id] = [
					'code' => $code,
					'shop' => $s
				];
			}
		}
	}

	cache_set(__FUNCTION__, $langues, 24 * 3600);

	return $langues;
}


/**
 * Retourne la liste des différentes langues et urls de shops de prestashop
 *
 * @uses prestashop_ws_list_languages();
 * @param string|null $lang
 *    Langue souhaitée. Null : utilise la langue en cours.
 * @return string
 *    URL pour cette langue (utilisera URL par défaut si aucune URL spécifique pour cette langue).
 */
function prestashop_ws_list_shops_by_lang($lang = null) {
	$langues = [];
	$ls = prestashop_ws_list_languages();
	foreach ($ls as $l) {
		$langues[$l['code']] = $l['shop']['url'];
	}
	if (is_null($lang)) {
		$lang = $GLOBALS['spip_lang'];
	}
	if (isset($langues[$lang])) {
		return $langues[$lang];
	}
	return lire_config('prestashop_api/url');
}


/**
 * Retourne la liste des champs simples d'une ressource
 *
 * Uniquement les champs qui ont un texte directement
 * (pas les associations ou les champs ayant des traductions)
 *
 * @fixme : que faire pour les autres champs du coup ?
 *
 * @param string $resource
 *     Nom de la ressource désirée (ex: 'products')
 * @return array couples (nom du champ => type de champ)
 */
function prestashop_ws_show_resource($resource) {
	if (!$resource) {
		return [];
	}

	static $shows = [];
	if (isset($shows[$resource])) {
		return $shows[$resource];
	}

	if (!prestashop_ws_cache_update() and cache_exists(__FUNCTION__ . '-' . $resource)) {
		$shows[$resource] = cache_get(__FUNCTION__ . '-' . $resource);
		return $shows[$resource];
	}

	try {
		// on suppose les champs identiques quelque soit la boutique
		$wsps = \SPIP\Prestashop\Webservice::getInstanceByLang();
	} catch (PrestaShopWebserviceException $ex) {
		spip_log('Erreur Webservice Prestashop : ' . $ex->getMessage());
		return [];
	}

	$show = [];
	// Demander les données au Prestashop.
	if ($xml = $wsps->show($resource)) {
		foreach ($xml->children() as $champs) {
			foreach ($champs as $nom => $desc) {
				// champs simples
				if (!count($desc)) {
					$show[$nom] = (string)$desc['format'];
				}
			}
		}
	}

	$shows[$resource] = $show;
	cache_set(__FUNCTION__ . '-' . $resource, $show, 24 * 3600);

	return $show;
}
