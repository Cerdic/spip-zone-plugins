<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// ---------------------------------------
// FONCTIONS
// ---------------------------------------

function make_google_map_proprietaire($conf, $who = 'proprietaire') {
	$str = $google_str = '';
	if (isset($conf[$who.'_adresse_rue']) and strlen($conf[$who.'_adresse_rue'])) {
		$str .= str_replace(array(',', ';', '.', ':', '/'), '', strip_tags($conf[$who.'_adresse_rue']));
	}
	if (isset($conf[$who.'_adresse_code_postal']) and strlen($conf[$who.'_adresse_code_postal'])) {
		$str .= ' '.str_replace(array(',', ';', '.', ':', '/'), '', strip_tags($conf[$who.'_adresse_code_postal']));
	}
	if (isset($conf[$who.'_adresse_ville']) and strlen($conf[$who.'_adresse_ville'])) {
		$str .= ' '.str_replace(array(',', ';', '.', ':', '/'), '', strip_tags($conf[$who.'_adresse_ville']));
	}
	if (strlen($str)) {
		$entries = explode(' ', $str);
		foreach ($entries as $entry) {
			if (strlen($entry)) {
				$google_str .= urlencode($entry).'+';
			}
		}
		$google_str = trim($google_str, '+');

		return $google_str;
	}

	return false;
}

// ---------------------------------------
// CONFIG
// ---------------------------------------

function spip_proprio_enregistrer_config($args) {
	if (!is_array($args)) {
		return;
	}
	$mess = array();
	$_conf = spip_proprio_recuperer_config();
	$conf = $_conf ? array_merge($_conf, $args) : $args;
	include_spip('inc/meta');
	ecrire_meta(_META_SPIP_PROPRIO, serialize($conf), 'non');
	ecrire_metas();

	return true;
}

function spip_proprio_recuperer_config($str = '') {
	if (!isset($GLOBALS['meta'][_META_SPIP_PROPRIO])) {
		return;
	}
	$_conf = unserialize($GLOBALS['meta'][_META_SPIP_PROPRIO]);
	if (strlen($str)) {
		if (isset($_conf[$str])) {
			return $_conf[$str];
		}

		return false;
	}

	return $_conf;
}

/**
 * Choix par defaut des options de presentation pour les formulaires.
 */
function spip_proprio_form_config() {
	global $spip_ecran, $spip_lang, $spip_display;
	$config = $GLOBALS['meta'];
	$config['lignes'] = ($spip_ecran == 'large') ? 8 : 5;
	$config['lignes_longues'] = $config['lignes'] + 15;
	$config['afficher_barre'] = $spip_display != 4;
	$config['langue'] = $spip_lang;
	$config['_browser_caret'] = isset($GLOBALS['browser_caret']) ? $GLOBALS['browser_caret'] : '';

	return $config;
}

// ---------------------------------------
// TEXTES PROPRIETAIRE
// ---------------------------------------

/**
 * Fonction de gestion des textes proprietaire.
 */
function spip_proprio_proprietaire_texte($str = '', $args = array(), $langdef = 'fr') {
	$souvenir = $GLOBALS['spip_lang'];
	$GLOBALS['spip_lang'] = $langdef;

	// Verification que la langue existe
//	$test = _T('proprietaire:exemple');
	// Ne fonctionne pas correctement avec '_T', on reprend la traduction pure de SPIP
	static $traduire = false;
	if (!$traduire) {
		$traduire = charger_fonction('traduire', 'inc');
		include_spip('inc/lang');
	}
	$text = $traduire('proprietaire:test_fichier_langue', $GLOBALS['spip_lang']);

	if (!isset($GLOBALS['i18n_proprietaire_'.$langdef])) {
		$test = _T('texteslegaux:exemple');
		creer_fichier_textes_proprietaire($GLOBALS['i18n_texteslegaux_'.$langdef], $langdef);
		$GLOBALS['i18n_proprietaire_'.$langdef] = $GLOBALS['i18n_texteslegaux_'.$langdef];
	}
	$GLOBALS['spip_lang'] = $souvenir;

	return _T('proprietaire:'.$str, $args);
}

/**
 * Creation de tous les fichiers de langue 'proprietaire_XX'
 * pour toutes les langues utilisees en partie publique.
 */
function spip_proprio_charger_toutes_les_langues() {
	// on force le chargement de proprietaire_XX si present
	// pour toutes les langues utilisees en partie publique
	$langues_du_site = array('fr');
	foreach (array('langues_utilisees', 'langues_multilingue', 'langue_site') as $ln_meta) {
		if (isset($GLOBALS['meta'][$ln_meta])) {
			$langues_du_site = array_merge($langues_du_site, explode(',', $GLOBALS['meta'][$ln_meta]));
		}
	}
	$langues_du_site = array_unique($langues_du_site);
	foreach ($langues_du_site as $ln) {
		spip_proprio_proprietaire_texte('', '', $ln);
	}

	return;
}

