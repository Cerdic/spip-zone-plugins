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
 * Conversion d'un texte en utf-8
 *
 * @param string	$sujet
 * @return string
 */
function entite2utf($sujet) {
	if (!$sujet OR !is_string($sujet)) return;
	include_spip('inc/charsets');

	return unicode_to_utf_8(html_entity_decode(preg_replace('/&([lg]t;)/S', '&amp;\1', $sujet), ENT_NOQUOTES, 'utf-8'));
}


//
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
function langonet_index_brut($occurrence) {
	$index = textebrut($occurrence);
	$index = preg_replace('/\\\\[nt]/', ' ', $index);
	$index = strtolower(translitteration($index));
	$index = trim(preg_replace('/\W+/', ' ', $index));
	$index = preg_replace('/\b(\w+)\W+\1/', '\1', $index);
	if (strlen($index) > 32) {
	  // trop long: abandonner les petits mots
		$index = preg_replace('/\b\w{1,3}\W/', '', $index);
		if (strlen($index) > 32) {
			// tant pis mais couper proprement si possible
			$index = substr($index, 0, 32);
			if ($n = strrpos($index,' ') OR ($n = strrpos($index,'_')))
				$index = substr($index, 0, $n);
		}
	}
	$index = str_replace(' ', '_', trim($index));

	return $index;
}


/**
 * Calcul du représentation canonique d'une chaine de langue à créer avec traitement d'homonynie.
 * En cas d'homonynmie, le représentant utilisé est le md5.
 *
 * @param string	$occurrence
 * @param array		$item_md5
 * @return string
 */
function langonet_index($occurrence, $item_md5) {
	// Calcul du raccourci brut de l'item de langue
	$index = langonet_index_brut($occurrence);

	// Si cet item existe déjà mais que la chaine diffère par des majuscules, on considère qu'on a à faire
	// au même item. Sinon c'est que le calcul précédent a donné lieu à une collision inattendue de deux items différents :
	// on prend alors son md5 mais qui produira un raccourci illisible
	if (isset($item_md5[$index])) {
		if (strcasecmp($item_md5[$index], $occurrence) != 0)
			$index = md5($occurrence);
	}

	return $index;
}


/**
 * Calcul du représentation canonique d'une chaine de langue à créer avec traitement d'homonynie.
 * En cas d'homonynmie, le représentant utilisé est le md5.
 *
 * @param string	$occurrence
 * @param array		$item_md5
 * @return string
 */
function langonet_calculer_raccourci($occurrence, $item_md5) {
	// Calcul du raccourci brut de l'item de langue
	$index_brut = langonet_index_brut($occurrence);
	$index = $index_brut;

	// Si cet item existe déjà mais que la chaine diffère par des majuscules, on considère qu'on a à faire
	// au même item. Sinon c'est que le calcul précédent a donné lieu à une collision inattendue de deux items différents :
	// on prend alors son md5 mais qui produira un raccourci illisible
	if (isset($item_md5[$index_brut])) {
		if (strcasecmp($item_md5[$index_brut], $occurrence) != 0)
			$index = md5($occurrence);
	}

	return array($index, $index_brut);
}


