<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_BOUSSOLE_PATTERN_SHA'))
	define('_BOUSSOLE_PATTERN_SHA', '%sha_contenu%');

/**
 * Génération du cache xml de la boussole contruit soit à partir de xml non traduit soit à parti d'un xml déjà traduit.
 * Ce cache est renvoyé sur l'action serveur_informer_boussole
 *
 * @api
 *
 * @param string	$alias
 * @param string	$prefixe_plugin
 * @return bool
 */
function boussole_cacher($alias, $prefixe_plugin='') {
	$retour = false;

	/* Détermination du mode de génération du fichier cache xml
		- fichier XML contenant une boussole déjà traduite (pas de DTD possible)
		- fichier XML contenant une boussole source non traduite (conforme à boussole.dtd)
	*/
	if ($fichier_xml = find_in_path("boussole_traduite-${alias}.xml")) {
		// TODO : compléter le cas où du XML boussole déja traduit
	}
	elseif ($fichier_xml = find_in_path("boussole-${alias}.xml")) {
		// Validation du fichier XML source (boussole.dtd)
		include_spip('inc/deboussoler');
		if (!boussole_valider_xml($fichier_xml, $erreur))
			spip_log("XML source non conforme (alias = $alias) : " . var_export($erreur['detail'], true), 'boussole' . _LOG_ERREUR);

		// Création du cache à partir du fichier XML source
		elseif (!xml_to_cache($fichier_xml, $alias, $prefixe_plugin))
			spip_log("Cache XML non créé (alias = $alias)", 'boussole' . _LOG_ERREUR);

		else
			$retour = true;
	}
	else
		spip_log("XML source introuvable (alias = $alias)", 'boussole' . _LOG_ERREUR);

	return $retour;
}


/**
 * Génération du cache de la liste des boussoles disponibles
 * Ce cache est renvoyé sur l'action serveur_lister_boussoles
 *
 * @api
 *
 * @param array $boussoles
 */
function boussole_lister($boussoles) {
	$retour = false;

	if ($boussoles) {
		$cache = '';
		foreach($boussoles as $_alias => $_infos) {
			$fichier_xml = _DIR_VAR . "cache-boussoles/boussole-${_alias}.xml";
			if (file_exists($fichier_xml)) {
				// Extraction des seules informations de la boussole pour créer le cache (pas de groupe ni site)
				lire_fichier($fichier_xml, $xml);
				$convertir = charger_fonction('simplexml_to_array', 'inc');
				$tableau = $convertir(simplexml_load_string($xml), false);

				if  (isset($tableau['name'])
				AND ($tableau['name'] == 'boussole')) {
					$cache .= inserer_balise('ouvrante', $tableau['name'], $tableau['attributes'], 1);
					if (isset($tableau['children']['nom'])) {
						$cache .= inserer_balise('ouvrante', 'nom', '', 2)
								. inserer_balise('ouvrante', 'multi', '', 3)
								. indenter(3) . $tableau['children']['nom'][0]['children']['multi'][0]['text'] . "\n"
								. inserer_balise('fermante', 'multi', '', 3)
								. inserer_balise('fermante', 'nom', '', 2);
					}
					$cache .= inserer_balise('fermante', $tableau['name'], '', 1);
				}
			}
		}

		if ($cache) {
			$cache = inserer_balise('ouvrante', 'boussoles', array('sha' => _BOUSSOLE_PATTERN_SHA))
				   . $cache
				   . inserer_balise('fermante', 'boussoles', '');
			$sha = sha1($cache);
			$cache = str_replace(_BOUSSOLE_PATTERN_SHA, $sha, $cache);

			$dir = sous_repertoire(_DIR_VAR, 'cache-boussoles');
			$fichier_cache = $dir . 'boussoles.xml';
			ecrire_fichier($fichier_cache, $cache);

			$fichier_sha = $fichier_cache . '.sha';
			ecrire_fichier($fichier_sha, sha1_file($fichier_cache));

			$retour = true;
		}
	}

	return $retour;
}


/**
 * Teste la validite du fichier xml de la boussole en fonction de la DTD boussole.dtd
 *
 * @api
 *
 * @param string $url
 * @param array &$erreur
 * @return boolean
 */

