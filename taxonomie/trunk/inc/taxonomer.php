<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_TAXONOMIE_REGNE_ANIMAL'))
	define('_TAXONOMIE_REGNE_ANIMAL', 'animalia');
if (!defined('_TAXONOMIE_REGNE_VEGETAL'))
	define('_TAXONOMIE_REGNE_VEGETAL', 'plantae');
if (!defined('_TAXONOMIE_REGNE_FONGIQUE'))
	define('_TAXONOMIE_REGNE_FONGIQUE', 'fungi');

if (!defined('_TAXONOMIE_LISTE_REGNES'))
	define('_TAXONOMIE_LISTE_REGNES',
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

// Liste des rangs utilisés du règne au genre compris.
// On utilise par défaut au niveau 2 le terme phylum du règne animal
// (division pour les autres règnes)
if (!defined('_TAXONOMIE_RANGS_PARENTS_ESPECE'))
	define('_TAXONOMIE_RANGS_PARENTS_ESPECE',
		implode(':', array(
			_TAXONOMIE_RANG_REGNE,
			_TAXONOMIE_RANG_PHYLUM,
			_TAXONOMIE_RANG_CLASSE,
			_TAXONOMIE_RANG_ORDRE,
			_TAXONOMIE_RANG_FAMILLE,
			_TAXONOMIE_RANG_GENRE
		)));
// Liste des rangs utilisés de l'espèce à la sous-forme
if (!defined('_TAXONOMIE_RANGS_ESPECE_ET_FILS'))
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
// Liste complète des rangs utilisés par le plugin
if (!defined('_TAXONOMIE_RANGS'))
	define('_TAXONOMIE_RANGS',
		_TAXONOMIE_RANGS_PARENTS_ESPECE . ':' .	_TAXONOMIE_RANGS_ESPECE_ET_FILS);

if (!defined('_TAXONOMIE_CACHE_NOMDIR'))
	/**
	 * Nom du dossier contenant les fichiers caches des éléments de taxonomie */
	define('_TAXONOMIE_CACHE_NOMDIR', 'cache-taxonomie/');
if (!defined('_TAXONOMIE_CACHE_DIR'))
	/**
	 * Chemin du dossier contenant les fichiers caches des boussoles */
	define('_TAXONOMIE_CACHE_DIR', _DIR_VAR . _TAXONOMIE_CACHE_NOMDIR);


/**
 * Renvoie, à partir de l'url du service, le tableau des données demandées.
 * Le service utilise dans ce cas une chaine JSON qui est décodée pour fournir
 * le tableau de sortie. Le flux retourné par le service est systématiquement
 * transcodé dans le chrset du site avant d'être décodé.
 *
 *@param string $url
 * 		URL complète du service web.
 *
*@return array
 */
function url2json_data($url, $taille_max=null) {
	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$flux = recuperer_page($url, true, false, $taille_max);

	// Tranformation de la chaine json reçue en tableau associatif
	$data = json_decode($flux, true);

	return $data;
}


/**
 * Liste dans un tableau les règnes supportés par le plugin, à savoir: animalia,
 * plantae et fungi.
 * Les règnes sont exprimés avec leur nom scientifique en lettres minuscules.
 *
 * @return array
 */
function lister_regnes() {
	return explode(':', _TAXONOMIE_LISTE_REGNES);
}


function preserver_taxons_edites($regne) {
	$select = array('tsn', 'nom_commun', 'descriptif');
	$where = array('regne=' . sql_quote($regne), 'edite=' . sql_quote('oui'));
	$taxons = sql_allfetsel($select, 'spip_taxons', $where);

	return $taxons;
}


function merger_multi($nom_charge, $nom_edite, $priorite_edition=true) {
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


function traduire_champ_taxon($champ) {
	$traduction = '';
	if ($champ) {
		$traduction = _T("taxon:champ_${champ}_label");
	}
	return $traduction;
}


function extraire_element($tableau, $cles) {
    $erreur = false;
    $element = $tableau;
	if ($cles) {
		foreach ($cles as $_cle) {
 		if (isset($element[$_cle])) {
          $element = $element[$_cle];
 		}
 		else {
 			$erreur = true;
 			break;
 		}
 	}
	}
    return ($erreur ? null : $element);
}

/**
 * Ecriture d'un contenu issu d'un service web taxonomique dans un fichier texte afin d'optimiser le nombre
 * de requête adressée au service.
 *
 * @param string	$cache
 * 		Contenu du fichier cache. Si le service appelant manipule un tableau il doit le sérialiser avant
 *      d'appeler cette fonction.
 * @param string    $service
 * @param int       $tsn
 * @param string    $code_langue
 * @param string    $action
 *
 * @return boolean
 * 		Toujours à vrai.
 */
function ecrire_cache_taxonomie($cache, $service, $tsn, $code_langue='', $action='') {
	// Création du dossier cache si besoin
	sous_repertoire(_DIR_VAR, trim(_TAXONOMIE_CACHE_NOMDIR, '/'));

	// Ecriture du fichier cache
	$fichier_cache = nommer_cache_taxonomie($service, $tsn, $code_langue, $action);
	ecrire_fichier($fichier_cache, $cache);

	return true;
}


/**
 * @param $service
 * @param $tsn
 * @param string $code_langue
 * @param string $action
 * @return string
 */
function nommer_cache_taxonomie($service, $tsn, $code_langue='', $action='') {
	// Construction du chemin complet d'un fichier cache
	$fichier_cache = _TAXONOMIE_CACHE_DIR
		. $service
		. ($action ? '_' . $action : '')
		. '_' . $tsn
		. ($code_langue ? '_' . $code_langue : '')
		. '.txt';

	return $fichier_cache;
}

/**
 * Vérifie l'existence du fichier cache pour un taxon et un service donnés. Si le fichier existe
 * la fonction retourne son chemin complet.
 *
 * @param string    $service
 * @param int       $tsn
 * @param string    $code_langue
 * @param string    $action
 *
 * @return string
 * 		Chemin du fichier cache si il existe ou chaine vide sinon.
 */
function cache_taxonomie_existe($service, $tsn, $code_langue='', $action='') {
	// Contruire le nom du fichier cache
	$fichier_cache = nommer_cache_taxonomie($service, $tsn, $code_langue, $action);

	// Vérification de l'existence du fichier:
	// - chaine vide si le fichier n'existe pas
	// - chemin complet du fichier si il existe
	if (!file_exists($fichier_cache))
		$fichier_cache = '';

	return $fichier_cache;
}


/**
 * Supprime tous les fichiers caches.
 *
 * @return boolean
 * 		Toujours à vrai.
 */
function supprimer_caches(){
	include_spip('inc/flock');

	if ($fichiers_cache = glob(_TAXONOMIE_CACHE_DIR . "*.*")) {
		foreach ($fichiers_cache as $_fichier) {
			supprimer_fichier($_fichier);
		}
	}

	return true;
}


/**
 * Etablit la liste de tous les caches y compris celui de la liste des boussoles
 * et construit un tableau avec la liste des fichiers et l'alias de la boussole
 * associée.
 *
 * @return array
 * 		Tableau des caches recensés :
 *
 * 		- fichier : chemin complet du fichier cache,
 * 		- alias : alias de la boussole ou vide si on est en présence de la liste des boussoles.
 */
function trouver_caches(){
	$caches = array();

	$fichier_liste = cache_liste_existe();
	if ($fichier_liste)
		$caches[] = array('fichier' => $fichier_liste, 'alias' => '');

	$pattern_cache = _BOUSSOLE_DIR_CACHE . str_replace(_BOUSSOLE_PATTERN_ALIAS, '*', _BOUSSOLE_CACHE);
	$fichiers_cache = glob($pattern_cache);
	if ($fichiers_cache) {
		foreach($fichiers_cache as $_fichier) {
			$alias_boussole = str_replace(_BOUSSOLE_PREFIXE_CACHE, '', basename($_fichier, '.xml'));
			$caches[] = array('fichier' => $_fichier, 'alias' => $alias_boussole);
		}
	}

	return $caches;
}

?>