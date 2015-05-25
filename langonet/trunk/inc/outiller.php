<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_LANGONET_PATTERN_REFERENCE'))
	define('_LANGONET_PATTERN_REFERENCE', '#<traduction[^>]*reference="(.*)">#Uims');
if (!defined('_LANGONET_PATTERN_FICHIERS_LANG'))
	define('_LANGONET_PATTERN_FICHIERS_LANG', '_[a-z]{2,3}\.php$');
if (!defined('_LANGONET_PATTERN_FICHIERS_LANG_FR'))
	define('_LANGONET_PATTERN_FICHIERS_LANG_FR', '_fr\.php$');
if (!defined('_LANGONET_PATTERN_CODE_LANGUE'))
	define('_LANGONET_PATTERN_CODE_LANGUE', '%_(\w{2,3})(_\w{2,3})?(_\w{2,4})?$%im');


/**
 * Calcul du représentant canonique d'une chaine de langue (_L ou <: :>).
 * C'est un transcodage ASCII, reduit aux 32 premiers caractères,
 * les caractères non alphabétiques étant remplacés par un souligné.
 * On élimine les répétitions de mots pour évacuer le cas fréquent truc: @truc@.
 * Si le résultat a plus que 32 caractères, on élimine les mots de moins de 3 lettres.
 * Si cela demeure toujours trop, on coupe au dernier mot complet avant 32 caractères.
 *
 * @param string	$occurrence
 * @return string
 */
function calculer_raccourci_brut($occurrence) {
	$raccourci = textebrut($occurrence);
	$raccourci = preg_replace('/\\\\[nt]/', ' ', $raccourci);
	$raccourci = strtolower(translitteration($raccourci));
	$raccourci = trim(preg_replace('/\W+/', ' ', $raccourci));
	$raccourci = preg_replace('/\b(\w+)\W+\1/', '\1', $raccourci);
	if (strlen($raccourci) > 48) {
	  // trop long: abandonner les petits mots
		$raccourci = preg_replace('/\b\w{1,3}\W/', '', $raccourci);
		if (strlen($raccourci) > 48) {
			// tant pis mais couper proprement si possible
			$raccourci = substr($raccourci, 0, 48);
			if ($n = strrpos($raccourci,' ') OR ($n = strrpos($raccourci,'_')))
				$raccourci = substr($raccourci, 0, $n);
		}
	}
	$raccourci = str_replace(' ', '_', trim($raccourci));

	return $raccourci;
}


/**
 * Calcul du représentation canonique d'une chaine de langue à créer avec traitement d'homonynie.
 * En cas d'homonynmie, le représentant utilisé est le md5.
 *
 * @param string	$occurrence
 * @param array		$item_md5
 * @return string
 */
function calculer_raccourci_unique($occurrence, $item_md5) {
	// Calcul du raccourci brut de l'item de langue
	$raccourci_brut = calculer_raccourci_brut($occurrence);
	$raccourci = $raccourci_brut;

	// Si cet item existe déjà mais que la chaine diffère par des majuscules, on considère qu'on a à faire
	// au même item. Sinon c'est que le calcul précédent a donné lieu à une collision inattendue de deux items différents :
	// on prend alors son md5 mais qui produira un raccourci illisible
	if (isset($item_md5[$raccourci_brut])) {
		if (strcasecmp($item_md5[$raccourci_brut], $occurrence) != 0)
			$raccourci = md5($occurrence);
	}

	return array($raccourci, $raccourci_brut);
}


/**
 * @param string $ou_fichier
 * @return array
 */
