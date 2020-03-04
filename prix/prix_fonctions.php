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
 * @uses prix_langue_defaut
 *
 * @param Float $prix
 *     Valeur du prix à formater
 * @param String $devise
 *     Code alphabétique à 3 lettres de la devise
 * @param String $langue
 *     Code de langue
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
function filtres_prix_formater_dist($prix, $devise = '', $langue = '', $options = array()) {
	prix_loader();

	$prix = floatval($prix);

	// Devise à utiliser
	$devise = $devise ?: prix_devise_defaut();

	// Langue à utiliser
	$langue = $langue ?: prix_langue_defaut();

	// Options : langue, style, etc.
	$options_formatter = array(
		'locale'           => $langue,
		'currency_display' => 'code', // pour l'accessibilité
	);
	if (is_array($options)) {
		$options_formatter = array_merge($options_formatter, $options);
	}

	// Définitions des formats numériques depuis resources/numberFormat.
	$numberFormatRepository = new CommerceGuys\Intl\NumberFormat\NumberFormatRepository;
	// Définitions des devises depuis resources/currency.
	$currencyRepository = new CommerceGuys\Intl\Currency\CurrencyRepository;
	// Formatage des devises
	$currencyFormatter = new CommerceGuys\Intl\Formatter\CurrencyFormatter($numberFormatRepository, $currencyRepository, $options_formatter);
	$prix_formate = $currencyFormatter->format($prix, $devise);

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
	$langue = $GLOBALS['spip_lang'];
	$langue = substr($langue, strpos($langue, '_'));

	foreach ($codes_devises as $code => $nom) {
		$devise = $currencyRepository->get($code, $langue);
		$devises[$code] = array(
			'code'     => $code, // ça ne mange pas de pain de le remettre
			'code_num' => $devise->getNumericCode(),
			'nom'      => $devise->getName(),
			'fraction' => $devise->getFractionDigits(),
			'symbole'  => $devise->getSymbol(),
			'langue'   => $devise->getLocale(),
		);
	}

	return $devises;
}

/**
 * Liste toutes les langues avec leur code ISO.
 *
 * @note
 * Les codes de langues peuvent différer de ceux de SPIP, qui ne sont pas ISO
 *
 * @return Array
 *     Tableau associatif : code => nom
 */
function prix_lister_langues() {

	prix_loader();
	$langues = array();

	// Définitions des langues depuis resources/language.
	$languageRepository = new CommerceGuys\Intl\Language\LanguageRepository;
	$codes_langues = $languageRepository->getlist();

	// Prendre la langue de l'utilisateur pour les noms
	$langue = $GLOBALS['spip_lang'];
	$langue = substr($langue, strpos($langue, '_'));

	foreach ($codes_langues as $code => $nom) {
		$language = $languageRepository->get($code, $langue);
		$langues[$code] = $language->getName();
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
 * Retourne le code de langue par défaut.
 *
 * Celle configurée, sinon celle du site, sinon du français de france.
 *
 * @return String
 *      Code de langue
 */
function prix_langue_defaut() {

	include_spip('inc/config');

	// Par défaut on prend celle configurée
	if ($langue_config = lire_config('prix/langue_defaut')) {
		$langue = $langue_config;
	// Sinon on prend la langue du site (possiblement moins précise).
	// Il ne faut que les premières lettres pour être sûr
	// d'avoir quelque chose d'exploitable (fr_fem → fr).
	} elseif ($langue_site = lire_config('langue_site')) {
		$langue = substr($langue_site, strpos($langue_site, '_'));
	// En dernier recours, du français de france
	} else {
		$langue = 'fr-FR';
	}

	return $langue;
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
