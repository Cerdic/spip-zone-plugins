<?php
/**
 * Plugin Ortho-Typographie
 * (c) 2013 cedric
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/config");

/**
 * Ajouter la CSS des guillemets si active
 * @param $flux
 * @return string
 */
function orthotypo_insert_head_css($flux) {
	$config = lire_config("orthotypo/");
	if (!isset($config['guillemets']) OR $config['guillemets'])
		$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('css/typo_guillemets.css').'" media="all" />'."\n";
	return $flux;
}

/**
 * Post-typo : corrections selon les fonctions activees
 * @param $texte
 * @return mixed|string
 */
function orthotypo_post_typo($texte){
	static $config;
	if (is_null($config))
		$config = lire_config("orthotypo/");
	if (!isset($config['guillemets']) OR $config['guillemets'])
		$texte = orthotypo_guillemets_post_typo($texte);
	// mois avant les exposants car on y match des "1er mars"
	if (!isset($config['mois']) OR $config['mois'])
		$texte = orthotypo_mois_post_typo($texte);
	// a optimiser : represente +60% du temps de calcul total des 4 fonctions
	if (!isset($config['exposants']) OR $config['exposants'])
		$texte = orthotypo_exposants_post_typo($texte);
	if (!isset($config['caps']) OR $config['caps'])
		$texte = orthotypo_caps_post_typo($texte);

	return $texte;
}

// Fonctions de traitement sur #TEXTE
function orthotypo_pre_propre($texte) {
	static $config;
	if (is_null($config))
		$config = lire_config("orthotypo/");
	if (isset($config['corrections'])
		AND $config['corrections']
		AND isset($config['corrections_regles'])
		AND $config['corrections_regles']
	){
		$texte = orthotypo_corrections_pre_propre($texte);
	}

	return $texte;
}


/**
 * evite les transformations typo dans les balises $balises
 * par exemple pour <html>, <cadre>, <code>, <frame>, <script>, <acronym> et <cite>, $balises = 'html|code|cadre|frame|script|acronym|cite'
 *
 * @param $texte
 *   $texte a filtrer
 * @param $filtre
 *   le filtre a appliquer pour transformer $texte
 *   si $filtre = false, alors le texte est retourne protege, sans filtre
 * @param $balises
 *   balises concernees par l'echappement
 *   si $balises = '' alors la protection par defaut est sur les balises de _PROTEGE_BLOCS
 *   si $balises = false alors le texte est utilise tel quel
 * @param null|array $args
 *   arguments supplementaires a passer au filtre
 * @return string
 */
function orthotypo_filtre_texte_echappe($texte, $filtre, $balises='', $args=NULL){
	if(!strlen($texte)) return '';

	if ($filtre!==false){
		$fonction = chercher_filtre($filtre,false);
		if (!$fonction) {
			spip_log("orthotypo_filtre_texte_echappe() : $filtre() non definie",_LOG_ERREUR);
			return $texte;
		}
		$filtre = $fonction;
	}

	// protection du texte
	if($balises!==false) {
		if(!strlen($balises)) $balises = _PROTEGE_BLOCS;//'html|code|cadre|frame|script';
		else $balises = ',<('.$balises.')(\s[^>]*)?>(.*)</\1>,UimsS';
		if (!function_exists('echappe_html'))
			include_spip('inc/texte_mini');
		$texte = echappe_html($texte, 'FILTRETEXTECHAPPE', true, $balises);
	}
	// retour du texte simplement protege
	if ($filtre===false) return $texte;
	// transformation par $fonction
	if (!$args)
		$texte = $filtre($texte);
	else {
		array_unshift($args,$texte);
		$texte = call_user_func_array($filtre, $args);
	}

	// deprotection des balises
	return echappe_retour($texte, 'FILTRETEXTECHAPPE');
}

