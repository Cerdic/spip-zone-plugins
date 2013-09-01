<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// ----------------------- Balises propres a Boussole ---------------------------------

/**
 * Balise retournant les informations sur une boussole.
 *
 * La balise #BOUSSOLE_INFOS renvoie :
 *
 * - le tableau des infos contenues dans la meta boussole_infos_alias si l'alias est fourni,
 * - la liste de tous les tableaux d'infos des meta boussole_infos_xxxx sinon.
 *
 * @balise BOUSSOLE_INFOS
 * @uses calcul_boussole_infos()
 *
 * @param string $p
 * 		alias de la boussole ou vide
 * @return array
 * 		tableau des informations demandees (une boussole ou toutes les boussoles)
 */
function balise_BOUSSOLE_INFOS($p) {
	
	$alias_boussole = interprete_argument_balise(1,$p);
	$alias_boussole = isset($alias_boussole) ? str_replace('\'', '"', $alias_boussole) : '""';

	$p->code = 'calcul_boussole_infos('.$alias_boussole.')';

	return $p;
}

/**
 * @param string $boussole
 * 		Alias de la boussole ou chaine vide
 *
 * @return array
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


// ----------------------- Filtres propres a Boussole ---------------------------------

/**
 * Traduction d'un champ d'une boussole, d'un groupe de sites ou d'un site
 *
 * @api
 * @filtre boussole_traduire
 *
 * @param string $aka_boussole
 * 		Alias de la boussole
 * @param string $champ
 * 		Champ à traduire
 * @param string $alias
 * 		Identifiant du groupe ou du site
 * @return string
 * 		Champ traduit dans la langue du site
 */
function boussole_traduire($aka_boussole, $champ, $alias='') {
	static	$champs_boussole = array('nom_boussole', 'slogan_boussole', 'descriptif_boussole');
	static	$champs_groupe = array('nom_groupe');
	static	$champs_site = array('nom_site', 'slogan_site', 'descriptif_site');

	$traduction = '';

	if ($champ == '')
		return $traduction;


	// Détermination de la traduction à rechercher dans les extras de boussole
	if ($aka_boussole) {
		if (in_array($champ, $champs_boussole)) {
			$type_objet = 'boussole';
			$aka_objet = $aka_boussole;
			$info = str_replace('boussole', 'objet', $champ);
		}
		elseif (in_array($champ, $champs_groupe)) {
			$type_objet = 'groupe';
			$aka_objet = $alias;
			$info = str_replace('groupe', 'objet', $champ);
		}
		elseif (in_array($champ, $champs_site)) {
			$type_objet = 'site';
			$aka_objet = $alias;
			$info = str_replace('site', 'objet', $champ);
		}
		elseif ($champ == 'nom_slogan_site') {
			$type_objet = 'site';
			$aka_objet = $alias;
			$info = array('nom_objet', 'slogan_objet');
		}
		else
			return $traduction;
	}

	// Accès à la table boussoles_extras où sont stockées les traductions
	$where = array(
		'aka_boussole=' . sql_quote($aka_boussole),
		'type_objet=' . sql_quote($type_objet),
		'aka_objet=' . sql_quote($aka_objet));
	$traductions = sql_fetsel($info, 'spip_boussoles_extras', $where);
	if (count($traductions) == 1)
		$traduction = extraire_multi($traductions[$info]);
	else if (count($traductions) == 2)
		$traduction = extraire_multi($traductions['nom_objet']) . '-' . extraire_multi($traductions['slogan_objet']);

	return $traduction;
}


/**
 * Liste des caches présents sur le site serveur complétée par des informations sur leur nature
 * et les boussoles associées.
 *
 * Cette fonction est utilisée pour l'affichage de la fonction serveur dans l'espace privé.
 *
 * @api
 * @filtre boussole_lister_caches
 * @pipeline_appel declarer_boussoles()
 *
 * @return array
 */
function boussole_lister_caches() {
	$caches = array();

	$dir_caches = _DIR_VAR . 'cache-boussoles';
	if ($fichiers_cache = glob($dir_caches . "/boussole*.xml")) {
		include_spip('inc/config');
		$boussoles = lire_config('boussole/serveur/boussoles_disponibles');
		$boussoles = pipeline('declarer_boussoles', $boussoles);

		foreach ($fichiers_cache as $_fichier) {
			$cache = array();
			$cache['fichier'] = $_fichier;
			$cache['nom'] = basename($_fichier);
			$cache['maj'] = date('Y-m-d H:i:s', filemtime($_fichier));

			$cache['sha'] = '';
			$cache['plugin'] = '';
			$cache['alias'] = '';
			$cache['manuelle'] = false;

			lire_fichier($_fichier, $contenu);
			$convertir = charger_fonction('simplexml_to_array', 'inc');
			$converti = $convertir(simplexml_load_string($contenu), false);
			$tableau = $converti['root'];
			if ($cache['nom'] == 'boussoles.xml') {
				// C'est le cache qui liste les boussoles hébergées
				$cache['description'] = _T('boussole:info_cache_boussoles');
				if  (isset($tableau['name'])
				AND ($tableau['name'] == 'boussoles')) {
					$cache['sha'] = $tableau['attributes']['sha'];
				}
			}
			else {
				// C'est le cache d'une boussole hébergée
				$alias_boussole = str_replace('boussole-', '', basename($_fichier, '.xml'));
				$cache['alias'] = $alias_boussole;
				$cache['description'] = _T('boussole:info_cache_boussole', array('boussole' => $alias_boussole));
				if  (isset($tableau['name'])
				AND ($tableau['name'] == 'boussole')) {
					$cache['sha'] = $tableau['attributes']['sha'];
					$cache['nom'] .= " ({$tableau['attributes']['version']})";
				}
				if (isset($boussoles[$alias_boussole]['prefixe'])
				AND ($boussoles[$alias_boussole]['prefixe'])) {
					// Boussole utilisant un plugin
					$informer = charger_fonction('informer_plugin', 'inc');
					$infos = $informer($boussoles[$alias_boussole]['prefixe']);
					if ($infos)
						$cache['plugin'] = "{$infos['nom']} ({$boussoles[$alias_boussole]['prefixe']}/{$infos['version']})";
				}
				else {
					// Boussole n'utilisant pas un plugin, nommée boussole manuelle
					$cache['manuelle'] = true;
					$cache['plugin'] = _T('boussole:info_boussole_manuelle');

					// Ajout de la version dans le fichier XML source de la boussole
					$fichier_source = find_in_path("boussole_traduite-${alias_boussole}.xml");
					lire_fichier($fichier_source, $contenu_source);
					$tableau_source = $convertir(simplexml_load_string($contenu_source), false);
					$tableau_source = $tableau_source['root'];
					if  (isset($tableau_source['name'])
					AND ($tableau_source['name'] == 'boussole')) {
						$cache['plugin'] .= " ({$tableau_source['attributes']['version']})";
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
 * @filtre boussole_compteur_hebergement
 * @pipeline_appel declarer_boussoles()
 *
 * @return int
 */
function boussole_compteur_hebergement() {
	include_spip('inc/config');
	$boussoles = lire_config('boussole/serveur/boussoles_disponibles');
	$boussoles = pipeline('declarer_boussoles', $boussoles);

	return count($boussoles);
}

?>