function textes_proprietaire($array = false, $lang = null) {
	if (is_null($lang)) {
		$lang = $GLOBALS['spip_lang'];
	}
	$globale = 'i18n_proprietaire_'.$lang;

	$ok1 = spip_proprio_proprietaire_texte();
	if ($lang != 'fr') {
		$ok2 = spip_proprio_proprietaire_texte('', '', $lang);
	}
	if (isset($GLOBALS["$globale"])) {
		$GLOBALS["$globale"] = array_merge($GLOBALS['i18n_proprietaire_fr'], $GLOBALS["$globale"]);
	} else {
		$GLOBALS["$globale"] = $GLOBALS['i18n_proprietaire_fr'];
	}
	if ($array) {
		return $GLOBALS["$globale"];
	}

	return false;
}

function charger_textes_proprietaire($bloc = true) {
	include_spip('inc/presentation');
	include_spip('inc/texte');
	$div = '';

	$valeurs = array();
	$tableau = textes_proprietaire(true);
	if (isset($tableau) and is_array($tableau)) {
		ksort($tableau);
		if ($bloc) {
			$div .= debut_cadre_relief('', true, '', '', 'raccourcis');
		}
		$div .= "\n<table class='spip' style='border:0;'>";
		$div .= "\n<tr class='titrem'><th class='verdana1'>"._T('module_raccourci')."</th>\n<th class='verdana2'>"._T('module_texte_affiche')."</th></tr>\n";
		$i = 0;
		foreach ($tableau as $raccourci => $val) {
			$bgcolor = alterner(++$i, 'row_even', 'row_odd');
			$div .= "\n<tr class='$bgcolor'><td class='verdana2' style='min-width:150px;'>"
				."<a href='".generer_url_ecrire('spip_proprio_textes', 'raccourci='.$raccourci)."' title='"._T('spipproprio:ptexte_cliquez_pour_editer')."'><b>$raccourci</b></td>\n"
				."<td id='$raccourci' class='arial2 editable' style='min-width:300px;'>".propre($val).'</td></tr>';
		}
		$div .= '</table>';
		if ($bloc) {
			$div .= fin_cadre_relief(true);
		}
	}

	return $div;
}

function traiter_textes_proprietaire($raccourci, $lang = 'fr') {
	include_spip('inc/texte');
	$valeur = _request('value');
	$array_langue = textes_proprietaire(true);
//	$valeur = propre( $valeur );
	if (strlen($valeur)) {
		$array_langue[$raccourci] = $valeur;
	} elseif (isset($array_langue[$raccourci])) {
		unset($array_langue[$raccourci]);
	}
	if ($ok = creer_fichier_textes_proprietaire($array_langue, $lang)) {
		return $valeur;
	}

	return false;
}

function creer_fichier_textes_proprietaire($array_langue, $lang = 'fr') {
	$file = 'proprietaire_'.$lang;
	$globale = 'i18n_proprietaire_'.$lang;
	if (!file_exists(find_in_path('lang/'.$file))) {
		include_spip('inc/flock');
		$contenu = var_export($array_langue, true);
		$contenu_final = '<'."?php\n\$GLOBALS['$globale'] = $contenu;\n?".'>';
		$dir = _DIR_PLUGIN_SPIP_PROPRIO;
		$a = ecrire_fichier(($dir[strlen($dir) - 1] == '/' ? substr($dir, 0, -1) : $dir) . '/lang/' . $file . '.php', $contenu_final);

		return $a;
	}
}

function transformer_raccourci($str) {
	include_spip('spip_proprio_fonctions');

	return spip_proprio_formater_nom_fichier($str);
}

// ----------------------
// FILTRE CHAINES
// ----------------------

/*
 * Délimiteurs pour découpage, notamment des contacts
 */
global $spip_proprio_usual_delimiters;
$spip_proprio_usual_delimiters = array(' ', '-', '_', '/', '.','#','\\','@');

/**
 * @param string $str La chaîne à analyser
 *
 * @return boolean/string Le délimiteur trouvé (en plus grand nombre), FALSE sinon
 */
function spip_proprio_usual_delimiters($str) {
	global $spip_proprio_usual_delimiters;
	$delim = false;
	foreach ($spip_proprio_usual_delimiters as $delimiter) {
		if (strpos($str, $delimiter)) {
			$delim = $delimiter;
		}
	}

	return $delim;
}

/**
 * fonction qui transforme les noms de fichiers.
 *
 * @todo decouper le nom du fichier pour enlever l'extension avant traitement, puis la remettre avant retour
 */
