<?php
/**
 * Ce fichier contient l'ensemble des constantes et des utilitaires nécessaires au fonctionnement du plugin.
 *
 * @package SPIP\TAXONOMIE
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_TAXONOMIE_REGNE_ANIMAL'))
	/**
	 * Nom latin du règne animal (animaux).
	 */
	define('_TAXONOMIE_REGNE_ANIMAL', 'animalia');
if (!defined('_TAXONOMIE_REGNE_VEGETAL'))
	/**
	 * Nom latin du règne végétal (plantes).
	 */
	define('_TAXONOMIE_REGNE_VEGETAL', 'plantae');
if (!defined('_TAXONOMIE_REGNE_FONGIQUE'))
	/**
	 * Nom latin du règne fongique (champignons).
	 */
	define('_TAXONOMIE_REGNE_FONGIQUE', 'fungi');

if (!defined('_TAXONOMIE_REGNES'))
	/**
	 * Liste des règnes supportés par le plugin (concanétation des noms séparés par le signe deux-points).
	 */
	define('_TAXONOMIE_REGNES',
		implode(':', array(
			_TAXONOMIE_REGNE_ANIMAL,
			_TAXONOMIE_REGNE_VEGETAL,
			_TAXONOMIE_REGNE_FONGIQUE
		)));

if (!defined('_TAXONOMIE_RANG_REGNE'))
	/**
	 * Nom anglais du rang `règne`.
	 */
	define('_TAXONOMIE_RANG_REGNE', 'kingdom');
// Suivant le règne l'embranchement se nomme phylum (animalia) ou division (fungi, plantae).
// Néanmoins, le terme phylum est souvent accepté pour l'ensemble des règnes
if (!defined('_TAXONOMIE_RANG_PHYLUM'))
	/**
	 * Nom anglais du rang `phylum` ou `embranchement`.
	 * Ce nom est utilisé pour le règne `animalia`
	 */
	define('_TAXONOMIE_RANG_PHYLUM', 'phylum');
if (!defined('_TAXONOMIE_RANG_DIVISION'))
	/**
	 * Nom anglais du rang `division`.
	 * Ce nom est utilisé pour le règne `fungi` ou `plantae` et correspond au `phylum` pour le règne animal.
	 */
	define('_TAXONOMIE_RANG_DIVISION', 'division');
if (!defined('_TAXONOMIE_RANG_CLASSE'))
	/**
	 * Nom anglais du rang `classe`.
	 */
	define('_TAXONOMIE_RANG_CLASSE', 'class');
if (!defined('_TAXONOMIE_RANG_ORDRE'))
	/**
	 * Nom anglais du rang `ordre`.
	 */
	define('_TAXONOMIE_RANG_ORDRE', 'order');
if (!defined('_TAXONOMIE_RANG_FAMILLE'))
	/**
	 * Nom anglais du rang `famille`.
	 */
	define('_TAXONOMIE_RANG_FAMILLE', 'family');
if (!defined('_TAXONOMIE_RANG_GENRE'))
	/**
	 * Nom anglais du rang `genre`.
	 */
	define('_TAXONOMIE_RANG_GENRE', 'genus');
if (!defined('_TAXONOMIE_RANG_ESPECE'))
	/**
	 * Nom anglais du rang `espèce`.
	 */
	define('_TAXONOMIE_RANG_ESPECE', 'species');
if (!defined('_TAXONOMIE_RANG_SOUS_ESPECE'))
	/**
	 * Nom anglais du rang intercalaire `sous-espèce`.
	 */
	define('_TAXONOMIE_RANG_SOUS_ESPECE', 'subspecies');
if (!defined('_TAXONOMIE_RANG_VARIETE'))
	/**
	 * Nom anglais du rang `variété`.
	 */
	define('_TAXONOMIE_RANG_VARIETE', 'variety');
if (!defined('_TAXONOMIE_RANG_SOUS_VARIETE'))
	/**
	 * Nom anglais du rang intercalaire `sous-variété`.
	 */
	define('_TAXONOMIE_RANG_SOUS_VARIETE', 'subvariety');
if (!defined('_TAXONOMIE_RANG_RACE'))
	/**
	 * Nom anglais du rang `race`.
	 */
	define('_TAXONOMIE_RANG_RACE', 'race');