function langonet_verifier_reference($module, $langue, $ou_langue) {
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


function langonet_trouver_module($ou_fichier) {

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
 * Creation d'un tableau des selects:
 * - des fichiers de langue
 * - des arborescences a scanner
 *
 * @param string $sel_l option du select des langues
 * @param array $sel_d option(s) du select des repertoires
 * @return array
 */
function creer_selects($sel_l='0',$sel_d=array(), $exclure_paquet=true, $multiple=true) {
	// Recuperation des repertoires des plugins par défaut
	$rep_plugins = lister_dossiers_plugins();
	// Recuperation des repertoires des extensions : _DIR_PLUGINS_DIST à partir de SPIP 3
	$rep_extensions = lister_dossiers_plugins(_DIR_PLUGINS_DIST);
	// Recuperation des repertoires des plugins supplémentaires en mutualisation : _DIR_PLUGINS_SUPPL
	$rep_suppl = defined('_DIR_PLUGINS_SUPPL') ? lister_dossiers_plugins(_DIR_PLUGINS_SUPPL) : array();
	// Recuperation des repertoires squelettes perso
	$rep_perso = array();
	$perso = strlen($GLOBALS['dossier_squelettes']) ? explode(':', $GLOBALS['dossier_squelettes']) : array('squelettes');
	foreach($perso as $_rep) {
		if (is_dir(_DIR_RACINE . $_rep))
			$rep_perso[] = $_rep;
	}
	// Recuperation des repertoires SPIP
	$rep_spip[] = rtrim(_DIR_RESTREINT_ABS, '/');
	$rep_spip[] = 'prive';
	$rep_spip[] = 'squelettes-dist';
	$rep_scan = array_merge($rep_perso, $rep_plugins, $rep_suppl, $rep_extensions, $rep_spip);

	// construction des <select>
	// -- les fichiers de langue
	$sel_lang = '<select name="fichier_langue" id="fichier_langue" style="margin-bottom:1em;">'."\n";
	$sel_lang .= '<option value="0"';
	$sel_lang .= ($sel_l == '0') ? ' selected="selected">' : '>';
	$sel_lang .= _T('langonet:option_aucun_fichier') . '</option>' . "\n";
	// -- les racines des arborescences a scanner
	if ($multiple) {
		$sel_dossier = '<select name="dossier_scan[]" id="dossier_scan" multiple="multiple">' . "\n";
	}
	else {
		$sel_dossier = '<select name="dossier_scan" id="dossier_scan">' . "\n";
		$sel_dossier .= '<option value="0"';
		$sel_dossier .= (count($sel_d) == 0) ? ' selected="selected">' : '>';
		$sel_dossier .= _T('langonet:option_aucun_dossier') . '</option>' . "\n";
	}

	// la liste des options :
	// value (fichier_langue) =>
	//     $rep (nom du repertoire parent de lang/)
	//     $module (prefixe fichier de langue)
	//     $langue (index nom de langue)
	//     $ou_lang (chemin relatif vers fichier de langue a verifier)
	foreach ($rep_scan as $_rep) {
		if (in_array($_rep, $rep_plugins)) {
			$reel_dir = _DIR_PLUGINS . $_rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else if (in_array($_rep, $rep_extensions)) {
			$reel_dir = _DIR_PLUGINS_DIST . $_rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else if (in_array($_rep, $rep_suppl)) {
			$reel_dir = _DIR_PLUGINS_SUPPL . $_rep;
			$ou_fichier = str_replace('../', '', $reel_dir) . '/';
		}
		else {
			$reel_dir = _DIR_RACINE . $_rep;
			$ou_fichier = $_rep . '/';
		}

		// on recupere tous les fichiers de langue directement places
		// dans lang/ sans parcourir d'eventuels sous-repertoires. On exclut si demandé ou par défaut
		// les fichiers de langue du paquet.xml
		if (is_dir($reel_dir . '/lang/')) {
			$opt_lang = '';
			foreach ($fic_lang = preg_files($reel_dir . '/lang/', _LANGONET_PATTERN_FICHIERS_LANG, 250, false) as $le_module) {
				preg_match_all(_LANGONET_PATTERN_CODE_LANGUE, str_replace('.php', '', $le_module), $matches);
				$module = str_replace($matches[0][0].'.php', '', $le_module);
				$module = str_replace($reel_dir . '/lang/', '', $module);
				if (!$exclure_paquet
				OR ($exclure_paquet	AND (strtolower(substr($module, 0, 7)) != 'paquet-'))) {
					$langue = ltrim($matches[0][0], '_');
					$ou_langue = str_replace('../', '', $reel_dir) . '/lang/';
					$value = $_rep.':'.$module.':'.$langue.':'.$ou_langue;
					$opt_lang .= '<option value="' . $value;
					$opt_lang .= ($value == $sel_l) ? '" selected="selected">' : '">';
					$opt_lang .= str_replace('.php', '', str_replace($reel_dir . '/lang/', '', $le_module)) . '</option>' . "\n";
				}
			}
			if ($opt_lang) {
				$sel_lang .= '<optgroup label="' . str_replace('../', '', $reel_dir) . '/">' . "\n";
				$sel_lang .= $opt_lang;
				$sel_lang .= '</optgroup>' . "\n";
			}
		}
		$sel_dossier .= '<option value="' . $ou_fichier;
		$sel_dossier .= (in_array($ou_fichier,$sel_d)) ? '" selected="selected">' : '">';
		$sel_dossier .= str_replace('../', '', $reel_dir) . '/</option>' . "\n";
	}

	$sel_lang .= '</select>' . "\n";
	$sel_dossier .= '</select>' . "\n";

	return $retour = array('fichiers' => $sel_lang, 'dossiers' => $sel_dossier);
}


/**
 * Lister tous les plugins
 *
 * @param string $rep_base
 * @return array
 */
// $rep_base  => le repertoire de depart de l'arboresence a scanner
function lister_dossiers_plugins($racine_arborescence=null) {
	include_spip('inc/plugin');
	// liste_plugin_files() integre les repertoires supplementaires de plugins
	// dans le cadre de la mutualisation
	$liste_dossiers = liste_plugin_files($racine_arborescence);
	if (is_null($racine_arborescence))
		$racine_arborescence = _DIR_PLUGINS;
	$dossiers = array();
	foreach ($liste_dossiers as $_dossier) {
		$chemin = $racine_arborescence . $_dossier;
		$dossiers[] = $_dossier;
		if ($liste_sous_dossiers = glob($chemin . '/*/lang', GLOB_ONLYDIR)) {
			for ($i = 0; $i < count($liste_sous_dossiers); $i++) {
    			$dossiers[] = str_replace($racine_arborescence, '', str_replace('/lang', '', $liste_sous_dossiers[$i]));
			}
		}
	}
	return $dossiers;
}

?>