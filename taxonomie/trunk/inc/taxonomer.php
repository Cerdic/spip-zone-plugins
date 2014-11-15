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
if (!defined('_TAXONOMIE_RANG_PHYLUM'))
	define('_TAXONOMIE_RANG_PHYLUM', 'phylum');
if (!defined('_TAXONOMIE_RANG_CLASSE'))
	define('_TAXONOMIE_RANG_CLASSE', 'class');
if (!defined('_TAXONOMIE_RANG_ORDRE'))
	define('_TAXONOMIE_RANG_ORDRE', 'order');
if (!defined('_TAXONOMIE_RANG_FAMILLE'))
	define('_TAXONOMIE_RANG_FAMILLE', 'family');
if (!defined('_TAXONOMIE_RANG_GENRE'))
	define('_TAXONOMIE_RANG_GENRE', 'genus');
if (!defined('_TAXONOMIE_RANG_ESPECE'))
	define('_TAXONOMIE_RANG_ESPECE', 'specie');

if (!defined('_TAXONOMIE_LISTE_RANGS'))
	define('_TAXONOMIE_LISTE_RANGS',
		implode(':', array(
			_TAXONOMIE_RANG_REGNE,
			_TAXONOMIE_RANG_PHYLUM,
			_TAXONOMIE_RANG_CLASSE,
			_TAXONOMIE_RANG_ORDRE,
			_TAXONOMIE_RANG_FAMILLE,
			_TAXONOMIE_RANG_GENRE,
			_TAXONOMIE_RANG_ESPECE
		)));


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
 * kingdom, phylum, class, order, family, genus et specie.
 * Les règnes sont exprimés en anglais et écrits en lettres minuscules.
 * La fonction permet d'exclure de la liste les rangs extrêmes kingdom et specie.
 *
 * @param bool $exclure_regne
 * 		Demande d'exclusion du règne de la liste des rangs
 * @param bool $exclure_espece
 * 		Demande d'exclusion de l'espèce de la liste des rangs
 *
 * @return array
 */
function lister_rangs($exclure_regne=true, $exclure_espece=true) {
	$exclusions = array();

	$rangs = explode(':', _TAXONOMIE_LISTE_RANGS);
	if ($exclure_regne)
		$exclusions[] = _TAXONOMIE_RANG_REGNE;
	if ($exclure_espece)
		$exclusions[] = _TAXONOMIE_RANG_ESPECE;
	$rangs = array_diff($rangs, $exclusions);

	return $rangs;
}

?>