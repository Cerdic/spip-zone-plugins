<?php
/**
 * Ce fichier contient l'ensemble des constantes et des utilitaires nécessaires au fonctionnement du plugin.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


$GLOBALS['_taxonomie']['regnes'] = array('animalia', 'plantae', 'fungi');

if (!defined('_TAXONOMIE_RANG_TYPE_PRINCIPAL')) {
	/**
	 * Type de rang selon la nomenclature taxonomique.
	 */
	define('_TAXONOMIE_RANG_TYPE_PRINCIPAL', 'principal');
}
if (!defined('_TAXONOMIE_RANG_TYPE_SECONDAIRE')) {
	/**
	 * Type de rang selon la nomenclature taxonomique.
	 */
	define('_TAXONOMIE_RANG_TYPE_SECONDAIRE', 'secondaire');
}
if (!defined('_TAXONOMIE_RANG_TYPE_INTERCALAIRE')) {
	/**
	 * Type de rang selon la nomenclature taxonomique.
	 */
	define('_TAXONOMIE_RANG_TYPE_INTERCALAIRE', 'intercalaire');
}

// TODO : vérifier les rangs stirp, morph, aberration, unspecified.
// TODO : vérifier pourquoi le rang serie n'est pas dans la liste de ITIS
$GLOBALS['_taxonomie']['rangs'] = array(
	'kingdom'       => array('type' => _TAXONOMIE_RANG_TYPE_PRINCIPAL, 'est_espece' => false, 'synonyme' => ''),
	'subkingdom'    => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'infrakingdom'  => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'superphylum'   => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'superdivision'),
	'phylum'        => array('type' => _TAXONOMIE_RANG_TYPE_PRINCIPAL, 'est_espece' => false, 'synonyme' => 'division'),
	'subphylum'     => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'subdivision'),
	'infraphylum'   => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'infradivision'),
	'superdivision' => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'superphylum'),
	'division'      => array('type' => _TAXONOMIE_RANG_TYPE_PRINCIPAL, 'est_espece' => false, 'synonyme' => 'phylum'),
	'subdivision'   => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'subphylum'),
	'infradivision' => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => 'infraphylum'),
	'superclass'    => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'class'         => array('type' => _TAXONOMIE_RANG_TYPE_PRINCIPAL, 'est_espece' => false, 'synonyme' => ''),
	'subclass'      => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'infraclass'    => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'superorder'    => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'order'         => array('type' => _TAXONOMIE_RANG_TYPE_PRINCIPAL, 'est_espece' => false, 'synonyme' => ''),
	'suborder'      => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'infraorder'    => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'section'       => array('type' => _TAXONOMIE_RANG_TYPE_SECONDAIRE, 'est_espece' => false, 'synonyme' => ''),
	'subsection'    => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'superfamily'   => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'family'        => array('type' => _TAXONOMIE_RANG_TYPE_PRINCIPAL, 'est_espece' => false, 'synonyme' => ''),
	'subfamily'     => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'tribe'         => array('type' => _TAXONOMIE_RANG_TYPE_SECONDAIRE, 'est_espece' => false, 'synonyme' => ''),
	'subtribe'      => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'genus'         => array('type' => _TAXONOMIE_RANG_TYPE_PRINCIPAL, 'est_espece' => false, 'synonyme' => ''),
	'subgenus'      => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => false, 'synonyme' => ''),
	'species'       => array('type' => _TAXONOMIE_RANG_TYPE_PRINCIPAL, 'est_espece' => true, 'synonyme' => ''),
	'subspecies'    => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'variety'       => array('type' => _TAXONOMIE_RANG_TYPE_SECONDAIRE, 'est_espece' => true, 'synonyme' => ''),
	'subvariety'    => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'form'          => array('type' => _TAXONOMIE_RANG_TYPE_SECONDAIRE, 'est_espece' => true, 'synonyme' => ''),
	'subform'       => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'race'          => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => true, 'synonyme' => 'variety'),
	'stirp'         => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'morph'         => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'aberration'    => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => true, 'synonyme' => ''),
	'unspecified'   => array('type' => _TAXONOMIE_RANG_TYPE_INTERCALAIRE, 'est_espece' => true, 'synonyme' => '')
);

if (!defined('_TAXONOMIE_RANG_REGNE')) {
	/**
	 * Nom anglais du rang principal `règne`.
	 */
	define('_TAXONOMIE_RANG_REGNE', 'kingdom');
}
if (!defined('_TAXONOMIE_RANG_GENRE')) {
	/**
	 * Nom anglais du rang principal `genre`.
	 */
	define('_TAXONOMIE_RANG_GENRE', 'genus');
}
if (!defined('_TAXONOMIE_RANG_ESPECE')) {
	/**
	 * Nom anglais du rang principal `espèce`.
	 */
	define('_TAXONOMIE_RANG_ESPECE', 'species');
}

if (!defined('_TAXONOMIE_LANGUES_POSSIBLES')) {
	/**
	 * Liste des langues utilisables pour les noms communs et les textes des taxons.
	 */
	define('_TAXONOMIE_LANGUES_POSSIBLES', 'fr:en:es:pt:de:it');
}


/**
 * Renvoie la liste des règnes supportés par le plugin.
 *
 * @package SPIP\TAXONOMIE\REGNE
 *
 * @api
 *
 * @return array
 *        Liste des noms scientifiques en minuscules des règnes supportés.
 */
function regne_lister() {

	return $GLOBALS['_taxonomie']['regnes'];
}