function trouver_module_langue($ou_fichier) {

	static $modules_spip = array('ecrire/' => 'ecrire', 'prive/' => 'spip', 'squelettes-dist/' => 'public');

	if (in_array($ou_fichier, array_keys($modules_spip))) {
		// On traite le cas de SPIP : on
		$module = $modules_spip[$ou_fichier];
		$langue = 'fr';
		$ou_langue = 'ecrire/lang/';
	}
	else {
		// On traite les autres modules
		// Dans le cas où aucun module n'est trouvé on nomme le module 'indefini' et la langue 'fr'
		$module = 'indefini';
		$langue = 'fr';
		$ou_langue = $ou_fichier . 'lang/';

		if (is_dir(_DIR_RACINE . $ou_langue)) {
			// Le répertoire des langues existe, il devrait y avoir des fichiers de langue traduits avec TradLang ou pas
			$module_trouve = false;
			if ($rapports_xml = glob(_DIR_RACINE . $ou_langue . '*.xml')) {
				// On cherche en premier lieu les rapports XML de traduction car il contiennent aussi la langue de
				// référence et on exclut toujours les fichiers de langue des paquet.xml.
				foreach ($rapports_xml as $_rapport_xml) {
					$module_xml = basename($_rapport_xml, '.xml');
	    			if (strtolower(substr($module_xml, 0, 7)) != 'paquet-') {
						$module = $module_xml;
						// On recherche la langue de référence
						if ($contenu = spip_file_get_contents($_rapport_xml))
							if (preg_match(_LANGONET_PATTERN_REFERENCE, $contenu, $matches))
								$langue = $matches[1];
						$module_trouve = true;
						break;
					}
				}
			}
			if (!$module_trouve) {
				// Tradlang n'est pas utilisé pour les traductions : on cherche donc les fichiers de langue php.
				if ($fichiers_lang = preg_files(_DIR_RACINE . $ou_langue, _LANGONET_PATTERN_FICHIERS_LANG_FR, 250, false)) {
					foreach ($fichiers_lang as $_fichier) {
						$module_lang = str_replace('_fr', '', basename($_fichier, '.php'));
						if (strtolower(substr($module_lang, 0, 7)) != 'paquet-') {
							$module = $module_lang;
							$langue = 'fr';
							$module_trouve = true;
						}
					}
				}

				// Si aucun module fr trouvé alors on prend le premier module de langue en excluant toujours le paquet.
				if (!$module_trouve) {
					if ($fichiers_lang = preg_files(_DIR_RACINE . $ou_langue, _LANGONET_PATTERN_FICHIERS_LANG, 250, false)) {
						include_spip('inc/lang_liste');
						foreach ($fichiers_lang as $_fichier) {
							$nom = basename($_fichier, '.php');
							if (preg_match(_LANGONET_PATTERN_CODE_LANGUE, $nom, $matches)) {
								$module_lang = str_replace($matches[0], '', $nom);
								$code_lang = trim($matches[0], '_');
								if ((strtolower(substr($module_lang, 0, 7)) != 'paquet-')
								AND (array_key_exists($code_lang, $GLOBALS['codes_langues']))) {
									$module = $module_lang;
									$langue = $code_lang;
									$module_trouve = true;
								}
							}
						}
					}
				}
			}
		}
	}

	return array($module, $langue, $ou_langue);
}


/**
 * @param string $module
 * @param string $langue
 * @param string $ou_langue
 * @return array
 */
function verifier_reference_tradlang($module, $langue, $ou_langue) {
	$utilise_tradlang=false;
	$est_langue_reference=false;

	$rapport_xml = _DIR_RACINE . $ou_langue . $module . '.xml';
	if (file_exists($rapport_xml)) {
		$utilise_tradlang = true;
		if ($contenu = spip_file_get_contents($rapport_xml))
			if (preg_match(_LANGONET_PATTERN_REFERENCE, $contenu, $matches))
				$est_langue_reference = ($matches[1] == $langue);
	}

	return array($est_langue_reference, $utilise_tradlang);
}

function lister_modules($langue, $exclure_paquet=true) {
	$liste = array();

	foreach (preg_files(_DIR_RACINE, "/lang/[^/]+_${langue}\.php$") as $_fichier) {
		// On extrait le module
		if (preg_match(",/lang/([^/]+)_${langue}\.php$,i", $_fichier, $module)) {
			// On ajoute le module à la liste : l'index correspond au module et la valeur au dossier
			if (!$exclure_paquet OR ($exclure_paquet
			AND (strtolower(substr($module[1], 0, 7)) != 'paquet-'))) {
				$liste[$module[1]] = dirname($_fichier) . '/';
			}
		}
	}

	return $liste;
}


// ----------------- A VOIR PLUS TARD L'UTILITE ---------------------------

function langonet_trouver_reference($module, $ou_langue, $force=true) {
	$langue_reference = 'fr';
	$tradlang=false;

	// On cherche d'abord si le module est sous tradlang et donc possède un rapport de traduction.
	// Dans ce cas, on connait exactement la langue de référence.
	$rapport_xml = _DIR_RACINE . $ou_langue . $module . '.xml';
	if (file_exists($rapport_xml)) {
		$tradlang = true;
		if ($contenu = spip_file_get_contents($rapport_xml))
			if (preg_match(_LANGONET_PATTERN_REFERENCE, $contenu, $matches))
				$langue_reference = $matches[1];
	}

	// On vérifie que le fichier pour la langue de référence déterminée existe sinon on continue à chercher
	if ($force
	AND (!file_exists($fichier_lang = _DIR_RACINE . $ou_langue . $module . '_' . $langue_reference . '.php'))) {
		$fichiers = preg_files(_DIR_RACINE . $ou_langue, "/lang/${module}_[^/]+\.php$");
		$langue_reference = '';
		if ($fichiers[0])
			$langue_reference = str_replace($module . '_', '', basename($fichiers[0], '.php'));
	}

	return array($langue_reference, $tradlang);
}

?>