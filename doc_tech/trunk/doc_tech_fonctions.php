<?php
/**
 * Fonctions utiles au plugin Documentation technique
 *
 * @plugin     Documentation technique
 * @copyright  2013-2017
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Doc_tech\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Lister tous les objets ayant une chaine de langue de type lang/objet_lang.php
 * Exemple : `lang/projet_fr.php`
 * `lang/forum_fr.php`
 *
 * @return array
 */
function doc_tech_lister_objet() {
	include_spip('base/objets');
	include_spip('inc/config');
	$objets_principales = array_keys(lister_tables_principales());
	// On va prendre la langue du site comme référence pour la langue de l'objet
	$langue_site = lire_config('langue_site');
	$liste_objet = array();

	foreach ($objets_principales as $objet) {
		$type = objet_type($objet);
		// On recherche les objets ayant une chaîne de langue selon le type
		// Exemple : lang/forum_fr.php
		// lang/projet_fr.php
		$lang = find_in_path("lang/" . $type . "_" . $langue_site . ".php");
		if ($lang) {
			$liste_objet[] = $type;
		}
	}

	return $liste_objet;
}


function doc_tech_chaine_langue($objet, $champ, $sufix) {
	$langue_site = $GLOBALS['meta']['langue_site'];
	$traduction = '';
	include_spip('inc/traduire');
	charger_langue($langue_site, $objet);
	/*
	echo "<pre>" . print_r($objet, true) . "\n"
		. print_r($champ, true) . "\n"
		. print_r($sufix, true) . "\n"
		. "</pre>";
	*/
	/**
	 * Dans certains cas, une chaine de langue est bien trouvée par cette fonction. cf. page ?exec=doc_tech
	 * Et dans d'autres cas, cf. ?exec=doc_tech_lang, la chaine de langue 'correcte' n'est pas trouvée. Exemple: objet pays (avec le plugin Pays)
	 */
	if (isset($GLOBALS['i18n_' . $objet . '_' . $langue_site])) {
		if (isset($GLOBALS['i18n_' . $objet . '_' . $langue_site]['champ_' . $champ . '_' . $sufix])) {
			$traduction = $GLOBALS['i18n_' . $objet . '_' . $langue_site]['champ_' . $champ . '_' . $sufix];
		} else if (isset($GLOBALS['i18n_' . $objet . '_' . $langue_site][$champ . '_' . $sufix])) {
				$traduction = $GLOBALS['i18n_' . $objet . '_' . $langue_site][$champ . '_' . $sufix] ;
		} else if (isset($GLOBALS['i18n_' . $objet . '_' . $langue_site][$sufix . '_' . $champ])) {
					$traduction = $GLOBALS['i18n_' . $objet . '_' . $langue_site][$sufix . '_' . $champ];
		}
	}

	return $traduction;
}