<?php
/**
 * Ce fichier contient l'API de gestion des caches des boussoles hébergées par le site serveur.
 *
 * @package SPIP\BOUSSOLE\Serveur\Cache
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


if (!defined('_BOUSSOLE_PATTERN_SHA'))
	/**
	 * Pattern permettant d'insérer le sha256 calculé à partir du XML d'origine d'une boussole
	 * dans le cache produit */
	define('_BOUSSOLE_PATTERN_SHA', '%sha_contenu%');


/**
 * Génération du cache de chaque boussole hébergée par le serveur et du cache de la liste
 * de ces boussoles.
 *
 * @api
 * @uses boussole_cacher_xml()
 * @uses boussole_cacher_liste()
 *
 * @return void
 */
function boussole_actualiser_caches() {

	// Suppression de tous les caches afin de ne pas conserver des boussoles qui ne sont plus disponibles
	$dir_caches = _DIR_VAR . 'cache-boussoles';
	if ($fichiers_cache = glob($dir_caches . "/*.*")) {
		foreach ($fichiers_cache as $_fichier) {
			supprimer_fichier($_fichier);
		}
	}

	// Acquisition de la liste des boussoles disponibles sur le serveur.
	// (on sait déjà que le mode serveur est actif)
	include_spip('inc/config');
	$boussoles = lire_config('boussole/serveur/boussoles_disponibles');
	$boussoles = pipeline('declarer_boussoles', $boussoles);

	if ($boussoles) {
		// Génération du cache de chaque boussole disponible pour l'action serveur_informer_boussole
		foreach($boussoles as $_alias => $_infos) {
			boussole_cacher_xml($_alias, $_infos['prefixe']);
		}

		// Génération du cache de la liste des boussoles disponibles pour l'action serveur_lister_boussoles
		boussole_cacher_liste($boussoles);
	}
}


/**
 * Génération du cache xml de la boussole contruit soit à partir de xml non traduit soit à partir d'un xml déjà traduit.
 * Ce cache est renvoyé sur l'action serveur_informer_boussole
 *
 * @api
 *
 * @param string	$alias
 * @param string	$prefixe_plugin
 * @return bool
 */
function boussole_cacher_xml($alias, $prefixe_plugin='') {
	$retour = false;

	/* Détermination du mode de génération du fichier cache xml
		- fichier XML contenant une boussole déjà traduite (pas de DTD possible)
		- fichier XML contenant une boussole source non traduite (conforme à boussole.dtd)
	*/
	if ($fichier_xml = find_in_path("boussole_traduite-${alias}.xml")) {
		if (!xmltraduit_to_cache($fichier_xml, $alias))
			spip_log("Cache XML non créé (alias = $alias)", _BOUSSOLE_LOG . _LOG_ERREUR);

		else
			$retour = true;
	}
	elseif ($fichier_xml = find_in_path("boussole-${alias}.xml")) {
		// Validation du fichier XML source (boussole.dtd)
		include_spip('inc/client');
		if (!boussole_valider_xml($fichier_xml, $erreur))
			spip_log("XML source non conforme (alias = $alias) : " . var_export($erreur['detail'], true), _BOUSSOLE_LOG . _LOG_ERREUR);

		// Création du cache à partir du fichier XML source
		elseif (!xml_to_cache($fichier_xml, $alias, $prefixe_plugin))
			spip_log("Cache XML non créé (alias = $alias)", _BOUSSOLE_LOG . _LOG_ERREUR);

		else
			$retour = true;
	}
	else
		spip_log("XML source introuvable (alias = $alias)", _BOUSSOLE_LOG . _LOG_ERREUR);

	return $retour;
}


/**
 * Génération du cache de la liste des boussoles disponibles
 * Ce cache est renvoyé sur l'action serveur_lister_boussoles
 *
 * @api
 *
 * @param array $boussoles
 *
 * @return bool
 */
