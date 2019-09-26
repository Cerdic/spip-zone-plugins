<?php
/**
 * Ce fichier contient les balises et les filtres fournis par le plugin.
 *
 * @package SPIP\BOUSSOLE\Squelettes
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


// ----------------------- Balises propres à Boussole ---------------------------------

/**
 * Compilation de la balise `#BOUSSOLE_INFOS` retournant les informations générales sur une
 * boussole.
 *
 * La balise #BOUSSOLE_INFOS renvoie :
 *
 * - le tableau des infos contenues dans la meta boussole_infos_xxx si l'alias "xxx" est fourni,
 * - la liste de tous les tableaux d'infos des meta boussole_infos_* sinon.
 *
 * La liste des informations disponibles est la suivante :
 *
 * - 'logo' : l'url du logo de la boussole
 * - 'version' : la version de la boussole
 * - 'serveur' : le nom du serveur fournissant la boussole
 * - 'sha' : sha256 du fichier cache de la boussole
 * - 'alias' : alias de la boussole
 * - 'demo' : url de la page de démo de la boussole
 * - 'nbr_sites' : nombre de sites intégrés dans la boussole
 * - 'maj' : date de la dernière mise à jour des informations
 *
 * @api
 * @balise
 * @uses calcul_boussole_infos()
 *
 * @example
 * 		`#BOUSSOLE_INFOS{spip}|table_valeur{logo}` renvoie l'url du logo de la boussole "spip"
 *
 * @param champ $p
 * 		Pile transmise en entrée à la balise
 * @return champ
 * 		Pile fournie en entrée et complétée par le code à générer
 */
function balise_BOUSSOLE_INFOS($p) {
	
	$alias_boussole = interprete_argument_balise(1,$p);
	$alias_boussole = isset($alias_boussole) ? str_replace('\'', '"', $alias_boussole) : '""';

	$p->code = 'calcul_boussole_infos('.$alias_boussole.')';

	return $p;
}

/**
 * Récupération des informations sur une boussole donnée ou sur toutes les boussoles installées.
 *
 * Les informations retournées pour une boussole d'alias "xxx" sont celles stockées dans la meta
 * `boussole_infos_xxx` auxquelles on adjoint la date de la dernière mise à jour de cette meta.
 *
 * @param string $boussole
 * 		Alias de la boussole ou chaine vide
 *
 * @return array
 * 		Tableau de la ou des boussoles installées.
 * 		Si l'alias de la boussole est erroné, la fonction retourne un tableau vide
 */
function calcul_boussole_infos($boussole) {

	$infos = array();
	
	$where = array();
	$group_by = array();
	if ($boussole)
		$where[] = 'aka_boussole=' . sql_quote($boussole);
	else
		$group_by[] = 'aka_boussole';

	$akas_boussole = sql_allfetsel('aka_boussole', 'spip_boussoles', $where, $group_by);
	if ($akas_boussole) {
		foreach (array_map('reset', $akas_boussole) as $_aka_boussole) {
			$meta = sql_fetsel('valeur, maj', 'spip_meta', 'nom=' . sql_quote('boussole_infos_' . $_aka_boussole));
			if ($meta) {
				if ($boussole)
					$infos = array_merge(unserialize($meta['valeur']), array('maj' => $meta['maj']));
				else
					$infos[] = array_merge(unserialize($meta['valeur']), array('maj' => $meta['maj']));
			}
		}
	}

	return $infos;
}


// ----------------------- Filtres propres à Boussole ---------------------------------

/**
 * Traduction d'un champ d'une boussole, d'un groupe de sites ou d'un site.
 *
 * @api
 * @filtre
 *
 * @param string $boussole
 * 		Alias de la boussole
 * @param string $champ
 * 		Champ à traduire. La liste des champs possibles est :
 *
 * 		- 'nom_boussole', 'slogan_boussole', 'descriptif_boussole' pour un objet boussole
 * 		- 'nom_groupe', 'slogan_groupe' pour un objet groupe
 * 		- 'nom_site', 'slogan_site', 'descriptif_site', 'nom_slogan_site' pour un objet site
 * @param string $objet
 * 		Identifiant d'un objet groupe ou site. Vide pour la traduction d'un champ d'un objet
 * 		boussole
 * @return string
 * 		Champ traduit dans la langue du site
 */
