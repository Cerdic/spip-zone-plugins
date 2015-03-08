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
// On utilise par défaut les rangs variété et sous-variété des règnes fongique et végétal
// (race et sous-race pour le règne animal)
if (!defined('_TAXONOMIE_RANGS_ESPECE_ET_FILS'))
	define('_TAXONOMIE_RANGS_ESPECE_ET_FILS',
		implode(':', array(
			_TAXONOMIE_RANG_ESPECE,
			_TAXONOMIE_RANG_SOUS_ESPECE,
			_TAXONOMIE_RANG_VARIETE,
			_TAXONOMIE_RANG_SOUS_VARIETE,
			_TAXONOMIE_RANG_FORME,
			_TAXONOMIE_RANG_SOUS_FORME
		)));
// Liste complète des rangs utilisés par le plugin
if (!defined('_TAXONOMIE_RANGS'))
	define('_TAXONOMIE_RANGS',
		_TAXONOMIE_RANGS_PARENTS_ESPECE . ':' .	_TAXONOMIE_RANGS_ESPECE_ET_FILS);


/**
 * Renvoie, à partir de l'url du service, le tableau des données demandées.
 * Le service utilise dans ce cas une chaine JSON qui est décodée pour fournir
 * le tableau de sortie.
 *
 *@param string $url
 * 		URL complète du service web.
 *
*@return array
 */
function url2json_data($url) {
	// Acquisition des données spécifiées par l'url
	include_spip('inc/distant');
	$flux = recuperer_page($url);

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


/**
 * Liste dans un tableau les rangs taxonomiques supportés par le plugin, à savoir:
 * kingdom, phylum, class, order, family, genus et species.
 * Les règnes sont exprimés en anglais et écrits en lettres minuscules.
 * La fonction permet d'exclure de la liste les rangs extrêmes kingdom et specie et de choisir
 * entre le rang phylum et son synonyme division.
 *
 * @param bool $exclure_regne
 * 		Demande d'exclusion du règne de la liste des rangs
 * @param bool $exclure_espece
 * 		Demande d'exclusion de l'espèce de la liste des rangs
 * @param string	$regne
 * 		Nom scientifque du règne pour lequel la liste des rangs est demandée.
 * 		Cet argument permet de remplacer le rang phylum par division qui est son synonyme
 * 		pour les règnes fongique et végétal
 *
 * @return array
 */
function lister_rangs($exclure_regne=true, $exclure_espece=true, $regne=_TAXONOMIE_REGNE_ANIMAL) {
	$exclusions = array();
	// todo : revoir complètement la fonction les deux listes de rangs

	$rangs = explode(':', _TAXONOMIE_RANGS);
	if ($exclure_regne)
		$exclusions[] = _TAXONOMIE_RANG_REGNE;
	if ($exclure_espece)
		$exclusions[] = _TAXONOMIE_RANG_ESPECE;
	$rangs = array_diff($rangs, $exclusions);

	if (($regne == _TAXONOMIE_REGNE_FONGIQUE)
	OR  ($regne == _TAXONOMIE_REGNE_VEGETAL)) {
		if ($index_phylum = array_search(_TAXONOMIE_RANG_PHYLUM, $rangs))
			$rangs[$index_phylum] = _TAXONOMIE_RANG_DIVISION;
	}

	return $rangs;
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
	if (preg_match(_EXTRAIRE_MULTI, $nom_source, $match))
		$source = extraire_trads($match[1]);
	if (preg_match(_EXTRAIRE_MULTI, $nom_destination, $match))
		$destination = extraire_trads($match[1]);

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

?>