function boussole_cacher_liste($boussoles) {
	$retour = false;

	if ($boussoles) {
		$convertir = charger_fonction('decoder_xml', 'inc');
		$cache = '';
		foreach($boussoles as $_alias => $_infos) {
			$fichier_xml = _DIR_VAR . "cache-boussoles/boussole-${_alias}.xml";
			if (file_exists($fichier_xml)) {
				// Extraction des seules informations de la boussole pour créer le cache (pas de groupe ni site)
				lire_fichier($fichier_xml, $xml);
				$tableau = $convertir($xml);

				if  (isset($tableau[_BOUSSOLE_NOMTAG_BOUSSOLE])) {
					$cache .= inserer_balise('ouvrante', _BOUSSOLE_NOMTAG_BOUSSOLE, $tableau['boussole']['@attributes'], 1);
					if (isset($tableau[_BOUSSOLE_NOMTAG_BOUSSOLE]['nom'])) {
						$cache .= inserer_balise('ouvrante', 'nom', array(), 2)
								. inserer_balise('ouvrante', 'multi', array(), 3)
								. indenter(3) . trim($tableau[_BOUSSOLE_NOMTAG_BOUSSOLE]['nom']['multi']) . "\n"
								. inserer_balise('fermante', 'multi', array(), 3)
								. inserer_balise('fermante', 'nom', array(), 2);
					}
					$cache .= inserer_balise('fermante', _BOUSSOLE_NOMTAG_BOUSSOLE, array(), 1);
				}
			}
		}

		if ($cache) {
			// Récupération du nom du serveur. On sait que le serveur et actif.
			include_spip('inc/config');
			$nom_serveur = lire_config('boussole/serveur/nom');

			$cache = inserer_balise('ouvrante', _BOUSSOLE_NOMTAG_LISTE_BOUSSOLES, array('serveur' => $nom_serveur, 'sha' => _BOUSSOLE_PATTERN_SHA))
				   . $cache
				   . inserer_balise('fermante', _BOUSSOLE_NOMTAG_LISTE_BOUSSOLES, array());
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
 * @package	SPIP\BOUSSOLE\Outils\XML
 * @api
 *
 * @param string $url
 * 		url absolue du fichier xml de description de la boussole
 * @param array &$erreur
 * 		tableau des erreurs collectees suite a la validation xml
 *
 * @return boolean
 */
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
 * @package	SPIP\BOUSSOLE\Serveur\Cache
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
	$convertir = charger_fonction('decoder_xml', 'inc');
	$tableau = $convertir($xml);

	if  (isset($tableau[_BOUSSOLE_NOMTAG_BOUSSOLE])) {
		$tableau = $tableau[_BOUSSOLE_NOMTAG_BOUSSOLE];
		include_spip('inc/filtres');
		include_spip('inc/filtres_mini');

		// Ouverture de l'élément englobant boussole
		// -- url absolue du logo à fournir dans la balise
		$att_boussole =array();
		$att_boussole['logo'] = url_absolue(find_in_path("images/boussole/boussole-${alias_boussole}.png"));
		// -- insertion de la version du plugin comme version du xml
		$informer = charger_fonction('informer_plugin', 'inc');
		$plugin = $informer($prefixe_plugin);
		$att_boussole['version'] = (isset($plugin['version']) ? $plugin['version'] : '');
		// -- insertion de l'alias du serveur
		include_spip('inc/config');
		$nom_serveur = lire_config('boussole/serveur/nom');
		$att_boussole['serveur'] = $nom_serveur;
		// -- insertion du pattern pour le sha1 du contenu
		$att_boussole['sha'] = _BOUSSOLE_PATTERN_SHA;
		// -- merge de tous les attributs
		$att_boussole = array_merge($att_boussole, $tableau['@attributes']);
		$cache .= inserer_balise('ouvrante', _BOUSSOLE_NOMTAG_BOUSSOLE, $att_boussole);
		// Insertion des balises multi pour le nom, le slogan et le descriptif de la boussole
		$cache .= inserer_traductions($alias_boussole, _BOUSSOLE_OBJET_BOUSSOLE, $alias_boussole, 1);

		if (isset($tableau[_BOUSSOLE_NOMTAG_GROUPE])) {
			$groupes = array();
			if (isset($tableau[_BOUSSOLE_NOMTAG_GROUPE][0]))
				$groupes = $tableau[_BOUSSOLE_NOMTAG_GROUPE];
			else
				$groupes[0] = $tableau[_BOUSSOLE_NOMTAG_GROUPE];
			// Insertion des éléments groupe
			foreach ($groupes as $_groupe) {
				$cache .= inserer_balise('ouvrante', _BOUSSOLE_NOMTAG_GROUPE, $_groupe['@attributes'], 1);
				// Insertion des balises multi pour le nom du groupe
				$cache .= inserer_traductions($alias_boussole, _BOUSSOLE_OBJET_GROUPE, $_groupe['@attributes']['type'], 2);

				// Insertion des éléments site du groupe en cours
				if (isset($_groupe[_BOUSSOLE_NOMTAG_SITE])) {
					$sites = array();
					if (isset($_groupe[_BOUSSOLE_NOMTAG_SITE][0]))
						$sites = $_groupe[_BOUSSOLE_NOMTAG_SITE];
					else
						$sites[0] = $_groupe[_BOUSSOLE_NOMTAG_SITE];
					foreach ($sites as $_site) {
						$att_site =array();
						// -- url absolue du logo à fournir dans la balise
						$alias_site = $_site['@attributes']['alias'];
						$att_site['logo'] = url_absolue(find_in_path("images/boussole/site-${alias_boussole}-${alias_site}.png"));
						$att_site = array_merge($att_site, $_site['@attributes']);
						$cache .= inserer_balise('ouvrante', _BOUSSOLE_NOMTAG_SITE, $att_site, 2);
						// Insertion des balises multi pour le nom, le slogan et le descriptif du site
						$cache .= inserer_traductions($alias_boussole, _BOUSSOLE_OBJET_SITE, $alias_site, 3);
						$cache .= inserer_balise('fermante', _BOUSSOLE_NOMTAG_SITE, array(), 2);
					}
				}
				$cache .= inserer_balise('fermante', _BOUSSOLE_NOMTAG_GROUPE, array(), 1);
			}
		}

		// Fermeture de l'élément englobant boussole
		$cache .= inserer_balise('fermante', _BOUSSOLE_NOMTAG_BOUSSOLE);

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
 * Lecture du xml traduit (donc non issu d'un plugin) et génération du xml complet incluant les logos
 *
 * @package	SPIP\BOUSSOLE\Serveur\Cache
 *
 * @param string	$fichier_xml
 * @param string	$alias_boussole
 *
 * @return bool
 */
function xmltraduit_to_cache($fichier_xml, $alias_boussole) {
	$retour = false;
	$cache = '';

	// Extraction du contenu du xml source
	lire_fichier($fichier_xml, $xml);
	$convertir = charger_fonction('decoder_xml', 'inc');
	$tableau = $convertir($xml);

	if  (isset($tableau[_BOUSSOLE_NOMTAG_BOUSSOLE])) {
		$tableau = $tableau[_BOUSSOLE_NOMTAG_BOUSSOLE];
		include_spip('inc/filtres');
		include_spip('inc/filtres_mini');

		// Ouverture de l'élément englobant boussole
		// -- url absolue du logo à fournir dans la balise
		$att_boussole =array();
		$att_boussole['logo'] = url_absolue(find_in_path("images/boussole/boussole-${alias_boussole}.png"));
		// -- insertion de l'alias du serveur
		include_spip('inc/config');
		$nom_serveur = lire_config('boussole/serveur/nom');
		$att_boussole['serveur'] = $nom_serveur;
		// -- insertion du pattern pour le sha1 du contenu
		$att_boussole['sha'] = _BOUSSOLE_PATTERN_SHA;
		// -- merge de tous les attributs
		$att_boussole = array_merge($att_boussole, $tableau['@attributes']);
		$cache .= inserer_balise('ouvrante', _BOUSSOLE_NOMTAG_BOUSSOLE, $att_boussole);
		// Insertion des balises multi pour le nom, le slogan et le descriptif de la boussole
		if (isset($tableau['nom'])) {
			$cache .= inserer_balise('ouvrante', 'nom', array(), 1)
					. inserer_balise('ouvrante', 'multi', array(), 2)
					. indenter(2) . trim($tableau['nom']['multi']) . "\n"
					. inserer_balise('fermante', 'multi', array(), 2)
					. inserer_balise('fermante', 'nom', array(), 1);
		}
		if (isset($tableau['slogan'])) {
			$cache .= inserer_balise('ouvrante', 'slogan', array(), 1)
					. inserer_balise('ouvrante', 'multi', array(), 2)
					. indenter(2) . trim($tableau['slogan']['multi']) . "\n"
					. inserer_balise('fermante', 'multi', array(), 2)
					. inserer_balise('fermante', 'slogan', array(), 1);
		}
		if (isset($tableau['description'])) {
			$cache .= inserer_balise('ouvrante', 'description', array(), 1)
					. inserer_balise('ouvrante', 'multi', array(), 2)
					. indenter(2) . trim($tableau['description']['multi']) . "\n"
					. inserer_balise('fermante', 'multi', array(), 2)
					. inserer_balise('fermante', 'description', array(), 1);
		}

		if (isset($tableau[_BOUSSOLE_NOMTAG_GROUPE])) {
			$groupes = array();
			if (isset($tableau[_BOUSSOLE_NOMTAG_GROUPE][0]))
				$groupes = $tableau[_BOUSSOLE_NOMTAG_GROUPE];
			else
				$groupes[0] = $tableau[_BOUSSOLE_NOMTAG_GROUPE];
			// Insertion des éléments groupe
			foreach ($groupes as $_groupe) {
				$cache .= inserer_balise('ouvrante', _BOUSSOLE_NOMTAG_GROUPE, $_groupe['@attributes'], 1);
				// Insertion des balises multi pour le nom du groupe
				if (isset($_groupe['nom'])) {
					$cache .= inserer_balise('ouvrante', 'nom', array(), 2)
							. inserer_balise('ouvrante', 'multi', array(), 3)
							. indenter(3) . trim($_groupe['nom']['multi']) . "\n"
							. inserer_balise('fermante', 'multi', array(), 3)
							. inserer_balise('fermante', 'nom', array(), 2);
				}
				if (isset($_groupe['slogan'])) {
					$cache .= inserer_balise('ouvrante', 'slogan', array(), 2)
							. inserer_balise('ouvrante', 'multi', array(), 3)
							. indenter(3) . trim($_groupe['slogan']['multi']) . "\n"
							. inserer_balise('fermante', 'multi', array(), 3)
							. inserer_balise('fermante', 'slogan', array(), 2);
				}

				// Insertion des éléments site du groupe en cours
				if (isset($_groupe[_BOUSSOLE_NOMTAG_SITE])) {
					$sites = array();
					if (isset($_groupe[_BOUSSOLE_NOMTAG_SITE][0]))
						$sites = $_groupe[_BOUSSOLE_NOMTAG_SITE];
					else
						$sites[0] = $_groupe[_BOUSSOLE_NOMTAG_SITE];
					foreach ($sites as $_site) {
						$att_site =array();
						// -- url absolue du logo à fournir dans la balise
						$alias_site = $_site['@attributes']['alias'];
						$att_site['logo'] = url_absolue(find_in_path("images/boussole/site-${alias_boussole}-${alias_site}.png"));
						$att_site = array_merge($att_site, $_site['@attributes']);
						$cache .= inserer_balise('ouvrante', _BOUSSOLE_NOMTAG_SITE, $att_site, 2);
						// Insertion des balises multi pour le nom, le slogan et le descriptif du site
						if (isset($_site['nom'])) {
							$cache .= inserer_balise('ouvrante', 'nom', array(), 3)
									. inserer_balise('ouvrante', 'multi', array(), 4)
									. indenter(4) . trim($_site['nom']['multi']) . "\n"
									. inserer_balise('fermante', 'multi', array(), 4)
									. inserer_balise('fermante', 'nom', array(), 3);
						}
						if (isset($_site['slogan'])) {
							$cache .= inserer_balise('ouvrante', 'slogan', array(), 3)
									. inserer_balise('ouvrante', 'multi', array(), 4)
									. indenter(4) . trim($_site['slogan']['multi']) . "\n"
									. inserer_balise('fermante', 'multi', array(), 4)
									. inserer_balise('fermante', 'slogan', array(), 3);
						}
						if (isset($_site['description'])) {
							$cache .= inserer_balise('ouvrante', 'description', array(), 3)
									. inserer_balise('ouvrante', 'multi', array(), 4)
									. indenter(4) . trim($_site['description']['multi']) . "\n"
									. inserer_balise('fermante', 'multi', array(), 4)
									. inserer_balise('fermante', 'description', array(), 3);
						}
						$cache .= inserer_balise('fermante', _BOUSSOLE_NOMTAG_SITE, array(), 2);
					}
				}
				$cache .= inserer_balise('fermante', _BOUSSOLE_NOMTAG_GROUPE, array(), 1);
			}
		}

		// Fermeture de l'élément englobant boussole
		$cache .= inserer_balise('fermante', _BOUSSOLE_NOMTAG_BOUSSOLE);

		// Création du cache et du sha1 associé
		if ($cache) {
			// insertion du sha comme attribut du fichier
			$sha = sha1($cache);
			$cache = str_replace(_BOUSSOLE_PATTERN_SHA, $sha, $cache);

			$dir = sous_repertoire(_DIR_VAR, 'cache-boussoles');
			$fichier_cache = $dir . str_replace('_traduite', '', basename($fichier_xml));
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
 * @package	SPIP\BOUSSOLE\Outils\XML
 *
 * @param string	$type
 * @param string	$nom_balise
 * @param array		$attributs
 * @param int		$indentation
 *
 * @return string
 */
function inserer_balise($type='ouvrante', $nom_balise, $attributs=array(), $indentation=0) {

	// Ouverture de la balise
	$texte = indenter($indentation) . '<' . ($type == 'fermante' ? '/' : '') . $nom_balise;
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
 * @package	SPIP\BOUSSOLE\Outils\XML
 *
 * @param string	$alias_boussole
 * @param string	$type_objet
 * @param string	$alias_objet
 * @param string	$indentation
 *
 * @return string
 */
function inserer_traductions($alias_boussole, $type_objet, $alias_objet, $indentation=0) {
	$multis = '';

	if ($fichier_fr = find_in_path("/lang/boussole-${alias_boussole}_fr.php")) {
		// Determination du nom du module, du prefixe et des items de langue
		$item_nom = "nom_${type_objet}_${alias_boussole}" . ($type_objet != 'boussole' ? "_${alias_objet}" : '');
		$item_slogan = "slogan_${type_objet}_${alias_boussole}" . ($type_objet != 'boussole' ? "_${alias_objet}" : '');
		$item_description = "descriptif_${type_objet}_${alias_boussole}" . ($type_objet != 'boussole' ? "_${alias_objet}" : '');

		// On cherche tous les fichiers de langue destines a la traduction du paquet.xml
		if ($fichiers_langue = glob(str_replace('_fr.php', '_*.php', $fichier_fr))) {
			$nom = $slogan = $description = '';
			include_spip('inc/lang_liste');

			foreach ($fichiers_langue as $_fichier_langue) {
				$nom_fichier = basename($_fichier_langue, '.php');
				$langue = substr($nom_fichier, strlen("boussole-${alias_boussole}") + 1 - strlen($nom_fichier));
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
				$multis .= inserer_balise('ouvrante', 'nom', array(), $indentation)
						 . inserer_balise('ouvrante', 'multi', array(), $indentation+1)
						 . $nom . "\n"
						 . inserer_balise('fermante', 'multi', array(), $indentation+1)
						 . inserer_balise('fermante', 'nom', array(), $indentation);
			if ($slogan)
				$multis .= inserer_balise('ouvrante', 'slogan', array(), $indentation)
						 . inserer_balise('ouvrante', 'multi', array(), $indentation+1)
						 . $slogan . "\n"
						 . inserer_balise('fermante', 'multi', array(), $indentation+1)
						 . inserer_balise('fermante', 'slogan', array(), $indentation);
			if ($description)
				$multis .= inserer_balise('ouvrante', 'description', array(), $indentation)
						 . inserer_balise('ouvrante', 'multi', array(), $indentation+1)
						 . $description . "\n"
						 . inserer_balise('fermante', 'multi', array(), $indentation+1)
						 . inserer_balise('fermante', 'description', array(), $indentation);
		}
	}

	return $multis;
}


/**
 * Contruction de la chaine de tabulations correspondant au décalage souhaité
 *
 * @package	SPIP\BOUSSOLE\Outils\XML
 *
 * @param int	$decalage
 *
 * @return string
 */
function indenter($decalage) {
	return str_repeat("\t", $decalage);
}

?>
