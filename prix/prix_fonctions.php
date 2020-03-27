<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * La balise qui va avec le prix TTC
 *
 * @param Object $p
 * @return Float
 */
function balise_PRIX_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	if (!$_type = interprete_argument_balise(1,$p)){
		$_type = sql_quote($p->boucles[$b]->type_requete);
		$_id = champ_sql($p->boucles[$b]->primary,$p);
	}
	else
		$_id = interprete_argument_balise(2,$p);
	$connect = $p->boucles[$b]->sql_serveur;
	$p->code = "prix_objet(intval(".$_id."),".$_type.','.sql_quote($connect).")";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * La balise qui va avec le prix HT
 *
 * @param Object $p
 * @return Float
 */
function balise_PRIX_HT_dist($p) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	if (!$_type = interprete_argument_balise(1,$p)){
		$_type = sql_quote($p->boucles[$b]->type_requete);
		$_id = champ_sql($p->boucles[$b]->primary,$p);
	}
	else
		$_id = interprete_argument_balise(2,$p);
	$connect = $p->boucles[$b]->sql_serveur;
	$p->code = "prix_ht_objet(intval(".$_id."),".$_type.','.sql_quote($connect).")";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Obtenir le prix TTC d'un objet
 *
 * @param Integer $id_objet
 * @param String $type_objet
 * @return Float
 */
function prix_objet($id_objet, $objet, $serveur = '') {
	$fonction = charger_fonction('prix', 'inc/');
	return $fonction($objet, $id_objet, array(), $serveur);
}

/**
 * Obtenir le prix HT d'un objet
 *
 * @param Integer $id_objet
 * @param String $type_objet
 * @return Float
 */
function prix_ht_objet($id_objet, $objet) {
	$fonction = charger_fonction('ht', 'inc/prix');
	return $fonction($objet, $id_objet);
}

/**
 * Compatibilité avec la balise #INFO_PRIX
 *
 * @uses prix_objet
 *
 * @param Integer $id_objet
 * @param String $type_objet
 * @param Array $ligne
 * @return Float
 */
function generer_prix_entite($id_objet, $objet, $ligne) {
	return prix_objet($id_objet, $objet);
}

/**
 * Compatibilité avec la balise #INFO_PRIX_HT
 *
 * @uses prix_ht_objet
 *
 * @param Integer $id_objet
 * @param String $type_objet
 * @param Array $ligne
 * @return Float
 */
function generer_prix_ht_entite($id_objet, $objet, $ligne) {
	return prix_ht_objet($id_objet, $objet);
}

/**
 * Formater un nombre pour l'afficher comme un prix selon une devise
 *
 * @note
 * Fonction déportée dans la fonction surchargeable `filtre_prix_formater`.
 *
 * @uses filtres_prix_formater_dist
 */
function prix_formater($prix, $options = array()) {
	$fonction_formater = charger_fonction('prix_formater', 'filtres/');
	return $fonction_formater($prix, $options);
}

/**
 * Formater un nombre pour l'afficher comme un prix.
 *
 * Le prix retourné respecte les règles d'affichages propres à chaque langue et devise :
 * nombre de décimales, virgules et/ou points, emplacement de la devise, etc.
 *
 * L'option `currency_display` permet d'avoir un format spécifique aux factures.
 * L'option `float_only` permet d'avoir le nombre flottant arrondi selon la devise.
 *
 * @note
 * Nécessite soit l'extension bcmath, soit l'extension intl.
 *
 * @example prix_formater($prix, array('currency'=>'EUR', 'locale'=>'fr-CA'))
 *
 * @see https://github.com/commerceguys/intl/blob/master/src/Formatter/CurrencyFormatterInterface.php#L8
 * @see https://www.php.net/manual/fr/numberformatter.formatcurrency.php
 *
 * @uses prix_devise_defaut
 * @uses prix_locale_defaut
 * @uses prix_devise_info
 * @uses prix_langue_vers_locale
 * @uses prix_filtrer_options_formater
 * @uses prix_alias_options_formater
 *
 * @param float $prix
 *     Valeur du prix à formater
 * @param array $options
 *     Tableau d'options :
 *     - currency|devise :         (String) devise, code alphabétique à 3 lettres.
 *                                 Défaut : celle par défaut configurée
 *     - float|flottant :          (Bool) pour retourner le nombre flottant arrondi selon la devise
 *                                 au lieu d'une chaîne de texte.
 *                                 Défaut : false
 *     - locale :                  (String) identifiant d'une locale (fr-CA) ou code de langue SPIP (fr_tu)
 *     - style :                   (String) standard | accounting.
 *                                 Défaut : standard
 *     - use_grouping :            (Bool) grouper les séparateurs.
 *                                 Défaut : true
 *     - rounding_mode :           constante PHP_ROUND_ ou `none`.
 *                                 Défaut : PHP_ROUND_HALF UP
 *     - minimum_fraction_digits : (Int)
 *                                 Défaut : fraction de la devise.
 *     - maximum_fraction_digits : (Int)
 *                                 Défaut : fraction de la devise.
 *     - currency_display :        (String) symbol | code | none.
 *                                 Défaut : symbol
 * @return string|float
 *     Retourne une chaine contenant le prix formaté avec une devise
 */
