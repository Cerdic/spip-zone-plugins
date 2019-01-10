<?php
/**
 * Fonctions utiles au plugin Prix Objets
 *
 * @plugin     Prix Objets
 * @copyright  2012 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Promotions_commandes\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('base/abstract_sql');

/**
 * Un tableau des devises dispoibles.
 *
 * @return array
 */
function devises() {
	$devises = array(

		// A
		'AUD' => 'AUD',

		// B
		'BRL' => 'Real',

		// C
		'CAD' => 'CAD',
		'CHF' => 'CHF',
		'CNY' => 'Yuan',
		'CSD' => 'CSD',
		'CZK' => 'CZK',

		// D
		'DKK' => 'DKK',

		// E
		'EUR' => '€',

		// G
		'GBP' => '£',

		// H
		'HKD' => 'HKD',
		'HUF' => 'HUF',

		// I
		'IDR' => 'IDR',
		'ILS' => 'Shekel',
		'IQD' => 'IQD',
		'IRR' => 'IRR',
		'ISK' => 'ISK',

		// J
		'JEP' => 'JEP',
		'JOD' => 'JOD',
		'JMD' => 'JMD',
		'JPY' => '¥',

		// K
		'KES' => 'KES',
		'KGS' => 'KGS',
		'KWD' => 'KWD',
		'KZT' => 'Tenge',

		// L
		'LAK' => 'Kip',
		'LBP' => 'LBP',
		'LKR' => 'LKR',
		'LRD' => 'LRD',
		'LTL' => 'Litas',
		'LVL' => 'Lat',

		// M
		'MAD' => 'Dirham',
		'MDL' => 'MDL',
		'MGA' => 'Ariary',
		'MKD' => 'MKD',
		'MNT' => 'Tughrik',
		'MRO' => 'Ouguiya',
		'MUR' => 'MUR',
		'MVR' => 'Rufiyaa',
		'MWK' => 'MWK',
		'MXN' => 'MXN',
		'MYR' => 'Ringgit',
		'MZN' => 'Metical',

		// N
		'NAD' => 'NAD',
		'NGN' => 'Naira',
		'NIO' => 'Cordoba',
		'NPR' => 'NPR',
		'NOK' => 'NOK',
		'NZD' => 'NZD',

		// O
		'OMR' => 'OMR',

		'QAR' => 'Riyal',

		// P
		'PGK' => 'Kina',
		'PHP' => 'PHP',
		'PKR' => 'PKR',
		'PLN' => 'Zloty',

		'RON' => 'RON',
		'RUB' => 'Rouble',
		'RWF' => 'RWF',

		// S
		'SCR' => 'SCR',
		'SDD' => 'SDD',
		'SEK' => 'SEK',
		'SGD' => 'SGD',
		'SOS' => 'SOS',
		'SLL' => 'Leone',
		'SRD' => 'SRD',
		'STD' => 'Dobra',
		'SVC' => 'Colon',
		'SYP' => 'SYP',

		// T
		'THB' => 'Baht',
		'TJS' => 'Somoni',
		'TND' => 'TND',
		'TMM' => 'TMM',
		'TRY' => 'Lirasi',
		'TTD' => 'TTD',
		'TWD' => 'TWD',
		'TZS' => 'TZS',

		// U
		'UAH' => 'Hryvna',
		'UGX' => 'UGX',
		'USD' => 'USD',
		'UZS' => 'UZS',

		// V
		'VND' => 'Dong',

		// X
		'XAF' => 'XAF',
		'XOF' => 'XOF',

		// Y
		'YER' => 'Rial',

		// Z
		'ZMK' => 'ZMK',
		'ZWN' => 'ZWN'
	);

	return $devises;
}

/**
 * Affiche le symbole de la devise si disponible
 *
 * @param string $code_devise
 * @return string
 */
function traduire_devise($code_devise) {
	include_spip('inc/devises');

	$devises = devises();
	$trad = $devises[$code_devise];

	return $trad;
}

