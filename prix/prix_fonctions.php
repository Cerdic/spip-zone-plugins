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
 * Formater un nombre pour l'afficher comme un prix avec une devise
 *
 * @uses filtres_prix_formater_dist
 */
function prix_formater($prix, $devise = '', $langue = '', $options = array()) {
	$fonction_formater = charger_fonction('prix_formater', 'filtres/');
	return $fonction_formater($prix, $devise, $langue, $options);
}

/**
 * Formater un nombre pour l'afficher comme un prix avec une devise
 *
 * Déport de la fonction `prix_formater`.
 * Fonction surchargeable avec `function filtres_prix_formater`.
 *
 * @see https://github.com/commerceguys/intl/blob/master/src/Formatter/CurrencyFormatterInterface.php#L8
 *
 * @uses prix_devise_defaut
 * @uses prix_locale_defaut
 *
 * @param Float $prix
 *     Valeur du prix à formater
 * @param String $devise
 *     Code alphabétique à 3 lettres de la devise
 * @param String $locale
 *     Identifiant d'une locale (fr-CA) ou code de langue spip (fr_tu)
 * @param Array $options
 *     Tableau d'options :
 *     - style :                   (String) standard | accounting.
 *                                 Défaut : standard
 *     - use_grouping :            (Bool) grouper les séparateur.
 *                                 Défaut : true
 *     - rounding_mode :           constante PHP_ROUND_ ou `none`.
 *                                 Défaut : PHP_ROUND_HALF UP
 *     - minimum_fraction_digits : (Int)
 *                                 Défaut : fraction de la devise.
 *     - maximum_fraction_digits : (Int)
 *                                 Défaut : fraction de la devise.
 *     - currency_display :        (String) symbol | code | none.
 *                                 Défaut : symbol
 * @return String
 *     Retourne une chaine contenant le prix formaté avec une devise
 */
function filtres_prix_formater_dist($prix, $devise = '', $locale = '', $options = array()) {
	prix_loader();

	$prix = floatval(str_replace(',', '.', $prix));
	$prix_formate = $prix;

	// Devise à utiliser
	$devise = $devise ?: prix_devise_defaut();

	// Locale à utiliser
	$locale = $locale ?: prix_locale_defaut();

	// De préférence, on utilise la librairie Intl de Commerceguys
	if (extension_loaded('bcmath')) {
		// Options : langue, style, etc.
		$options_formatter = array(
			'locale'           => $locale,
			'currency_display' => 'code', // pour l'accessibilité
		);
		if (is_array($options)) {
			$options_formatter = array_merge($options_formatter, $options);
		}

		$numberFormatRepository = new CommerceGuys\Intl\NumberFormat\NumberFormatRepository;
		$currencyRepository = new CommerceGuys\Intl\Currency\CurrencyRepository;
		$currencyFormatter = new CommerceGuys\Intl\Formatter\CurrencyFormatter($numberFormatRepository, $currencyRepository, $options_formatter);
		$prix_formate = $currencyFormatter->format($prix, $devise);

	// Sinon on se rabat sur la librairie Intl de php
	} elseif (extension_loaded('intl')) {
		$formatter = new NumberFormatter( $locale, NumberFormatter::CURRENCY );
		$prix_formate = numfmt_format_currency($formatter, $prix, $devise);

	// Sinon, on fait le minimum syndical
	} else {
		$prix_formate = str_replace('.', ',', $prix) . '&nbsp;' . $devise;
	}

	return $prix_formate;
}

/**
 * Liste les devises et les informations associées
 *
 * @return Array
 *     Tableau associatif avec les codes alphabétiques en clés, et des sous-tableaux :
 *     - nom : nom de la devise
 *     - code : code alphabétique (répété au cas où)
 *     - code_num : code numérique
 *     - symbole : symbole associé
 *     - fraction : fraction pour passer à l'unité inférieure (centimes et cie)
 *     - langue : code de langue utilisée
 */
function prix_lister_devises() {

	prix_loader();
	$devises = array();

	// Définitions des devises depuis resources/currency.
	$currencyRepository = new CommerceGuys\Intl\Currency\CurrencyRepository;
	$codes_devises = $currencyRepository->getList();

	// On veut la langue de l'utilisateur pour les noms
	$langue_spip = $GLOBALS['spip_lang'];
	$locale = substr($langue_spip, strpos($langue_spip, '_'));

	foreach ($codes_devises as $code => $nom) {
		$devise = $currencyRepository->get($code, $locale);
		$devises[$code] = array(
			'code'     => $code, // ça ne mange pas de pain de le remettre
			'code_num' => $devise->getNumericCode(),
			'nom'      => $devise->getName(),
			'fraction' => $devise->getFractionDigits(),
			'symbole'  => $devise->getSymbol(),
			'locale'   => $devise->getLocale(),
		);
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

	// Prendre la langue de l'utilisateur pour les noms
	$langue_spip = $GLOBALS['spip_lang'];
	$locale_utilisateur = prix_langue_vers_locale($langue_spip);

	foreach ($repo_locales as $locale => $nom) {
		$language = $languageRepository->get($locale, $locale_utilisateur);
		$langues[$locale] = $language->getName();
	}

	return $langues;
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

	// Normalement l'admin a configuré la locale correspondante à chaque code langue de spip
	if (!empty($locales_config[$langue_spip])) {
		$locale = $locales_config[$langue_spip];
	// Sinon tant pis, on donne juste le code pays tiré du code langue de spip
	} else {
		$locale = prix_langue_vers_locale($langue_spip);
	}

	return $locale;
}

/**
 * Donne la locale correspondante un code langue de SPIP pour le formatage des prix.
 *
 * L'objectif est d'obtenir une locale qui fait partie de la liste prise en charge par Intl
 *
 * On extrait le code pays afin d'obtenir la locale "générale" (norme ISO 639).
 * Il s'agit des 2 à 3 lettres précédentes l'underscore : fr_tu → fr.
 *
 * @see https://github.com/commerceguys/intl/blob/master/src/Language/LanguageRepository.php#L46
 * @see https://blog.smellup.net/106
 *
 * @param string $langue_spip
 * @return string
 */
function prix_langue_vers_locale($langue_spip) {

	// Extraire le code pays pour avoir la locale "générale" : fr_tu → fr
	$locale = strtolower(strtok($langue_spip, '_'));

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

	return $locale;
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