function filtres_prix_formater_dist($prix, $options = array()) {
	prix_loader();

	// Alias des options
	$options = prix_alias_options_formater($options);

	// S'assurer d'avoir un nombre flottant
	$prix = floatval(str_replace(',', '.', $prix));

	// Devise à utiliser et sa fraction (ex. : nb pour passer des euros aux centimes)
	$devise = (!empty($options['currency']) ? $options['currency'] : prix_devise_defaut());
	$fraction = intval(prix_devise_info($devise, 'fraction'));

	// S'il faut retourner directement le nombre flottant, on arrondit simplement selon la devise.
	if (!empty($options['float'])) {
		$prix_formate = round($prix, $fraction);

	// Sinon lançons la machine
	} else {

		// Locale à utiliser
		$locale = (!empty($options['locale']) ? $options['locale'] : prix_locale_defaut());
		$locale = prix_langue_vers_locale($locale);

		// 1) De préférence, on utilise la librairie Intl de Commerceguys
		if (extension_loaded('bcmath')) {

			// Options : on pose celles de base puis on ajoute celles passées en paramètre.
			$options_base = array(
				'locale'           => $locale,
				'currency_display' => 'code', // pour l'accessibilité
			);
			if (is_array($options)) {
				$options = prix_filtrer_options_formater($options);
				$options = array_merge($options_base, $options);
			} else {
				$options = $options_base;
			}

			// Formatons
			$numberFormatRepository = new CommerceGuys\Intl\NumberFormat\NumberFormatRepository;
			$currencyRepository = new CommerceGuys\Intl\Currency\CurrencyRepository;
			$currencyFormatter = new CommerceGuys\Intl\Formatter\CurrencyFormatter($numberFormatRepository, $currencyRepository, $options);
			$prix_formate = $currencyFormatter->format($prix, $devise);

		// 2) Sinon on se rabat sur la librairie Intl PECL
		} elseif (extension_loaded('intl')) {
			$currencyFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
			$prix_formate = $currencyFormatter->formatCurrency($prix, $devise);

		// 3) Sinon, on fait le formatage du pauvre
		} else {
			$prix_formate = str_replace('.', ',', round($prix, $fraction)) . '&nbsp;' . $devise;
		}
	}

	return $prix_formate;
}

/**
 * Liste les devises et les informations associées
 *
 * @uses prix_devise_info()
 *
 * @return Array
 *     Tableau associatif avec les codes alphabétiques en clés et les infos en sous-tableaux
 */
function prix_lister_devises() {

	prix_loader();
	$devises = array();

	$currencyRepository = new CommerceGuys\Intl\Currency\CurrencyRepository;
	$codes_devises = $currencyRepository->getList();

	foreach ($codes_devises as $code => $nom) {
		$devises[$code] = prix_devise_info($code);
	}

	return $devises;
}

/**
 * Liste les langues avec leur identifiant de locale.
 *
 * @see https://www.php.net/manual/fr/class.locale.php
 *
 * @return Array
 *     Tableau associatif : locale => nom
 */
function prix_lister_langues() {

	prix_loader();
	$langues = array();

	$languageRepository = new CommerceGuys\Intl\Language\LanguageRepository;
	$repo_locales = $languageRepository->getlist();

	// Prendre la langue du visiteur pour les noms
	$langue_spip = $GLOBALS['spip_lang'];
	$locale_visiteur = prix_langue_vers_locale($langue_spip);

	foreach ($repo_locales as $locale => $nom) {
		$language = $languageRepository->get($locale, $locale_visiteur);
		$langues[$locale] = $language->getName();
	}

	return $langues;
}

/**
 * Renvoie une ou toutes les infos sur une devise
 *
 * @param string $code
 *    Code alphabétique à 3 lettres de la devise
 * @param string $info
 *    Info précise éventuelle :
 *    - nom : nom de la devise
 *    - code : code alphabétique (remis au cas où)
 *    - code_num : code numérique
 *    - symbole : symbole associé
 *    - fraction : fraction pour passer à l'unité inférieure (centimes et cie)
 *    - langue : code de langue utilisée
 * @return string|array
 */
function prix_devise_info($code, $info = '') {
	prix_loader();

	// Langue du visiteur pour les noms
	$langue_spip = $GLOBALS['spip_lang'];
	$locale_visiteur = prix_langue_vers_locale($langue_spip);

	$currencyRepository = new CommerceGuys\Intl\Currency\CurrencyRepository;
	$devise = $currencyRepository->get($code, $locale_visiteur);
	$infos = array(
		'code'     => $code,
		'code_num' => $devise->getNumericCode(),
		'nom'      => $devise->getName(),
		'fraction' => $devise->getFractionDigits(),
		'symbole'  => $devise->getSymbol(),
		'locale'   => $devise->getLocale(),
	);

	$retour = (isset($infos[$info]) ? $infos[$info] : $infos);

	return $retour;
}

