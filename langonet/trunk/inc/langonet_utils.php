<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_LANGONET_PATTERN_REFERENCE'))
	define('_LANGONET_PATTERN_REFERENCE', '#<traduction[^>]*reference="(.*)">#Uims');


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
		$module = 'indefini';
		$langue = 'fr';
		$ou_langue = $ou_fichier . 'lang/';

		if (is_dir(_DIR_RACINE . $ou_langue)) {
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
						break;
					}
				}
			}
			else {

			}
		}
	}

	return array($module, $langue, $ou_langue);
}

?>