function prix_defaut($id_objet, $objet = 'article') {
	if ($_COOKIE['spip_devise']) {
		$devise_defaut = $_COOKIE['spip_devise'];
	}
	elseif (lire_config('prix_objets/devise_default')) {
		$devise_defaut = lire_config('prix_objets/devise_default');
	}
	else {
		$devise_defaut = 'EUR';
	}

	$req = sql_select('code_devise,prix', 'spip_prix_objets', 'id_objet=' . $id_objet . ' AND objet=' . sql_quote($objet));
	while ($row = sql_fetch($req)) {
		$prix = $row['prix'] . ' ' . traduire_devise($row['code_devise']);
		if ($row['code_devise'] == $devise_defaut) {
			$defaut = $row['prix'] . ' ' . traduire_devise($row['code_devise']);
		}
	}

	if ($defaut) {
		$defaut = $defaut;
	}
	else {
		$defaut = $prix;
	}

	return $defaut;
}

function devise_defaut_prix($prix = '', $traduire = true) {
	if ($_COOKIE['spip_devise']) {
		$devise_defaut = $_COOKIE['spip_devise'];
	}
	else {
		$devise_defaut = $devise_defaut = prix_objets_devise_defaut();
	}
	$devise_defaut = traduire_devise($devise_defaut);

	if ($prix) {
		$devise_defaut = $prix . ' ' . $devise_defaut;
	}

	return $devise_defaut;
}

function devise_defaut_objet($id_objet, $objet = 'article') {
	include_spip('inc/config');
	$config = lire_config('prix_objets');

	if (!$devise_defaut = $_COOKIE['devise_selectionnee']) {
		$devise_defaut = $config['devise_default'];
	}
	else {
		$devise_defaut = prix_objets_devise_defaut($config);
	}

	$req = sql_select('code_devise,prix', 'spip_prix_objets', 'id_objet=' . $id_objet . ' AND objet=' . sql_quote($objet));

	while ($row = sql_fetch($req)) {
		$prix = $row['prix'] . ' ' . traduire_devise($row['code_devise']);
		if ($row['code_devise'] == $devise_defaut) {
			$defaut = $row['code_devise'];
		}
	}

	if ($defaut) {
		$defaut = $defaut;
	}
	else {
		$defaut = $prix;
	}

	return $defaut;
}

function traduire_code_devise($code_devise, $id_objet, $objet = 'article', $option = "") {
	$prix = sql_getfetsel('prix', 'spip_prix_objets', 'id_objet=' . $id_objet . ' AND objet=' . sql_quote($objet) . ' AND code_devise =' . sql_quote($code_devise));

	if ($option == 'prix') {
		$prix = $prix . ' ' . traduire_devise($code_devise);
	}

	return $prix;
}

function rubrique_prix($id = '', $objet = 'article', $sousrubriques = false) {
	include_spip('inc/config');
	include_spip('prive/formulaires/selecteur/generique_fonctions');

	$rubrique_produit = picker_selected(lire_config('prix_objets/rubrique_prix', array()), 'rubrique');

	if ($rubrique_produit) {
		$id_parent = $rubrique_produit;

		if (!$sousrubriques) {
			$rubriques = $id_parent;
		}
		else {
			$rubriques = array();
		}

		$rubriques = rubriques_enfant($id_parent, $rubriques);
		if ($id) {
			$retour = sql_getfetsel('id_' . $objet, 'spip_' . $objet . 's', 'id_' . $objet . '=' . $id . ' AND id_rubrique IN (' . implode(',', $rubriques) . ')');
		}
		else {
			$retour = $rubriques;
		}
	}
	else {
		return false;
	}

	return $retour;
}

function rubriques_enfant($id_parent, $rubriques = array()) {
	$id_p = '';

	if (is_array($id_parent)) {
		$id_parent = implode(',', $id_parent);
	}

	if ($id_parent) {
		$sql = sql_select('id_rubrique', 'spip_rubriques', 'id_parent IN (' . $id_parent . ')');
	}

	$id_p = array();
	while ($row = sql_fetch($sql)) {
		$id_p[] = $row['id_rubrique'];
		$rubriques[] = $row['id_rubrique'];
	}

	if (count($id_p) > 0) {
		$rubriques = rubriques_enfant($id_p, $rubriques);
	}

	return $rubriques;
}