function orthotypo_echappe_balises_html($texte){
	// prudence : on protege le contenu de toute balise html
	if (strpos($texte, '<')!==false){
		// tout
		#$texte = preg_replace_callback(',<[^>]*>,UmsS', 'orthotypo_echappe_balise_html', $texte);
		// dangereux uniquement
		$texte = preg_replace_callback(',<\w+\b[^>]+>,UmsS', 'orthotypo_echappe_balise_html', $texte);
	}
	return $texte;
}

function orthotypo_echappe_balise_html($m) {
	if (strpos($m[0],'class="base64')!==false) return $m[0];
	return code_echappement($m[0], 'FILTRETEXTECHAPPE',true,'span');
}


/* *********************************************************************************************************************
 * Guillemets
 * merge de
 * - http://zone.spip.org/trac/spip-zone/browser/_plugins_/typo_guillemets/typo_guillemets_fonctions.php?rev=54676
 * - http://zone.spip.org/trac/spip-zone/browser/_plugins_/couteau_suisse/outils/guillemets.php?rev=64721
 */

/*
Fichier de formatage typographique des guillemets, par Vincent Ramos
<www-lansargues AD kailaasa POINT net>, sous licence GNU/GPL.

Ne sont touchees que les paires de guillemets.

Le formatage des guillemets est tire de
<http://en.wikipedia.org/wiki/Quotation_mark%2C_non-English_usage>
Certains des usages indiques ne correspondent pas a ceux que la
barre d'insertion de caracteres speciaux de SPIP propose.

Les variables suivies du commentaire LRTEUIN sont confirmees par le
_Lexique des regles typographiques en usage a l'Imprimerie nationale_.

Les variables entierement commentees sont celles pour lesquelles
aucune information n'a ete trouvee. Par defaut, les guillements sont alors
de la forme &ldquo;mot&rdquo;, sauf si la barre d'insertion de SPIP proposait
deja une autre forme.

Version optimisee par Patrice Vanneufville (2007) cf http://www.spip-contrib.net/?article1592
*/

// voir aussi : http://trac.rezo.net/trac/spip/changeset/9429

// definitions des chaines de remplacement
define('_GUILLEMETS_defaut', '&ldquo;$1&rdquo;');
define('_GUILLEMETS_fr', '&#171;&nbsp;$1&nbsp;&#187;'); //LRTEUIN
//define('_GUILLEMETS_ar', '');
define('_GUILLEMETS_bg', '&bdquo;$1&ldquo;');
//define('_GUILLEMETS_br', '');
//define('_GUILLEMETS_bs', '');
define('_GUILLEMETS_ca', '&#171;$1&#187;');
define('_GUILLEMETS_cpf', '&#171;&nbsp;$1&nbsp;&#187;');
//define('_GUILLEMETS_cpf_hat', '');
define('_GUILLEMETS_cs', '&bdquo;$1&ldquo;');
define('_GUILLEMETS_da', '&#187;$1&#171;');
define('_GUILLEMETS_de', '&bdquo;$1&ldquo;'); //ou "&#187;$1&#171;" // LRTEUIN
define('_GUILLEMETS_en', '&ldquo;$1&rdquo;'); //LRTEUIN
define('_GUILLEMETS_eo', '&#171;$1&#187;');
define('_GUILLEMETS_es', '&#171;$1&#187;');
//define('_GUILLEMETS_eu', '');
//define('_GUILLEMETS_fa', '');
//define('_GUILLEMETS_fon', '');
//define('_GUILLEMETS_gl', '');
define('_GUILLEMETS_hu', '&bdquo;$1&rdquo;');
define('_GUILLEMETS_it', '&#171;$1&#187;');
define('_GUILLEMETS_it_fem', '&#171;$1&#187;');
define('_GUILLEMETS_ja', '&#12300;$1&#12301;');
//define('_GUILLEMETS_lb', '');
define('_GUILLEMETS_nl', '&bdquo;$1&rdquo;');
//define('_GUILLEMETS_oc_auv', '');
//define('_GUILLEMETS_oc_gsc', '');
//define('_GUILLEMETS_oc_lms', '');
//define('_GUILLEMETS_oc_lnc', '');
//define('_GUILLEMETS_oc_ni', '');
//define('_GUILLEMETS_oc_ni_la', '');
//define('_GUILLEMETS_oc_prv', '');
//define('_GUILLEMETS_oc_va', '');
define('_GUILLEMETS_pl', '&bdquo;$1&rdquo;');
define('_GUILLEMETS_pt', '&#171;$1&#187;');
define('_GUILLEMETS_pt_br', '&#171;$1&#187;');
define('_GUILLEMETS_ro', '&bdquo;$1&rdquo;');
define('_GUILLEMETS_ru', '&#171;$1&#187;');
define('_GUILLEMETS_tr', '&#171;$1&#187;');
//define('_GUILLEMETS_vi', '');
define('_GUILLEMETS_zh', '&#12300;$1&#12301;'); // ou "&ldquo;$1&rdquo;" en chinois simplifie