function spip_proprio_formater_nom_fichier($string, $spacer = '_') {
	$search = array('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i','@[ç]@i','@[^a-zA-Z0-9]@');
	$replace = array('e','a','i','u','o','c',' ');
	$string = preg_replace($search, $replace, $string);
	$string = strtolower($string);
	$string = str_replace(' ', $spacer, $string);
	$string = preg_replace('#\-+#', $spacer, $string);
	$string = preg_replace('#([-]+)#', $spacer, $string);
	trim($string, $spacer);

	return $string;
}

function spip_proprio_recuperer_extension($str) {
	return substr(strrchr($str, '.'), 1);
}

/**
 * Revue pour autoriser les numéros avec la mention "appel surtaxé"
 * Basiquement, on ne re-formate que les chiffres, et on laisse '(+33)' le cas échéant.
 */
function spip_proprio_formater_telephone($str) {

//echo "entrée dans la fct avec '$str'<br />";

	// on isole ce qu'on considère comme la partie numéro
	$numstr = spip_proprio_isoler_telephone($str, false);
//echo "numéro isolé '$numstr'<br />";

	// on recupère le numéro formaté
	$numstr_formated = spip_proprio_isoler_telephone($str);
//echo "numéro formaté '$numstr_formated'<br />";

	$str = str_replace(trim($numstr), $numstr_formated, $str);
//	$str = preg_replace('/[^0-9]/', '', $str);
//	$str = str_replace(array('(+33)',' ','.'), '', $str);
//	$str = str_replace(array(' ','.'), '', $str);
//	$str = str_replace('(+33)', '(+33) ', $str);


	return $str;
}

/**
 * Ne renvoie que le numéro de tel, sans le +33.
 *
 * @param bool $strip_spaces Doit-on retirer les espaces (non pour la fonction 'spip_proprio_formater_telephone' | oui par defaut)
 */
function spip_proprio_isoler_telephone($str, $strip_spaces = true) {
	$str = str_replace(array('(33)', '(+33)', '+33'), ' ', $str);
	// isoler les chiffres en laissant les espaces internes
	$str = trim($str, ' ');
	$str = preg_replace('/[^0-9 \-\.\/]/', '', $str);
	if ($strip_spaces) {
		$str = str_replace(array(' ', '.'), '', $str);
	}

	return $str;
}

/*
echo "<pre>";

//echo "chaine de travail : ".$entry."<br />";
//echo spip_proprio_isoler_telephone($entry, false)."<br />";
//echo spip_proprio_isoler_telephone($entry)."<br />";

echo spip_proprio_formater_telephone("(+33) 6 01 02 03 04 05 (appel surtaxé)")."<br />";
echo "<br />";
echo spip_proprio_formater_telephone("+33 6 01 02 03 04 05 (appel surtaxé)")."<br />";
echo "<br />";
echo spip_proprio_formater_telephone("(33) 6 01 02 03 04 05 (appel surtaxé)")."<br />";
echo "<br />";
echo spip_proprio_formater_telephone("08 01-02030405")."<br />";
echo "<br />";
echo spip_proprio_formater_telephone("06.69.04.52.34")."<br />";
echo "<br />";
echo spip_proprio_formater_telephone("06 69 04 52 34")."<br />";

exit;
*/
/**
 * Fonction mettant une apostrophe si nécessaire
 * Cette fonction ne traite pas les cas particuliers (nombreux ...) ni les 'h' muet.
 */
function apostrophe($str = '', $article = '', $exception = false) {
	// On retourne direct si non FR
	if ($GLOBALS['spip_ang'] != 'fr') {
		return $article.' '.$str;
	}

	$voyelles = array('a', 'e', 'i', 'o', 'u');
	$article = trim($article);

	$str_deb = substr(spip_proprio_formater_nom_fichier($str), 0, 1);
	$article_fin = substr($article, -1, 1);

	if (in_array($str_deb, $voyelles) or $exception) {
		return substr($article, 0, strlen($article) - 1)."'".$str;
	}

	return $article.' '.$str;
}

function modifier_guillemets($str) {
	return str_replace("'", '"', $str);
}

// ----------------------
// FILTRE GENERATEUR D'IMAGE
// ----------------------

// Avec l'aide inestimable de Paris-Bayrouth (http://www.paris-beyrouth.org/)
function spip_proprio_image_alpha($img, $alpha = '', $src = false) {
	if (!$alpha or !strlen($alpha) or $alpha == '0') {
		return $img;
	}
	include_spip('inc/filtres_images');
	$image = _image_valeurs_trans($img, 'one', 'png');
//var_export($image);
	include_spip('filtres/images_transforme');
	$img = image_alpha($img, $alpha);
	if ($src) {
		return(extraire_attribut($img, 'src'));
	}

	return $img;
}