if (!defined('_TAXONOMIE_RANG_FORME'))
	/**
	 * Nom anglais du rang `forme`.
	 */
	define('_TAXONOMIE_RANG_FORME', 'forma');
if (!defined('_TAXONOMIE_RANG_SOUS_FORME'))
	/**
	 * Nom anglais du rang intercalaire `sous-règne`.
	 */
	define('_TAXONOMIE_RANG_SOUS_FORME', 'subforma');

//
if (!defined('_TAXONOMIE_RANGS_PARENTS_ESPECE'))
	/**
	 * Liste des rangs utilisés du règne au genre compris (concanétation des noms séparés par le signe deux-points).
	 * On utilise par défaut au niveau 2 le terme phylum du règne animal (division pour les autres règnes)
	 */
	define('_TAXONOMIE_RANGS_PARENTS_ESPECE',
		implode(':', array(
			_TAXONOMIE_RANG_REGNE,
			_TAXONOMIE_RANG_PHYLUM,
			_TAXONOMIE_RANG_CLASSE,
			_TAXONOMIE_RANG_ORDRE,
			_TAXONOMIE_RANG_FAMILLE,
			_TAXONOMIE_RANG_GENRE
		)));
if (!defined('_TAXONOMIE_RANGS_ESPECE_ET_FILS'))
	/**
	 * Liste des rangs utilisés de l'espèce à la sous-forme (concanétation des noms séparés par le signe deux-points).
	 */
	define('_TAXONOMIE_RANGS_ESPECE_ET_FILS',
		implode(':', array(
			_TAXONOMIE_RANG_ESPECE,
			_TAXONOMIE_RANG_SOUS_ESPECE,
			_TAXONOMIE_RANG_VARIETE,
			_TAXONOMIE_RANG_SOUS_VARIETE,
			_TAXONOMIE_RANG_RACE,
			_TAXONOMIE_RANG_FORME,
			_TAXONOMIE_RANG_SOUS_FORME
		)));
if (!defined('_TAXONOMIE_RANGS'))
	/**
	 * Liste complète des rangs utilisés par le plugin (concanétation des noms séparés par le signe deux-points).
	 */
	define('_TAXONOMIE_RANGS',
		_TAXONOMIE_RANGS_PARENTS_ESPECE . ':' .	_TAXONOMIE_RANGS_ESPECE_ET_FILS);

if (!defined('_TAXONOMIE_CACHE_NOMDIR'))
	/**
	 * Nom du dossier contenant les fichiers caches des éléments de taxonomie
	 *
	 * @package SPIP\TAXONOMIE\CACHE
	 */
	define('_TAXONOMIE_CACHE_NOMDIR', 'cache-taxonomie/');
if (!defined('_TAXONOMIE_CACHE_DIR'))
	/**
	 * Chemin du dossier contenant les fichiers caches des boussoles
	 *
	 * @package SPIP\TAXONOMIE\CACHE
	 */
	define('_TAXONOMIE_CACHE_DIR', _DIR_VAR . _TAXONOMIE_CACHE_NOMDIR);


/**
 * Renvoie, à partir de l'url du service, le tableau des données demandées.
 * Le service utilise dans ce cas une chaine JSON qui est décodée pour fournir
 * le tableau de sortie. Le flux retourné par le service est systématiquement
 * transcodé dans le charset du site avant d'être décodé.
 *
 * @package SPIP\TAXONOMIE\SERVICES
 *
 * @param string	$url
 * 		URL complète de la requête au service web concerné.
 * @param int|null	$taille_max
 * 		Taille maximale di flux récupéré suite à la requête.
 * 		`null` désigne la taille par défaut.
 *
 * @return array
 */
function service_requeter_json($url, $taille_max=null) {
	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$flux = recuperer_page($url, true, false, $taille_max);

	// Tranformation de la chaine json reçue en tableau associatif
	$data = json_decode($flux, true);

	return $data;
}