function orthotypo_guillemets_echappe_balises_callback($matches) {
	if (strpos($matches[1],'class="base64')===false)
		$matches[1] = code_echappement($matches[1], 'GUILL',true,'span');
	return str_replace('"',"'",$matches[1]);
}

function orthotypo_guillemets_rempl($texte){
	// on s'en va si pas de guillemets...
	if (strpos($texte, '"')===false) return $texte;
	// prudence : on protege TOUTES les balises contenant des doubles guillemets droits
	if (strpos($texte, '<')!==false){
		$texte = preg_replace_callback('/(<[^>]+"[^>]*>)/Ums', 'orthotypo_guillemets_echappe_balises_callback', $texte);
	}

	// si le texte ne contient pas de guill droit
	// ou s'il contient deja des guillemets élaborés
	// on ne touche pas
	if (strpos($texte, '"')!==false
		AND (strpos($texte, '&#171;') === false)
	  AND (strpos($texte, '&#187;') === false)
	  AND (strpos($texte, '&#8220;') === false)
	  AND (strpos($texte, '&#8221;') === false)
	){
		// choix de la langue, de la constante et de la chaine de remplacement
		$lang = isset($GLOBALS['lang_objet'])?$GLOBALS['lang_objet']:$GLOBALS['spip_lang'];
		$constante = '_GUILLEMETS_'.$lang;
		$guilles = defined($constante)?constant($constante):_GUILLEMETS_defaut;

		// Remplacement des autres paires de guillemets (et suppression des espaces apres/avant)
		// Et retour des balises contenant des doubles guillemets droits
		$texte = preg_replace('/"\s*(.*?)\s*"/', $guilles, $texte);
	}
	$texte = echappe_retour($texte, 'GUILL');

	return $texte;
}

function orthotypo_guillemets_post_typo($texte){
	if (strpos($texte, '"')!==false)
		$texte = orthotypo_filtre_texte_echappe($texte,'orthotypo_guillemets_rempl','html|code|cadre|frame|script|acronym|cite');
	return $texte;
}


/* *********************************************************************************************************************
 * Exposants
 * Merge de
 * - http://zone.spip.org/trac/spip-zone/browser/_plugins_/typo_exposants/typo_exposants_fonctions.php?rev=61523
 * - http://zone.spip.org/trac/spip-zone/browser/_plugins_/couteau_suisse/outils/typo_exposants.php?rev=63425
 */

// Filtre typographique exposants pour langue francaise
// serieuse refonte 2006 : Patrice Vanneufville
// Toutes les infos sur : http://www.spip-contrib.net/?article1564

// TODO : raccourci pour les exposants et indices (Pouce^2 ou Pouce^2^, H_2O ou H_2_O ou H,,2,,O
// exemple : http://zone.spip.org/trac/spip-zone/wiki/WikiFormatting