/**
 * Surcharge de la fonction filtres_prix_formater_dist du plugin prix.
 * Formate le prix en y ajoutant la devise.
 *
 * @param string $prix
 * @param string $devise
 * @return string
 */
function filtres_prix_formater($prix, $devise = '') {
	include_spip('inc/config');
	include_spip('inc/cookie');

	$config = lire_config('prix_objets');

	if (!$devise) {
		$devises = isset($config['devises']) ? $config['devises'] : array();

		// Si il y a un cookie 'devise_selectionnee' et qu'il figure parmis les devises disponibles on le prend
		if (isset($_COOKIE['devise_selectionnee']) and in_array($_COOKIE['devise_selectionnee'], $devises)) {
			$devise = $_COOKIE['devise_selectionnee'];
			$GLOBALS['devise_defaut'] = $devise;
		} // Sinon on regarde si il ya une devise defaut valable
		else {
			$devise = prix_objets_devise_defaut($config);
		}
	}

	// On met le cookie
	spip_setcookie('devise_selectionnee', $devise, time() + 3660 * 24 * 365, '/');

	// On détermine la langue du contexte
	$lang = $GLOBALS['spip_lang'];


	// Si PECL intl est présent on dermine le format de l'affichage de la devise selon la langue du contexte
	if (function_exists('numfmt_create') and is_float($prix)) {
		$fmt = numfmt_create($lang, NumberFormatter::CURRENCY);
		$prix = numfmt_format_currency($fmt, $prix, $devise);
	} // Sinon à la française
	else {
		$prix = $prix . '&nbsp;' . traduire_devise($devise);
	}

	return $prix;
}

/**
 * Détermine la devise par défaut
 *
 * @param array $config
 *        	Les donnes de configuration de prix_objets
 * @return string Code de la devise
 */
function prix_objets_devise_defaut($config = '') {
	if (!$config) {
		include_spip('inc/config');
		$config = lire_config('prix_objets');
	}
	$devises = isset($config['devises']) ? $config['devises'] : array();
	// Sinon on regarde si il ya une devise defaut valable
	if ($config['devise_default']) {
		$devise_defaut = $config['devise_default'];
	} // Sinon on prend la première des devises choisies
	elseif (isset($devises[0])) {
		$devise_defaut = $devises[0];
	} // Sinon on met l'Euro
	else {
		$devise_defaut = 'EUR';
	}

	return $devise_defaut;
}

/**
 * Donne le prix pour un objet
 *
 * @param string $objet
 *        	Objet dont on cherche le prix
 * @param string $id_objet
 *        	Identifiant de l'objet dont on cherche le prix
 * @param array $contexte
 *        	Les variables de l'environnement utilisées dans le calcul du prix.
 * @param string  $type
 *          prix (ttc) ou prix_ht
 * @param array $options
 *
 * @return string Le prix applicable.
 */
