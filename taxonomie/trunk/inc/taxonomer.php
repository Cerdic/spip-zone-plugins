<?php
/**
 * Ce fichier contient l'ensemble des constantes et des utilitaires nécessaires au fonctionnement du plugin.
 *
 * @package SPIP\TAXONOMIE\OUTILS
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_TAXONOMIE_REGNE_ANIMAL'))
	define('_TAXONOMIE_REGNE_ANIMAL', 'animalia');
if (!defined('_TAXONOMIE_REGNE_VEGETAL'))
	define('_TAXONOMIE_REGNE_VEGETAL', 'plantae');
if (!defined('_TAXONOMIE_REGNE_FONGIQUE'))
	define('_TAXONOMIE_REGNE_FONGIQUE', 'fungi');

if (!defined('_TAXONOMIE_REGNES'))
	define('_TAXONOMIE_REGNES',
		implode(':', array(
			_TAXONOMIE_REGNE_ANIMAL,
			_TAXONOMIE_REGNE_VEGETAL,
			_TAXONOMIE_REGNE_FONGIQUE
		)));

if (!defined('_TAXONOMIE_RANG_REGNE'))
	define('_TAXONOMIE_RANG_REGNE', 'kingdom');
// Suivant le règne l'embranchement se nomme phylum (animalia) ou division (fungi, plantae).
// Néanmoins, le terme phylum est souvent accepté pour l'ensemble des règnes
if (!defined('_TAXONOMIE_RANG_PHYLUM'))
	define('_TAXONOMIE_RANG_PHYLUM', 'phylum');
if (!defined('_TAXONOMIE_RANG_DIVISION'))
	define('_TAXONOMIE_RANG_DIVISION', 'division');
if (!defined('_TAXONOMIE_RANG_CLASSE'))
	define('_TAXONOMIE_RANG_CLASSE', 'class');
if (!defined('_TAXONOMIE_RANG_ORDRE'))
	define('_TAXONOMIE_RANG_ORDRE', 'order');
if (!defined('_TAXONOMIE_RANG_FAMILLE'))
	define('_TAXONOMIE_RANG_FAMILLE', 'family');
if (!defined('_TAXONOMIE_RANG_GENRE'))
	define('_TAXONOMIE_RANG_GENRE', 'genus');
if (!defined('_TAXONOMIE_RANG_ESPECE'))
	define('_TAXONOMIE_RANG_ESPECE', 'species');
if (!defined('_TAXONOMIE_RANG_SOUS_ESPECE'))
	define('_TAXONOMIE_RANG_SOUS_ESPECE', 'subspecies');
if (!defined('_TAXONOMIE_RANG_VARIETE'))
	define('_TAXONOMIE_RANG_VARIETE', 'variety');
if (!defined('_TAXONOMIE_RANG_SOUS_VARIETE'))
	define('_TAXONOMIE_RANG_SOUS_VARIETE', 'subvariety');
if (!defined('_TAXONOMIE_RANG_RACE'))
	define('_TAXONOMIE_RANG_RACE', 'race');
if (!defined('_TAXONOMIE_RANG_FORME'))
	define('_TAXONOMIE_RANG_FORME', 'forma');
if (!defined('_TAXONOMIE_RANG_SOUS_FORME'))
	define('_TAXONOMIE_RANG_SOUS_FORME', 'subforma');

//
if (!defined('_TAXONOMIE_RANGS_PARENTS_ESPECE'))
	/**
	 * Liste des rangs utilisés du règne au genre compris.
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
	 * Liste des rangs utilisés de l'espèce à la sous-forme
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
	 * Liste complète des rangs utilisés par le plugin
	 */
	define('_TAXONOMIE_RANGS',
		_TAXONOMIE_RANGS_PARENTS_ESPECE . ':' .	_TAXONOMIE_RANGS_ESPECE_ET_FILS);

if (!defined('_TAXONOMIE_CACHE_NOMDIR'))
	/**
	 * @package SPIP\TAXONOMIE\CACHE
	 *
	 * Nom du dossier contenant les fichiers caches des éléments de taxonomie
	 */
	define('_TAXONOMIE_CACHE_NOMDIR', 'cache-taxonomie/');
if (!defined('_TAXONOMIE_CACHE_DIR'))
	/**
	 * @package SPIP\TAXONOMIE\CACHE
	 *
	 * Chemin du dossier contenant les fichiers caches des boussoles
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
 *
 * @package SPIP\TAXONOMIE\OBJET
 *
 * @param $regne
 *
 * @return array
 */
function taxon_preserver_editions($regne) {
	$select = array('tsn', 'nom_commun', 'descriptif');
	$where = array('regne=' . sql_quote($regne), 'edite=' . sql_quote('oui'));
	$taxons = sql_allfetsel($select, 'spip_taxons', $where);

	return $taxons;
}


/**
 *
 * @package SPIP\TAXONOMIE\OBJET
 *
 * @param           $nom_charge
 * @param           $nom_edite
 * @param bool|true $priorite_edition
 *
 * @return string
 */
function taxon_merger_traductions($nom_charge, $nom_edite, $priorite_edition=true) {
	$source = array();
	$destination = array();
	$nom_merge = '';

	// Suivant la priorite entre édition et chargement automatique on positionne la source
	// (priorite plus faible) et la destination (priorité plus haute)
	$nom_source = $nom_charge;
	$nom_destination = $nom_edite;
	if (!$priorite_edition) {
		$nom_source = $nom_edite;
		$nom_destination = $nom_charge;
	}

	// On extrait les noms par langue
	include_spip('inc/filtres');
	if (preg_match(_EXTRAIRE_MULTI, $nom_source, $match)) {
		$source = extraire_trads($match[1]);
	}
	if (preg_match(_EXTRAIRE_MULTI, $nom_destination, $match)) {
		$destination = extraire_trads($match[1]);
	}

	// On complète la destination avec les noms de la source dont la langue n'est pas
	// présente dans la destination
	foreach ($source as $_lang => $_nom) {
		if (!array_key_exists($_lang, $destination)) {
			$destination[$_lang] = $_nom;
		}
	}

	// On construit le nom mergé à partir de la destination
	foreach ($destination as $_lang => $_nom) {
		$nom_merge .= '[' . $_lang . ']' . $_nom;
	}
	$nom_merge = '<multi>' . $nom_merge . '</multi>';

	return $nom_merge;
}


/**
 *
 * @package SPIP\TAXONOMIE\OBJET
 *
 * @param $champ
 *
 * @return string
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
 * 		Liste des fichiers à supprimer ou vide si tous les fichiers cache doivent être supprimer.
 * 		Il est possible de passer un seul fichier comme une chaine et pas un tableau à un élément.
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