include_spip('inc/charsets');
if (!defined('_TYPO_class')) define('_TYPO_class', '<sup class="typo_exposants">');
if (!defined('_TYPO_sup')) define('_TYPO_sup', _TYPO_class.'\\1</sup>');
if (!defined('_TYPO_sup2')) define('_TYPO_sup2', '\\1'._TYPO_class.'\\2</sup>');

// cette fonction ne fonctionne que pour l'anglais
// elle n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function orthotypo_exposants_en($texte){
	static $typo;
	if(!$typo) {
		$typo = array( array(
			',(?<=1)(st)\b,',
			',(?<=2)(nd)\b,',
			',(?<=3)(rd)\b,',
			',(?<=\d)(th)\b,',
		), array(
			_TYPO_sup, _TYPO_sup, _TYPO_sup, _TYPO_sup,
		));
	}
	return preg_replace($typo[0], $typo[1], $texte);
}

// cette fonction ne fonctionne que pour le francais
// elle n'est pas appelee dans les balises html : html|code|cadre|frame|script|acronym|cite
function orthotypo_exposants_fr($texte){
	static $typo = null;
	static $egrave; static $eaigu1; static $eaigu2; static $accents;
	if (is_null($typo)) {
		// en principe, pas besoin de : caractere_utf_8(232)
		$carre  = unicode2charset('&#178;').'|&#178;|&sup2;';
		$egrave = unicode2charset('&#232;').'|&#232;|&egrave;';
		$eaigu1 = unicode2charset('&#233;').'|&#233;|&eacute;';
		$eaigu2 = unicode2charset('&#201;').'|&#201;|&Eacute;';
		$accents = unicode2charset('&#224;&#225;&#226;&#228;&#229;&#230;&#232;&#233;&#234;&#235;&#236;&#237;&#238;&#239;&#242;&#243;&#244;&#246;&#249;&#250;&#251;&#252;');
		$typo = array(
			// Mlle(s), Mme(s) et erreurs Melle(s)
			// Mme(s) et Mgr
			'/\bM(gr|mes?)\b/u' => 'M'._TYPO_sup,
			'/\bMe?(lles?)\b/u' => 'M'._TYPO_sup,
			// Dr, Pr suivis d'un espace d'un point ou d'un tiret
			'/\b([DP])(r)(?=[\s\.-])/u' => _TYPO_sup2,

			// m2
			"/\bm(?:$carre)\b/" => 'm'._TYPO_class.'2</sup>',
			// m2, m3
			'/\bm([23])\b/u' => 'm'._TYPO_sup,

			// millions, milliards
			'/\b([Mm])([nd]s?)\b/u' => _TYPO_sup2,

			// Vve
			'/\bV(ve)\b/' => 'V'._TYPO_sup,
			// Cie(s)
			'/\bC(ies?)\b/u' => 'C'._TYPO_sup,


			// Societes(s), Etablissements
			"/\bS(t(?:$eaigu1)s?)(?=\W)/u" => 'S'._TYPO_sup,
			"/(?<=\W)(?:E|$eaigu2)ts\b/u" => '&#201;'._TYPO_class.'ts</sup>',

			// 1er(s), Erreurs 1ier(s), 1ier(s)
			'/(?<=\b[1I])i?(ers?)\b/u' => _TYPO_sup,
			"/(?<=\b[1I])i?(?:e|$egrave)(res?)\b/u" => _TYPO_sup,	// Erreurs 1(i)ere(s) + accents
			'/(?<=\b1)(r?es?)\b/u' => _TYPO_sup, // 1e(s), 1re(s)
			'/(?<=\b2)(nde?s?)\b/u' => _TYPO_sup,	// 2nd(e)(s)

			// Erreurs (i)(e)me(s) + accents
			"/(\b[0-9IVX]+)i?(?:e|$egrave)?me(s?)\b/u" => '$1'._TYPO_class.'e$2</sup>',
			// 2e(s), IIIe(s)... (les 1(e?r?s?) ont deja ete remplaces)
			'/\b([0-9IVX]+)(es?)\b/u' => _TYPO_sup2,
			// recto, verso, primo, secondo, etc.
			"/(?<![;$accents])\b(\d+|r|v)o\b/u" => '$1'._TYPO_class.'o</sup>',
			// Maitre (suivi d'un espace et d'une majuscule)
			'/\bM(e)(?= [A-Z])/u' => 'M'._TYPO_sup,
		);
		$typo = array(array_keys($typo),array_values($typo));

	}
	return preg_replace($typo[0], $typo[1], $texte);
}

