<?php
/**
 * Fonction de vérification du plugin URLs Pages Personnalisées
 *
 * @plugin     URLs Pages Personnalisées
 * @copyright  2016
 * @author     tcharlss
 * @licence    GNU/GPL
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déterminer si le fond d'une page est valide (fond = squelette)
 *
 * Squelettes non valides :
 * - squelettes techniques : sommaire.html, backend.html, inc-xxx.html, sitemap.xml.html, etc.
 * - squelettes des erreurs HTTP : 404.html, 403.html etc.
 * - squelettes des objets éditoriaux : article.html, article-N.lg.html, article_composition.html, etc.
 * - squelettes dans le privé
 * - (optionnel) : squelette d'une page déjà en base
 * - (optionnel) : squelette inexistant
 *
 * @param string $chemin
 *     chemin/vers/page.html
 * @param array $options
 *     (bool)         doublon : pour exclure les pages en BDD
 *     (bool)         fichier : pour vérifier que le squelette existe
 *     (string|array) type    : pour spécifier les vérifications à faire
 *                    objet | code_http | technique | pseudo_fichier | prive | doublon | fichier
 * @return string
 *    Message d'erreur éventuel
 */
function verifier_fond_page_dist($chemin, $options = array()) {

	// Variables initiales
	$erreur = ''; // valeur de retour par défaut
	$page   = pathinfo($chemin, PATHINFO_FILENAME); // squelette
	$fond   = pathinfo($chemin, PATHINFO_BASENAME); // squelette.html
	$z      = defined('_DIR_PLUGIN_ZCORE') ? 'zcore' : defined('_DIR_PLUGIN_Z') ? 'z' : false;
	// Avec Zspip, il faut retirer le prefixe « page- »
	if ($z == 'z'
		and substr($page, 0, strlen('page-')) == 'page-'
	) {
		$type_page = substr($page, strlen('page-'));
		$type_fond = substr($fond, strlen('page-'));
	} else {
		$type_page = $page;
		$type_fond = $fond;
	}
	// Définissons les vérifications à effectuer
	$verifier = array(
		'objet',
		'code_http',
		'technique',
		'pseudo_fichier',
		'prive',
		(isset($options['doublon']) and $options['doublon']) ? 'doublon' : '',
		(isset($options['fichier']) and $options['fichier']) ? 'fichier' : '',
	);
	$verifier = array_filter($verifier);
	if (isset($options['type'])
		and is_string($options['type'])
		and in_array($options['type'], $verifier)
	){
		$verifier = array($options['type']);
	} elseif (isset($options['type'])
		and is_array($options['type'])
		and array_intersect($verifier, $options['type'])
	){
		$verifier = $options['type'];
	}


	// =====================================================
	// 1) Préparons les listes et les masques pour les tests
	// =====================================================


	// 1.1) Masque : pas un objet éditorial
	// Il faut prendre en compte les déclinaisons (http://spip.net/3445)
	// - objet.html : la base
	// - objet.lg.html : pour une langue donnée (+ combinaisons : objet=N.lg.html, etc.)
	// - objet=N.html : pour une rubrique N
	// - objet-N.html : pour une branche N
	// - objet_N.html : pour un numéro N précis (plugin variantes articles)
	// - objet-composition.html : pour une composition précise (plugin compositions)
	$objets = lister_objets_types();
	$objets_split = join('|', $objets);
	$exclure_regex_objets = "/^($objets_split)([\-=_][a-zA-Z0-9-]+)?(\.[a-z]{2})?\.html$/";

	// 1.2) Liste : squelettes techniques, d'après leurs noms exacts
	$exclure_predefinis = array(
		'sommaire',
		'login',
		'backend',
		'distrib',
		'ical',
		'structure', // zcore
		'body',      // zcore
		'z_apl',     // zcore
		'page',      // zpip
		'objet',     // zpip
	);

	// 1.3) Masque : squelettes technique commençant par certaines chaînes (^squelette)
	$exclure_misc = array(
		'inc-',
		'backend-',
		'rss_forum_',
		'sitemap-'
	);
	$exclure_misc_split = join('|', $exclure_misc);
	$exclure_regex_misc = "/^($exclure_misc_split)/";

	// 1.4) Masque : code HTTP 404.html et cie
	$exclure_regex_http = '/^[0-9]{3}\.html$/';

	// 1.5) Masque : squelette d'un pseudo-fichier : squelette.txt.html, squelette.xml.html etc.
	$exclure_regex_fichier = '/\.[a-z]{3}\.html$/';


	// =======================
	// 2) Effectuons les tests
	// =======================


	// Squelette dans le prive
	if (in_array('prive', $verifier)
		and substr($chemin, 0, strlen('prive/')) == 'prive/'
	) {
		$erreur = _T('urls_pages:erreur_fond_prive');
	}
	// Squelette d'un objet éditorial
	elseif (in_array('objet', $verifier)
		and preg_match($exclure_regex_objets, $type_fond)
	){
		$erreur = _T('urls_pages:erreur_fond_objet_editorial');
	}
	// Squelette technique
	elseif (in_array('technique', $verifier)
		and (
			in_array($type_page, $exclure_predefinis)
			or preg_match($exclure_regex_misc, $type_fond)
		)
	){
		$erreur = _T('urls_pages:erreur_fond_technique');
	}
	// Squelette d'un code HTTP 404 et cie
	elseif (in_array('code_http', $verifier)
		and preg_match($exclure_regex_http, $type_fond)
	){
		$erreur = _T('urls_pages:erreur_fond_code_http');
	}
	// Squelette d'un pseudo fichiers .xml et cie
	elseif (in_array('pseudo_fichier', $verifier)
		and preg_match($exclure_regex_fichier, $type_fond)
	){
		$erreur = _T('urls_pages:erreur_fond_pseudo_fichier');
	}
	// Squelette d'une page déjà en BDD
	elseif (in_array('doublon', $verifier)
		and $url_bdd = sql_getfetsel('url', 'spip_urls', array('page = ' . sql_quote($page)))
	) {
		$erreur = _T('urls_pages:erreur_fond_doublon_url', array('url' => $url_bdd));
	}
	// Squelette inexistant
	elseif (in_array('fichier', $verifier)
		and trouver_fond_page($type_page) == false
	) {
		$erreur = _T('urls_pages:erreur_fond_absent');
	}

	return $erreur;
}