// $url	=> url absolue du fichier xml de description de la boussole
// $erreur	=> tableau des erreurs collectees suite a la validation xml
function boussole_valider_xml($url, &$erreur) {
	include_spip('inc/distant');

	$ok = true;

	// On verifie la validite du contenu en fonction de la dtd
	$valider_xml = charger_fonction('valider', 'xml');
	$retour = $valider_xml(recuperer_page($url));
	$erreurs = is_array($retour) ? $retour[1] : $retour->err;
	if ($erreurs === false) {
		$ok = false;
	}
	else if ($erreurs) {
		$erreur['detail'] = $erreurs;
		$ok = false;
	}

	return $ok;
}


/**
 * Lecture du xml non traduit (donc issu d'un plugin) et génération du xml traduit et incluant les logos
 *
 * @param string	$fichier_xml
 * @param string	$alias_boussole
 * @param string	$prefixe_plugin
 * @return bool
 */
function xml_to_cache($fichier_xml, $alias_boussole, $prefixe_plugin) {
	$retour = false;
	$cache = '';

	// Extraction du contenu du xml source
	lire_fichier($fichier_xml, $xml);
	$convertir = charger_fonction('simplexml_to_array', 'inc');
	$tableau = $convertir(simplexml_load_string($xml), false);

	if  (isset($tableau['name'])
	AND ($tableau['name'] == 'boussole')) {
		include_spip('inc/filtres');
		include_spip('inc/filtres_mini');

		// Ouverture de l'élément englobant boussole
		// -- url absolue du logo à fournir dans la balise
		$att_boussole['logo'] = url_absolue(find_in_path("images/boussole/boussole-${alias_boussole}.png"));
		// -- insertion de la version du plugin comme version du xml
		$versionner = charger_filtre('info_plugin');
		$att_boussole['version'] = $versionner(strtoupper($prefixe_plugin), 'version');
		// -- insertion de l'alias du serveur
		$att_boussole['serveur'] = _BOUSSOLE_ALIAS_SERVEUR;
		// -- insertion du pattern pour le sha1 du contenu
		$att_boussole['sha'] = _BOUSSOLE_PATTERN_SHA;
		// -- merge de tous les attributs
		$att_boussole = array_merge($att_boussole, $tableau['attributes']);
		$cache .= inserer_balise('ouvrante', $tableau['name'], $att_boussole);
		// Insertion des balises multi pour le nom, le slogan et le descriptif de la boussole
		$cache .= inserer_traductions($alias_boussole, $tableau['name'], $alias_boussole, 1);

		if (isset($tableau['children']['groupe'])) {
			// Insertion des éléments groupe
			foreach ($tableau['children']['groupe'] as $_groupe) {
				$cache .= inserer_balise('ouvrante', $_groupe['name'], $_groupe['attributes'], 1);
				// Insertion des balises multi pour le nom du groupe
				$cache .= inserer_traductions($alias_boussole, $_groupe['name'], $_groupe['attributes']['type'], 2);

				// Insertion des éléments site du groupe en cours
				if (isset($_groupe['children']['site'])) {
					foreach ($_groupe['children']['site'] as $_site) {
						// -- url absolue du logo à fournir dans la balise
						$alias_site = $_site['attributes']['alias'];
						$att_site['logo'] = url_absolue(find_in_path("images/boussole/site-${alias_boussole}-${alias_site}.png"));
						$att_site = array_merge($att_site, $_site['attributes']);
						$cache .= inserer_balise('ouvrante', $_site['name'], $att_site, 2);
						// Insertion des balises multi pour le nom, le slogan et le descriptif du site
						$cache .= inserer_traductions($alias_boussole, $_site['name'], $alias_site, 3);
						$cache .= inserer_balise('fermante', $_site['name'], '', 2);
					}
				}
				$cache .= inserer_balise('fermante', $_groupe['name'], '', 1);
			}
		}

		// Fermeture de l'élément englobant boussole
		$cache .= inserer_balise('fermante', $tableau['name']);

		// Création du cache et du sha1 associé
		if ($cache) {
			// insertion du sha comme attribut du fichier
			$sha = sha1($cache);
			$cache = str_replace(_BOUSSOLE_PATTERN_SHA, $sha, $cache);

			$dir = sous_repertoire(_DIR_VAR, 'cache-boussoles');
			$fichier_cache = $dir . basename($fichier_xml);
			ecrire_fichier($fichier_cache, $cache);

			$fichier_sha = $fichier_cache . '.sha';
			ecrire_fichier($fichier_sha, sha1_file($fichier_cache));

			$retour = true;
		}
	}

	return $retour;
}