function orthotypo_exposants_echappe_balises_callback($matches) {
	return code_echappement($matches[1], 'EXPO',true,'span');
}

function orthotypo_exposants_post_typo($texte){
	if (!$lang = $GLOBALS['lang_objet']) $lang = $GLOBALS['spip_lang'];
	if(function_exists($fonction = 'orthotypo_exposants_'.lang_typo($lang))){
		// prudence : on protege les balises <a> et <img>
		if (strpos($texte, '<')!==false)
			$texte = preg_replace_callback('/(<(a|img)\s[^>]+>)/Uims', 'orthotypo_exposants_echappe_balises_callback', $texte);
		$texte = orthotypo_filtre_texte_echappe($texte,$fonction,'html|code|cadre|frame|script|acronym|cite');
		return echappe_retour($texte, 'EXPO');
	}
	return $texte;
}

/* *********************************************************************************************************************
 * Typo des Mois : espace insecable entre le numero du jour et le mois : 12 Mars => 12&nbsp;Mars
 * Tire de
 * - http://zone.spip.org/trac/spip-zone/browser/_plugins_/typo_mois/typo_mois_fonctions.php?rev=43239
 * avec ameliorations :
 * - prise en compte des echappements de balise
 * - prise en compte de la langue
 */

function orthotypo_mois_rempl($texte){
	static $typo = array();
	$lang = $GLOBALS['spip_lang'];
	if(!isset($typo[$lang])) {
		$typo[$lang] = array();
		for ($m=1; $m<=12; $m++)
			$typo[$lang][] = _T('date_mois_'.$m);
		$pre1 = _T('date_jnum1');
		$pre2 = _T('date_jnum2');
		$pre3 = _T('date_jnum3');
		// si on est en _AUTOBR desactive, on accepte un retour ligne entre le chiffre et le mois (mais pas 2=paragraphe)
		// sinon on accepte pas de retours lignes du tout
		$space = ((defined('_AUTOBR')&&!_AUTOBR)?"(?:[ \t]*(?:\r\n|\r|\n))?[ \t]*":"[ \t]+");
		$typo[$lang] = ",([1-3]?[0-9]|$pre1|$pre2|$pre3)$space+(".join('|', $typo[$lang]).')\b,UimsS';
		include_spip('inc/charsets');
		$typo[$lang] = unicode2charset(html2unicode($typo[$lang]));
	}

	return preg_replace($typo[$lang], '\1&nbsp;\2', $texte);
}

function orthotypo_mois_post_typo($texte){
	if (strpbrk($texte,"123456789")!==false){
		$texte = orthotypo_filtre_texte_echappe($texte,'orthotypo_mois_rempl','html|code|cadre|frame|script|acronym|cite');
	}
	return $texte;
}

/* *********************************************************************************************************************
 * Typo des Caps : espace insecable entre le numero du jour et le mois : 12 Mars => 12&nbsp;Mars
 * Tire de
 * - http://zone.spip.org/trac/spip-zone/browser/_plugins_/typo_caps/typo_caps_fonctions.php?rev=43238
 * avec ameliorations :
 * - prise en compte des echappements de balise
 */