/**
 * Retourne la devise par défaut.
 *
 * Celle configurée, sinon des euros
 *
 * @return String
 *     Code alphabétique à 3 lettres
 */
function prix_devise_defaut() {

	include_spip('inc/config');

	// Par défaut celle configurée
	if ($devise_config = lire_config('prix/devise_defaut')) {
		$devise = $devise_config;
	// Sinon des euros
	} else {
		$devise = 'EUR';
	}

	return $devise;
}

/**
 * Retourne la locale d'après la langue du contexte
 *
 * @return String
 *      Identifiant de la locale
 */
function prix_locale_defaut() {

	include_spip('inc/config');

	$langue_spip = $GLOBALS['spip_lang'];
	$locales_config = lire_config('prix/locales', array());

	// Normalement l'admin a configuré la locale correspondante à chaque code langue de spip.
	// Sinon tant pis, on donne juste le code pays tiré du code langue de spip.
	$locale = $locales_config[$langue_spip] ?: prix_langue_vers_locale($langue_spip);

	return $locale;
}

/**
 * Retourne une locale reconnue par Intl.
 *
 * Si c'est un code langue de spip, on ne garde que le code du pays (norme ISO 639).
 *
 * @see https://github.com/commerceguys/intl/blob/master/src/Language/LanguageRepository.php#L46
 * @see https://blog.smellup.net/106
 *
 * @param string $code_langue
 * @return string
 */
function prix_langue_vers_locale($code_langue) {

	include_spip('inc/config');
	$locale = $code_langue;
	$is_langue_spip = in_array($code_langue, explode(',', lire_config('langues_proposees')));

	if ($is_langue_spip) {
		// Extraire le code pays pour avoir la locale "générale" : fr_tu → fr
		$locale = strtolower(strtok($code_langue, '_'));

		// Exceptions : certains codes pays des langues de spip ne font pas partie de la liste des locales.
		// On fait une correspondance manuellement en prenant la locale la plus proche.
		// (ça n'indique pas que ce sont des langues identiques, mais suffisamment proches pour le formatage des prix)
		$exceptions = array(
			'oc'  => 'fr', // occitan
			'ay'  => 'ayr', // aymara
			'co'  => 'fr', // corse
			'cpf' => 'fr', // créole et pidgins (rcf)
			'fon' => '', // fongbè
			'roa' => 'pdc', // langues romanes
		);
		if (!empty($exceptions[$locale])) {
			$locale = $exceptions[$locale];
		}
	}

	return $locale;
}

/**
 * Fonction privée pour filtrer le tableau d'options du formatter
 *
 * Retire les options inconnues et typecaste les valeurs pour éviter les exceptions invalid argument.
 *
 * Le tableau d'options peut être issu d'un squelette,
 * et dans ce cas par défaut les valeurs sont des chaînes de texte à défaut de |filtre ou de #EVAL.
 *
 * @param array $valeurs
 * @return array
 */
function prix_filtrer_options_formater($options) {
	$options_valides = array(
		'locale',
		'style',
		'use_grouping',
		'rounding_mode',
		'minimum_fraction_digits',
		'maximum_fraction_digits',
		'currency_display',
	);
	foreach ($options as $k => $v) {
		// option inconnue, chaine vide ou null : on retire la valeur
		if (!in_array($k, $options_valides) or is_null($v) or $v == '') {
			unset($options[$k]);
			// nombre flottant / entier
		} elseif (is_numeric($v)) {
			if (intval($v) == $v) {
				$options[$k] = intval($v);
			} else {
				$options[$k] = floatval($v);
			}
		// booléens
		} elseif (in_array($v, array('true', 'oui'))) {
			$options[$k] = true;
		} elseif (in_array($v, array('false', 'non'))) {
			$options[$k] = false;
		}
	}
	return $options;
}

/**
 * Fonction privée pour changer les alias dans le tableau d'option du formatter
 *
 * @param array $options
 * @return array
 */
function prix_alias_options_formater($options) {
	$options_alias = array(
		'currency' => 'devise',
		'float'    => 'flottant',
	);
	foreach ($options as $k => $v) {
		foreach ($options_alias as $option => $alias) {
			if ($k == $alias) {
				$options[$option] = $v;
				unset($options[$k]);
				break;
			}
		}
	}
	return $options;
}

/**
 * Autoloader
 * @throws Exception
 */
function prix_loader() {
	static $done = false;
	if (!$done) {
		$done = true;
		require_once __DIR__ . '/vendor/autoload.php';
	}
}
