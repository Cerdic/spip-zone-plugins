<?php
/**
 * Ce fichier contient l'ensemble des constantes et des utilitaires nécessaires au fonctionnement du plugin.
 *
 * @package SPIP\TAXONOMIE\TAXON
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


if (!defined('_TAXONOMIE_REGNE_ANIMAL')) {
	/**
	 * Nom latin du règne animal (animaux).
	 */
	define('_TAXONOMIE_REGNE_ANIMAL', 'animalia');
}
if (!defined('_TAXONOMIE_REGNE_VEGETAL')) {
	/**
	 * Nom latin du règne végétal (plantes).
	 */
	define('_TAXONOMIE_REGNE_VEGETAL', 'plantae');
}
if (!defined('_TAXONOMIE_REGNE_FONGIQUE')) {
	/**
	 * Nom latin du règne fongique (champignons).
	 */
	define('_TAXONOMIE_REGNE_FONGIQUE', 'fungi');
}
if (!defined('_TAXONOMIE_REGNES')) {
	/**
	 * Liste des règnes supportés par le plugin (concanétation des noms séparés par le signe deux-points).
	 */
	define('_TAXONOMIE_REGNES',
		_TAXONOMIE_REGNE_ANIMAL . ':' .
		_TAXONOMIE_REGNE_VEGETAL . ':' .
		_TAXONOMIE_REGNE_FONGIQUE
	);
}


if (!defined('_TAXONOMIE_RANG_REGNE')) {
	/**
	 * Nom anglais du rang principal `règne`.
	 */
	define('_TAXONOMIE_RANG_REGNE', 'kingdom');
}
// Suivant le règne l'embranchement se nomme phylum (animalia) ou division (fungi, plantae).
// Néanmoins, le terme phylum est souvent accepté pour l'ensemble des règnes
if (!defined('_TAXONOMIE_RANG_PHYLUM')) {
	/**
	 * Nom anglais du rang principal `phylum` ou `embranchement`.
	 * Ce nom est utilisé pour le règne `animalia`
	 */
	define('_TAXONOMIE_RANG_PHYLUM', 'phylum');
}
if (!defined('_TAXONOMIE_RANG_DIVISION')) {
	/**
	 * Nom anglais du rang principal `division`.
	 * Ce nom est utilisé pour le règne `fungi` ou `plantae` et correspond au `phylum` pour le règne animal.
	 */
	define('_TAXONOMIE_RANG_DIVISION', 'division');
}
if (!defined('_TAXONOMIE_RANG_CLASSE')) {
	/**
	 * Nom anglais du rang principal `classe`.
	 */
	define('_TAXONOMIE_RANG_CLASSE', 'class');
}
if (!defined('_TAXONOMIE_RANG_ORDRE')) {
	/**
	 * Nom anglais du rang principal `ordre`.
	 */
	define('_TAXONOMIE_RANG_ORDRE', 'order');
}
if (!defined('_TAXONOMIE_RANG_FAMILLE')) {
	/**
	 * Nom anglais du rang principal `famille`.
	 */
	define('_TAXONOMIE_RANG_FAMILLE', 'family');
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
if (!defined('_TAXONOMIE_RANGS_PRINCIPAUX')) {
	/**
	 * Liste des rangs principaux de la taxonomie.
	 */
	define('_TAXONOMIE_RANGS_PRINCIPAUX',
		_TAXONOMIE_RANG_REGNE . ':' .
		_TAXONOMIE_RANG_PHYLUM . ':' .
		_TAXONOMIE_RANG_CLASSE . ':' .
		_TAXONOMIE_RANG_ORDRE . ':' .
		_TAXONOMIE_RANG_FAMILLE . ':' .
		_TAXONOMIE_RANG_GENRE . ':' .
		_TAXONOMIE_RANG_ESPECE
	);
}


if (!defined('_TAXONOMIE_RANG_TRIBU')) {
	/**
	 * Nom anglais du rang secondaire `tribu`.
	 */
	define('_TAXONOMIE_RANG_TRIBU', 'tribe');
}
if (!defined('_TAXONOMIE_RANG_SECTION')) {
	/**
	 * Nom anglais du rang secondaire `section`.
	 */
	define('_TAXONOMIE_RANG_SECTION', 'section');
}
if (!defined('_TAXONOMIE_RANG_SERIE')) {
	/**
	 * Nom anglais du rang secondaire `serie`.
	 */
	define('_TAXONOMIE_RANG_SERIE', 'series');
}
if (!defined('_TAXONOMIE_RANG_VARIETE')) {
	/**
	 * Nom anglais du rang secondaire `variété`.
	 */
	define('_TAXONOMIE_RANG_VARIETE', 'variety');
}
if (!defined('_TAXONOMIE_RANG_FORME')) {
	/**
	 * Nom anglais du rang secondaire `forme`.
	 */
	define('_TAXONOMIE_RANG_FORME', 'forma');
}
if (!defined('_TAXONOMIE_RANGS_SECONDAIRES')) {
	/**
	 * Liste des rangs secondaires de la taxonomie.
	 */
	define('_TAXONOMIE_RANGS_SECONDAIRES',
		_TAXONOMIE_RANG_TRIBU . ':' .
		_TAXONOMIE_RANG_SECTION . ':' .
		_TAXONOMIE_RANG_SERIE . ':' .
		_TAXONOMIE_RANG_VARIETE . ':' .
		_TAXONOMIE_RANG_FORME
	);
}


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


/**
 * Extrait, de la table `spip_taxons`, la liste des taxons d'un règne donné ayant fait l'objet
 * d'une modification manuelle.
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
 * @param $champ
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