/**
 * Insertion d'un balise ouvrante, fermante ou vide
 *
 * @param string	$type
 * @param string	$balise
 * @param array		$attributs
 * @param int		$indentation
 * @return string
 */
function inserer_balise($type='ouvrante', $balise, $attributs=array(), $indentation=0) {
	// Ouverture de la balise
	$texte = indenter($indentation) . '<' . ($type == 'fermante' ? '/' : '') . $balise;
	// Insertion des attributs
	if ($attributs) {
		foreach ($attributs as $_nom => $_valeur) {
			$texte .= ' ' . $_nom . '="' . $_valeur . '"';
		}
	}
	// Fermeture de la balise
	if ($type == 'vide')
		$texte .= " />\n";
	else
		$texte .= ">\n";

	return $texte;
}


/**
 * Insertion d'une balise complète <nom>, <slogan> ou <description> incluant les traductions en <multi>
 *
 * @param string	$alias
 * @param string	$type_objet
 * @param string	$objet
 * @param string	$indentation
 * @return string
 */
function inserer_traductions($alias, $type_objet, $objet, $indentation=0) {
	$multis = '';

	if ($fichier_fr = find_in_path("/lang/boussole-${alias}_fr.php")) {
		// Determination du nom du module, du prefixe et des items de langue
		$item_nom = "nom_${type_objet}_${alias}" . ($type_objet != 'boussole' ? "_${objet}" : '');
		$item_slogan = "slogan_${type_objet}_${alias}" . ($type_objet != 'boussole' ? "_${objet}" : '');
		$item_description = "descriptif_${type_objet}_${alias}" . ($type_objet != 'boussole' ? "_${objet}" : '');

		// On cherche tous les fichiers de langue destines a la traduction du paquet.xml
		if ($fichiers_langue = glob(str_replace('_fr.php', '_*.php', $fichier_fr))) {
			$nom = $slogan = $description = '';
			include_spip('inc/lang_liste');

			foreach ($fichiers_langue as $_fichier_langue) {
				$nom_fichier = basename($_fichier_langue, '.php');
				$langue = substr($nom_fichier, strlen("boussole-${alias}") + 1 - strlen($nom_fichier));
				// Si la langue est reconnue, on traite la liste des items de langue
				if (isset($GLOBALS['codes_langues'][$langue])) {
					$GLOBALS['idx_lang'] = $langue;
 					include($_fichier_langue);
					if (isset($GLOBALS[$langue][$item_nom]))
						$nom .= ($nom ? "\n" : '') . indenter($indentation+2) . "[$langue]" . $GLOBALS[$langue][$item_nom];
					if (isset($GLOBALS[$langue][$item_slogan]))
						$slogan .= ($slogan ? "\n" : '') . indenter($indentation+2) . "[$langue]" . $GLOBALS[$langue][$item_slogan];
					if (isset($GLOBALS[$langue][$item_description]))
						$description .= ($description ? "\n" : '') . indenter($indentation+2) . "[$langue]" . $GLOBALS[$langue][$item_description];
				}
			}

			// Finaliser la construction des balises multi
			if ($nom)
				$multis .= inserer_balise('ouvrante', 'nom', '', $indentation)
						 . inserer_balise('ouvrante', 'multi', '', $indentation+1)
						 . $nom . "\n"
						 . inserer_balise('fermante', 'multi', '', $indentation+1)
						 . inserer_balise('fermante', 'nom', '', $indentation);
			if ($slogan)
				$multis .= inserer_balise('ouvrante', 'slogan', '', $indentation)
						 . inserer_balise('ouvrante', 'multi', '', $indentation+1)
						 . $slogan . "\n"
						 . inserer_balise('fermante', 'multi', '', $indentation+1)
						 . inserer_balise('fermante', 'slogan', '', $indentation);
			if ($description)
				$multis .= inserer_balise('ouvrante', 'description', '', $indentation)
						 . inserer_balise('ouvrante', 'multi', '', $indentation+1)
						 . $description . "\n"
						 . inserer_balise('fermante', 'multi', '', $indentation+1)
						 . inserer_balise('fermante', 'description', '', $indentation);
		}
	}

	return $multis;
}


/**
 * Contruction de la chaine de tabulations correspondant au décalage souhaité
 *
 * @param int	$decalage
 * @return string
 */
function indenter($decalage) {
	return str_repeat("\t", $decalage);
}

?>