function prix_par_objet($objet, $id_objet, $contexte, $type = 'prix_ht', $options = array()) {
	$prix = 0;

	if ($type == 'prix_ht') {
		$fonction_prix = charger_fonction('ht', 'inc/prix');
	}
	else {
		$fonction_prix = charger_fonction('prix', 'inc');
	}

	// Le mode de calcul de prix, ou passé dans les options, ou depuis la config.
	if (isset($options['mode']) and !empty($options['mode'])) {
		$mode = $options['mode'];
	}
	else {
		include_spip('inc/config');
		$mode = lire_config('prix_objets/prix_par_objet_mode', 'global');
	}

	if ($mode == 'prorata') {
		$horaire = isset($options['horaire']) ? $options['horaire'] : '';
		$format = isset($options['date_format']) ? $options['date_format'] : '';
		$sequence = isset($options['sequence']) ? $options['sequence'] : '';

		if (!$sequence) {

			// Séquence composé de dates.
			if (isset($contexte['date_debut']) and
					isset($contexte['date_fin']) and
					include_spip('filtres/dates_outils') and
					function_exists('dates_intervalle')) {

						$sequence = dates_intervalle($contexte['date_debut'], $contexte['date_fin'], 0, -1, $horaire, $format);
					}
					else {
						$sequence = array();
					}
		}

		$nr_elements_sequence = count($sequence);
		$contexte['date_fin'] = $contexte['date_debut'];
	}
	else {
		$nr_elements_sequence = 0;
	}

	$prix_source = sql_allfetsel(
			'id_prix_objet,prix_total,titre',
			'spip_prix_objets',
			'id_prix_objet_source=0 AND objet LIKE ' . sql_quote(trim($objet)) . ' AND id_objet=' . $id_objet, '',
			array(
				'rang_lien',
				'titre',
				'prix_ht'
			)
			);


	// On parcours les extension pour chaque prix principal.
	$prix_elements = array();
	foreach ($prix_source as $index => $data_source) {

		$id_prix_objet = $data_source['id_prix_objet'];

		// passer l'info sur le prix total dans l'environnement
		set_request('prix_total', $data_source['prix_total']);

		if (!$extensions = sql_allfetsel(
			'extension,id_extension,titre',
			'spip_prix_objets',
			'id_prix_objet_source=' . $id_prix_objet)) {
			$extensions = [];
		}

		$prix = $fonction_prix('prix_objet', $id_prix_objet);
		$count_sextensions = count($extensions);

		// Si il y a des extensions.
		if ($count_sextensions > 0) {
			$i = 0;
			$applicables = array();
			$dates_applicables = array();

			// On établit l'extension qui définit le prix.
			foreach ($extensions as $data_extension) {
				$i ++;
				$id_extension = $data_extension['id_extension'];

				if ($extension = charger_fonction($data_extension['extension'], 'prix_objet/', TRUE)) {
					switch ($mode) {
						// Si global on met les resultats positiv dans un simple tableau.
						case 'global':
							if ($applicable = $extension($id_extension, $contexte, $mode)) {
								$applicables[] = $applicable;
							}
							break;
							// Si prorata on détermine quels éléments de séquences sont applicables.
						case 'prorata':
							if (is_array($sequence)) {
								foreach ($sequence as $index => $element) {
									$contexte['date_debut'] = $element;
									$contexte['date_fin'] = $element;

									if ($applicable = $extension($id_extension, $contexte, $mode)) {
										$dates_applicables[$element][$index][] = $applicable;
									}
								}
							}
							break;
					}
				}
				else {
					$applicables[] = 1;
				}
			}

			// mode de calcul global
			if ($mode == 'global') {
				// On choisit le premier prix qui est applicable pour chaque extension.
				if (count($applicables) == $count_sextensions) {
					break;
				}
			}
			// mode de calcul prorata
			elseif ($mode == "prorata" and is_array($dates_applicables)) {
				// On établit un prix pour éléement de séquence, puis on enlève l'élément de la séquence.
				foreach ($dates_applicables as $element => $applicables) {
					foreach ($applicables as $index => $counter) {
						if (array_sum($counter) >= $count_sextensions) {
							$prix_elements[$element] = $prix;
							unset($sequence[$index]);
						}
					}
				}
			}
		}
	}

	// Si mode prorata on calcule le prix à partir des prix par élément
	if ($mode == "prorata") {
		$nr_prix_prorata = count($prix_elements);
		$sum_prix_prorata = array_sum($prix_elements);

		// Si on a un prix pour chaque élément de séquence
		if ($nr_prix_prorata == $nr_elements_sequence) {
			// Si il y a des éléments de séquence, on divise la somme par le nombre d'élément.
			if ($nr_elements_sequence > 0) {
				$prix = $sum_prix_prorata / $nr_elements_sequence;
			}
		}
		// Sinon on divise la somme par le nombre de prix, on ajoute le prix par défaut (le dernier)
		// et on divise par deux
		elseif ($nr_prix_prorata > 0) {
			$prix = (($sum_prix_prorata / $nr_prix_prorata) + $prix) / 2;
		}
	}

	// Permettre d'intervenir sur le prix
	return pipeline('prix_par_objet', array(
		'data' => $prix,
		'args' => array(
			'objet' => $objet,
			'id_objet' => $id_objet,
			'contexte' => $contexte,
			'type' => $type,
			'options' => $options
		)
	));
}