function boussole_traduire($boussole, $champ, $objet='') {
	static	$champs_autorises = array(
									'nom_boussole', 'slogan_boussole', 'descriptif_boussole',
									'nom_groupe', 'slogan_groupe',
									'nom_site', 'slogan_site', 'descriptif_site', 'nom_slogan_site');
	$traduction = '';

	if ($champ == '')
		return $traduction;

	// Détermination de la traduction à rechercher dans les extras de boussole
	if ($boussole) {
		if (in_array($champ, $champs_autorises)) {
			if ($champ == 'nom_slogan_site') {
				$type_objet = 'site';
				$aka_objet = $objet;
				$select = array('nom_objet', 'slogan_objet');
			}
			else {
				list($type_champ, $type_objet) = explode('_', $champ);
				if ($type_objet == 'boussole')
					$aka_objet = $boussole;
				else
					$aka_objet = $objet;
				$select = "${type_champ}_objet";
			}

			// Accès à la table boussoles_extras où sont stockées les traductions
			$where = array(
				'aka_boussole=' . sql_quote($boussole),
				'type_objet=' . sql_quote($type_objet),
				'aka_objet=' . sql_quote($aka_objet));
			$traductions = sql_fetsel($select, 'spip_boussoles_extras', $where);

			if (count($traductions) == 1)
				$traduction = extraire_multi($traductions[$select]);
			else if (count($traductions) == 2)
				$traduction = extraire_multi($traductions['nom_objet']) . '-' . extraire_multi($traductions['slogan_objet']);
		}
	}

	return $traduction;
}


/**
 * Liste des caches présents sur le site serveur complétée par des informations sur leur nature
 * et les boussoles associées.
 *
 * Cette fonction est utilisée pour l'affichage de la fonction serveur dans l'espace privé.
 *
 * @api
 * @filtre
 * @pipeline_appel declarer_boussoles
 *
 * @return array
 */
function boussole_lister_caches() {
	$caches = array();

	include_spip('inc/boussole_cache');
	$fichiers_cache = trouver_caches();
	if ($fichiers_cache) {
		include_spip('inc/config');
		$boussoles = lire_config('boussole/serveur/boussoles_disponibles');
		$boussoles = pipeline('declarer_boussoles', $boussoles);

		// Chargement de la fonction de conversion xml en tableau
		$convertir = charger_fonction('decoder_xml', 'inc');

		foreach ($fichiers_cache as $_fichier) {
			$cache = array();
			$cache['fichier'] = $_fichier['fichier'];
			$cache['nom'] = basename($_fichier['fichier']);
			$cache['maj'] = date('Y-m-d H:i:s', filemtime($_fichier['fichier']));

			$cache['sha'] = '';
			$cache['plugin'] = '';
			$cache['alias'] = $_fichier['alias'];
			$cache['manuelle'] = false;

			$contenu = '';
			lire_fichier($_fichier['fichier'], $contenu);
			$tableau = $convertir($contenu);
			if (!$cache['alias']) {
				// C'est le cache qui liste les boussoles hébergées
				$cache['description'] = _T('boussole:info_cache_boussoles');
				if (isset($tableau[_BOUSSOLE_NOMTAG_LISTE_BOUSSOLES])) {
					$cache['sha'] = $tableau[_BOUSSOLE_NOMTAG_LISTE_BOUSSOLES]['@attributes']['sha'];
				}
			}
			else {
				// C'est le cache d'une boussole hébergée
				$cache['description'] = _T('boussole:info_cache_boussole', array('boussole' => $cache['alias']));
				if  (isset($tableau[_BOUSSOLE_NOMTAG_BOUSSOLE])) {
					$cache['sha'] = $tableau[_BOUSSOLE_NOMTAG_BOUSSOLE]['@attributes']['sha'];
					$cache['nom'] .= " ({$tableau[_BOUSSOLE_NOMTAG_BOUSSOLE]['@attributes']['version']})";
				}
				if (isset($boussoles[$cache['alias']]['prefixe'])
				AND ($boussoles[$cache['alias']]['prefixe'])) {
					// Boussole utilisant un plugin
					$informer = charger_fonction('informer_plugin', 'inc');
					$plugin = $informer($boussoles[$cache['alias']]['prefixe']);
					if ($plugin)
						$cache['plugin'] = "{$plugin['nom']} ({$boussoles[$cache['alias']]['prefixe']}/{$plugin['version']})";
				}
				else {
					// Boussole n'utilisant pas un plugin, nommée boussole manuelle
					$cache['manuelle'] = true;
					$cache['plugin'] = _T('boussole:info_boussole_manuelle');

					// Ajout de la version contenue dans le fichier XML source de la boussole pour
					// vérifier que le cache est bien à jour avec
					$fichier_source = find_in_path("boussole_traduite-{$cache['alias']}.xml");
					lire_fichier($fichier_source, $contenu);
					$tableau_source = $convertir($contenu);
					if  (isset($tableau_source[_BOUSSOLE_NOMTAG_BOUSSOLE])) {
						$cache['plugin'] .= isset($tableau_source[_BOUSSOLE_NOMTAG_BOUSSOLE]['@attributes']['version'])
										  ? " ({$tableau_source[_BOUSSOLE_NOMTAG_BOUSSOLE]['@attributes']['version']})"
										  : "";
					}
				}
			}
			$caches[] = $cache;
		}
	}

	return $caches;
}


/**
 * Récupération du nombre de boussoles hébergées sur le site serveur.
 *
 * @api
 * @filtre
 * @pipeline_appel declarer_boussoles
 *
 * @return int
 */
function boussole_compter_hebergements() {
	include_spip('inc/config');
	$boussoles = lire_config('boussole/serveur/boussoles_disponibles');
	$boussoles = pipeline('declarer_boussoles', $boussoles);

	return count($boussoles);
}