/**
 * Extrait de la table `spip_taxons` la liste des taxons d'un règne donné ayant fait l'objet
 * d'une modification manuelle.
 *
 * @package SPIP\TAXONOMIE\OBJET
 *
 * @param string	$regne
 * 		Nom scientifique du règne en lettres minuscules : `animalia`, `plantae`, `fungi`.
 *
 * @return array
 * 		Liste des taxons modifiées manuellement. Chaque élément de la liste est un tableau
 * 		composé des index `tsn`, `nom_commun`, `descriptif`.
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
 * @package SPIP\TAXONOMIE\OBJET
 *
 * @param string	$multi_prioritaire
 * 		Balise multi considérée comme prioritaire en cas de conflit sur une langue.
 * @param string	$multi_non_prioritaire
 * 		Balise multi considérée comme non prioritaire en cas de conflit sur une langue.
 *
 * @return string
 * 		La chaine construite est toujours une balise `<multi>` complète ou une chaine vide sinon.
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
 * @package SPIP\TAXONOMIE\OBJET
 *
 * @param $champ
 * 		Nom du champ dans la base de données.
 *
 * @return string
 * 		Traduction du champ dans la langue du site.
 */
function taxon_traduire_champ($champ) {
	$traduction = '';
	if ($champ) {
		$traduction = _T("taxon:champ_${champ}_label");
	}
	return $traduction;
}


/**
 * Ecrit le contenu issu d'un service taxonomique dans un fichier texte afin d'optimiser le nombre
 * de requêtes adressées au service.
 *
 * @package SPIP\TAXONOMIE\CACHE
 *
 * @param string	$cache
 * 		Contenu du fichier cache. Si le service appelant manipule un tableau il doit le sérialiser avant
 *      d'appeler cette fonction.
 * @param string    $service
 * @param int       $tsn
 * @param string    $spip_langue
 * @param string    $action
 *
 * @return boolean
 * 		Toujours à vrai.
 */
function cache_taxonomie_ecrire($cache, $service, $tsn, $spip_langue='', $action='') {
	// Création du dossier cache si besoin
	sous_repertoire(_DIR_VAR, trim(_TAXONOMIE_CACHE_NOMDIR, '/'));

	// Ecriture du fichier cache
	$fichier_cache = cache_taxonomie_nommer($service, $tsn, $spip_langue, $action);
	ecrire_fichier($fichier_cache, $cache);

	return true;
}


/**
 * Construit le nom du fichier cache en fonction du service, du taxon concernés et
 * d'autres critères optionnels.
 *
 * @package SPIP\TAXONOMIE\CACHE
 *
 * @param string    $service
 * @param int       $tsn
 * @param string    $spip_langue
 * @param string    $action
 *
 * @return string
 */
function cache_taxonomie_nommer($service, $tsn, $spip_langue='', $action='') {
	// Construction du chemin complet d'un fichier cache
	$fichier_cache = _TAXONOMIE_CACHE_DIR
		. $service
		. ($action ? '_' . $action : '')
		. '_' . $tsn
		. ($spip_langue ? '_' . $spip_langue : '')
		. '.txt';

	return $fichier_cache;
}

/**
 * Vérifie l'existence du fichier cache pour un taxon et un service donnés.
 * Si le fichier existe la fonction retourne son chemin complet.
 *
 * @package SPIP\TAXONOMIE\CACHE
 *
 * @param string    $service
 * @param int       $tsn
 * @param string    $spip_langue
 * @param string    $action
 *
 * @return string
 * 		Chemin du fichier cache si il existe ou chaine vide sinon.
 */
function cache_taxonomie_existe($service, $tsn, $spip_langue='', $action='') {
	// Contruire le nom du fichier cache
	$fichier_cache = cache_taxonomie_nommer($service, $tsn, $spip_langue, $action);

	// Vérification de l'existence du fichier:
	// - chaine vide si le fichier n'existe pas
	// - chemin complet du fichier si il existe
	if (!file_exists($fichier_cache))
		$fichier_cache = '';

	return $fichier_cache;
}


/**
 * Supprime tout ou partie des fichiers cache taxonomiques.
 *
 * @package SPIP\TAXONOMIE\CACHE
 *
 * @param array|string	$caches
 * 		Liste des fichiers à supprimer ou vide si tous les fichiers cache doivent être supprimés.
 * 		Il est possible de passer un seul fichier comme une chaine.
 *
 * @return boolean
 * 		Toujours à `true`.
 */
function cache_taxonomie_supprimer($caches=array()){
	include_spip('inc/flock');

	if ($caches) {
		$fichiers_cache = is_string($caches) ? array($caches) : $caches;
	} else {
		$fichiers_cache = glob(_TAXONOMIE_CACHE_DIR . "*.*");
	}

	if ($fichiers_cache) {
		foreach ($fichiers_cache as $_fichier) {
			supprimer_fichier($_fichier);
		}
	}

	return true;
}

?>