/**
 * Renvoie le type de rang principal, secondaire ou intercalaire.
 *
 * @package SPIP\TAXONOMIE\RANG
 *
 * @api
 *
 * @param string $rang
 *        Nom anglais du rang en minuscules.
 *
 * @return string
 *        `principal`, `secondaire` ou `intercalaire` si le rang est valide, chaine vide sinon.
 */
function rang_informer_type($rang) {

	// Initialisation à chaine vide pour le cas où le rang n'est pas dans la liste des rangs admis.
	$type = '';

	if (!empty($GLOBALS['_taxonomie']['rangs'][$rang])) {
		$type = $GLOBALS['_taxonomie']['rangs'][$rang]['type'];
	}

	return $type;
}


/**
 * Détermine si un rang est celui d'une espèce ou d'un taxon de rang inférieur.
 *
 * @package SPIP\TAXONOMIE\RANG
 *
 * @api
 *
 * @param string $rang
 *        Nom anglais du rang en minuscules.
 *
 * @return bool
 *        `true` si le rang est celui d'une espèce ou d'un taxon de rang inférieur, `false` sinon.
 */
function rang_est_espece($rang) {

	// Initialisation à false pour le cas où le rang n'est pas dans la liste des rangs admis.
	$est_espece = false;

	if (!empty($GLOBALS['_taxonomie']['rangs'][$rang])) {
		$est_espece = $GLOBALS['_taxonomie']['rangs'][$rang]['est_espece'];
	}

	return $est_espece;
}


/**
 * Extrait, de la table `spip_taxons`, la liste des taxons d'un règne donné ayant fait l'objet
 * d'une modification manuelle.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 *
 * @param string $regne
 *        Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 *
 * @return array
 *        Liste des taxons modifiées manuellement. Chaque élément de la liste est un tableau
 *        composé des index `tsn`, `nom_commun`, `descriptif`.
 */
function taxon_preserver_editions($regne) {

	$select = array('tsn', 'nom_commun', 'descriptif');
	$where = array('regne=' . sql_quote($regne), 'edite=' . sql_quote('oui'));
	$taxons = sql_allfetsel($select, 'spip_taxons', $where);

	return $taxons;
}


/**
 * Fusionne les traductions d'une balise `<multi>` avec celles d'une autre balise `<multi>`.
 * L'une des balise est considérée comme prioritaire ce qui permet de régler le cas où la même
 * langue est présente dans les deux balises.
 * Si on ne trouve pas de balise `<multi>` dans l'un ou l'autre des paramètres, on considère que
 * le texte est tout même formaté de la façon suivante : texte0[langue1]texte1[langue2]texte2...
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 *
 * @param string $multi_prioritaire
 *        Balise multi considérée comme prioritaire en cas de conflit sur une langue.
 * @param string $multi_non_prioritaire
 *        Balise multi considérée comme non prioritaire en cas de conflit sur une langue.
 *
 * @return string
 *        La chaine construite est toujours une balise `<multi>` complète ou une chaine vide sinon.
 */
function taxon_merger_traductions($multi_prioritaire, $multi_non_prioritaire) {

	$multi_merge = '';

	// On extrait le contenu de la balise <multi> si elle existe.
	include_spip('inc/filtres');
	$multi_prioritaire = trim($multi_prioritaire);
	$multi_non_prioritaire = trim($multi_non_prioritaire);
	if (preg_match(_EXTRAIRE_MULTI, $multi_prioritaire, $match)) {
		$multi_prioritaire = trim($match[1]);
	}
	if (preg_match(_EXTRAIRE_MULTI, $multi_non_prioritaire, $match)) {
		$multi_non_prioritaire = trim($match[1]);
	}

	if ($multi_prioritaire) {
		if ($multi_non_prioritaire) {
			// On extrait les traductions sous forme de tableau langue=>traduction.
			$traductions_prioritaires = extraire_trads($multi_prioritaire);
			$traductions_non_prioritaires = extraire_trads($multi_non_prioritaire);

			// On complète les traductions prioritaires avec les traductions non prioritaires dont la langue n'est pas
			// présente dans les traductions prioritaires.
			foreach ($traductions_non_prioritaires as $_lang => $_traduction) {
				if (!array_key_exists($_lang, $traductions_prioritaires)) {
					$traductions_prioritaires[$_lang] = $_traduction;
				}
			}

			// On construit le contenu de la balise <multi> mergé à partir des traductions prioritaires mises à jour.
			// Les traductions vides sont ignorées.
			ksort($traductions_prioritaires);
			foreach ($traductions_prioritaires as $_lang => $_traduction) {
				if ($_traduction) {
					$multi_merge .= ($_lang ? '[' . $_lang . ']' : '') . trim($_traduction);
				}
			}
		} else {
			$multi_merge = $multi_prioritaire;
		}
	} else {
		$multi_merge = $multi_non_prioritaire;
	}

	// Si le contenu est non vide on l'insère dans une balise <multi>
	if ($multi_merge) {
		$multi_merge = '<multi>' . $multi_merge . '</multi>';
	}

	return $multi_merge;
}


/**
 * Traduit un champ de la table `spip_taxons` dans la langue du site.
 *
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @api
 *
 * @param string $champ
 *        Nom du champ dans la base de données.
 *
 * @return string
 *        Traduction du champ dans la langue du site.
 */
function taxon_traduire_champ($champ) {

	$traduction = '';
	if ($champ) {
		$traduction = _T("taxon:champ_${champ}_label");
	}

	return $traduction;
}
