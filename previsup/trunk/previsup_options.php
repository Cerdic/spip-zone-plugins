<?php
/**
 * Options au chargement du plugin Prévisualisation persistante
 *
 * @plugin     Prévisualisation persistante
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Previsup\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

define('_PREVIEW_TOKEN', true);

if (!defined('_ACTIVER_PREVISUALISATION_PERSISTANTE')) {
	/** @var bool Activer la prévisu persistante sur le site */
	define('_ACTIVER_PREVISUALISATION_PERSISTANTE', true);
}

if (!defined('_DUREE_PREVISUALISATION_PERSISTANTE')) {
	/** @var int Durée de conservation de la prévisu persistante, en seconde */
	define('_DUREE_PREVISUALISATION_PERSISTANTE', 3600);
}

if (!defined('_COOKIE_PREVISUALISATION_PERSISTANTE')) {
	/** @var string Nom du cookie pour la prévisu persistante */
	define('_COOKIE_PREVISUALISATION_PERSISTANTE', 'spip_previsualisation_persistante');
}


/**
 * Pour un auteur identifié qui demande une preview,
 * accorder une preview 1 heure de navigation sur le site public.
 *
 * - S’il n’a pas actuellement le cookie persistant et qu’il demande une prévisu, le créer.
 * - Pour chaque hit avec cookie previsu, activer le var_mode=preview
 *   (notons que l’autorisation d’accès à ce var_mode sera traitée par init_var_mode() plus tard)
 * - Si hit sur l’espace privé, supprimer automatiquement le cookie de preview.
 *
 * @see init_var_mode()
 */
if (
	_ACTIVER_PREVISUALISATION_PERSISTANTE
	and !empty($GLOBALS['visiteur_session']['id_auteur'])
	and !empty($GLOBALS['visiteur_session']['statut'])
	and !in_array($GLOBALS['visiteur_session']['statut'], array('6forum', '5poubelle'))
) {
	$var_mode = explode(',', trim(_request('var_mode')));
	if (empty($_COOKIE[_COOKIE_PREVISUALISATION_PERSISTANTE])) {
		if (in_array('preview', $var_mode)) {
			// creation du cookie
			include_spip('inc/cookie');
			spip_setcookie(_COOKIE_PREVISUALISATION_PERSISTANTE, 'on', $_COOKIE[_COOKIE_PREVISUALISATION_PERSISTANTE] = time() + _DUREE_PREVISUALISATION_PERSISTANTE);
		}
	} else {
		if (!test_espace_prive()) {
			// ajouter le var_mode previsu
			set_request('var_mode', implode(',', array_unique(array_merge($var_mode, array('preview')))));
		} else {
			// suppression du cookie
			include_spip('inc/cookie');
			spip_setcookie(_COOKIE_PREVISUALISATION_PERSISTANTE, null, time() - 1);
		}
	}
	unset($var_mode);
}


/**
 * Ajouter une icone pour quitter la prévisu
 *
 * @param string $flux
 * @return string
 */
function previsup_affichage_final($flux) {
	if (
		_ACTIVER_PREVISUALISATION_PERSISTANTE
		and defined('_VAR_PREVIEW')
		and _VAR_PREVIEW
		and !empty($_COOKIE[_COOKIE_PREVISUALISATION_PERSISTANTE])
		and $p = stripos($flux, '</body>')
	) {
		$url = generer_url_action('preview_stop', 'redirect=' . urlencode(self()), true);
		$quitter_previsu =
			'<a href="' . $url . '" class="previsu_close">'
			. '<span title="' . attribut_html(_T('previsup:arreter_previsualisation')) . '">✖</span>'
			. '</a>';
		$js = "jQuery('$quitter_previsu').appendTo('.spip-previsu');";
		$js = "jQuery(function(){\n $js \n});";
		$js = "<script type='text/javascript'>$js</script>";
		$js .= "<style>\n"
			. ".previsu_close { position:absolute; right:10px; text-decoration:none; color:white; }\n"
			. ".previsu_close span { padding:3px 5px; color:white;  opacity:.9; }\n"
			. ".previsu_close:hover span { background:#eee; color:black; }\n"
			. "</style>";
		$flux = substr_replace($flux, $js, $p, 0);
	}

	return $flux;
}