<?php

include_spip('inc/memoization');

/**
 * Retourne la liste des différentes boutiques
 * connues dans ce Prestashop.
 *
 * @return array Liste des boutiques et leurs URLs
 */
function prestashop_ws_list_shops() {
	if (!is_null($W = cache_me())) {
		return $W;
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
	if (!is_null($W = cache_me())) {
		return $W;
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