/* code tire de typogrify
 * http://jeffcroft.com/sidenotes/2007/may/29/typogrify-easily-produce-web-typography-doesnt-suc/
 */


/**
 * This is necessary to keep dotted cap strings to pick up extra spaces
 * used in preg_replace_callback later on
 */
function orthotypo_caps_replace_callback($matchobj)
{
    if ( !empty($matchobj[2]) )
    {
        return sprintf('<span class="caps">%s</span>', $matchobj[2]);
    }
    else
    {
        $mthree = $matchobj[3];
        if ( ($mthree{strlen($mthree)-1}) == " " )
        {
            $caps = substr($mthree, 0, -1);
            $tail = ' ';
        }
        else
        {
            $caps = $mthree;
            $tail = '';
        }
        return sprintf('<span class="caps">%s</span>%s', $caps, $tail);
    }
}

function orthotypo_caps_rempl($texte){
	static $cap_finder;
	if (is_null($cap_finder)){
		$cap_finder = "/(
	            (\b[A-Z\d]*        # Group 2: Any amount of caps and digits
	            [A-Z]\d*[A-Z]      # A cap string much at least include two caps (but they can have digits between them)
	            [A-Z\d]*\b)        # Any amount of caps and digits
	            | (\b[A-Z]+\.\s?   # OR: Group 3: Some caps, followed by a '.' and an optional space
	            (?:[A-Z]+\.\s?)+)  # Followed by the same thing at least once more
	            (?:\s|\b|$))/xS";
	}
	$texte = orthotypo_echappe_balises_html($texte);
	return preg_replace_callback($cap_finder, 'orthotypo_caps_replace_callback', $texte);
}

function orthotypo_caps_post_typo($texte){
	$texte = orthotypo_filtre_texte_echappe($texte,'orthotypo_caps_rempl','html|code|cadre|frame|script|acronym|cite');
	return $texte;
}


/* *********************************************************************************************************************
 * Outil Correction auto tiree de la lame insertion du CS
 */


// cette fonction appelee automatiquement a chaque affichage de la page privee du Couteau Suisse renvoie un tableau
function orthotypo_corrections_regles() {
	$str = array();
	$preg = array();
	$regles = trim(lire_config("orthotypo/corrections_regles"));
	if (strlen($regles)){
		$liste = preg_split("/[\r\n]+/", $regles);
		foreach ($liste as $l) {
			list($a, $b) = explode("=", $l, 2);
			$a = trim($a); $b = trim($b);
			if (!strlen($a) || preg_match('/^(#|\/\/)/', $a)) {
				// remarques ou vide
			} elseif (preg_match('/^\((.+)\)$/', $a, $reg)) {
				// les mots seuls
				$preg[0][] = '/\b'.$reg[1].'\b/'; $preg[1][] = $b;
			} elseif (preg_match('/^(\/.+\/[imsxuADSUX]*)$/', $a)) {
				// expressions regulieres
				$preg[0][] = $a; $preg[1][] = $b;
			} elseif (strlen($a)) {
				// simples remplacements
				$str[0][] = $a; $str[1][] = $b;
			}
		}
	}

	return array($str, $preg);
}


// cette fonction n'est pas appelee dans les balises html : html|code|cadre|frame|script
function orthotypo_corrections_rempl($texte) {
	static $str,$preg;
	if (is_null($str)){
		list($str,$preg) = orthotypo_corrections_regles();
	}

	$texte = orthotypo_echappe_balises_html($texte);
	if (count($str))
		$texte = str_replace($str[0], $str[1], $texte);
	if (count($preg))
		$texte = preg_replace($preg[0], $preg[1], $texte);

	return $texte;
}

// Fonctions de traitement sur #TEXTE
function orthotypo_corrections_pre_propre($texte) {
	return orthotypo_filtre_texte_echappe($texte,'orthotypo_corrections_rempl');
}

?>