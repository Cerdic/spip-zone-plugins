<?php
/** 
 * Crayons plugin for spip (c) Fil, toggg 2006-2013 -- licence GPL
 * 
 * @package SPIP\Crayons\Fonctions
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Débuguer les crayons
 * mettre a true dans mes_options pour avoir les crayons non compresses
 */
if(!defined('_DEBUG_CRAYONS'))
	define('_DEBUG_CRAYONS', false);

// Dire rapidement si ca vaut le coup de chercher des droits
function analyse_droits_rapide_dist() {
	return isset($GLOBALS['auteur_session']['statut']);
}

// Le pipeline header_prive (pour y tester les crayons)
function Crayons_insert_head($head) {
	// verifie la presence d'une meta crayons, si c'est vide
	// on ne cherche meme pas a traiter l'espace prive
	$config_espace_prive = @unserialize($GLOBALS['meta']['crayons']);
	if (empty($config_espace_prive)) {
		return $head;
	}

	// verifie que l'edition de l'espace prive est autorisee
	if ($config_espace_prive['espaceprive'] == 'on') {
		// determine les pages (exec) crayonnables
		if (($config_espace_prive['exec_autorise'] == '*') ||
	       in_array(_request('exec'),explode(',',$config_espace_prive['exec_autorise']))) {
			// Calcul des droits
			include_spip('inc/crayons');
			$head = Crayons_preparer_page($head, '*', wdgcfg(), 'head');
		}
	}

	// retourne l'entete modifiee
	return $head;
}

// Le pipeline affichage_final, execute a chaque hit sur toute la page
function &Crayons_affichage_final(&$page) {

	// ne pas se fatiguer si le visiteur n'a aucun droit
	if (!(function_exists('analyse_droits_rapide')?analyse_droits_rapide():analyse_droits_rapide_dist()))
		return $page;

	// sinon regarder rapidement si la page a des classes crayon
	if (strpos($page, 'crayon')===FALSE)
		return $page;

	// voir un peu plus precisement lesquelles
	include_spip('inc/crayons');
	if (!preg_match_all(_PREG_CRAYON, $page, $regs, PREG_SET_ORDER))
		return $page;
	$wdgcfg = wdgcfg();

	// calculer les droits sur ces crayons
	include_spip('inc/autoriser');
	$droits = array();
	$droits_accordes = 0;
	foreach ($regs as $reg) {
		list(,$crayon,$type,$champ,$id) = $reg;
		if (_DEBUG_CRAYONS) spip_log("autoriser('modifier', $type, $id, NULL, array('champ'=>$champ))","crayons_distant");
		if (autoriser('modifier', $type, $id, NULL, array('champ'=>$champ))) {
			if(!isset($droits['.' . $crayon]))
				$droits['.' . $crayon] = 0;
			$droits['.' . $crayon]++;
			$droits_accordes ++;
		}
	}
	// et les signaler dans la page
	if ($droits_accordes == count($regs)) // tous les droits
		$page = Crayons_preparer_page($page, '*', $wdgcfg);
	else if ($droits) // seulement certains droits, preciser lesquels
		$page = Crayons_preparer_page($page, join(',',array_keys($droits)), $wdgcfg);

	return $page;
}

function &Crayons_preparer_page(&$page, $droits, $wdgcfg = array(), $mode='page') {
	/**
	 * Si pas forcer_lang, on charge le contrôleur dans la langue que l'utilisateur a dans le privé
	 */
	if(!isset($GLOBALS['forcer_lang']) OR !$GLOBALS['forcer_lang'] OR ($GLOBALS['forcer_lang'] === 'non'))
		lang_select($GLOBALS['auteur_session']['lang']);
	
	$jsFile = generer_url_public('crayons.js');
	if (_DEBUG_CRAYONS)
		$jsFile = parametre_url($jsFile,'debug_crayons',1,'&');
	include_spip('inc/filtres'); // rien que pour direction_css() :(
	$cssFile = direction_css(find_in_path('crayons.css'));

	$config = crayons_var2js(array(
		'imgPath' => dirname(find_in_path('images/crayon.png')),
		'droits' => $droits,
		'dir_racine' => _DIR_RACINE,
		'self' => self('&'),
		'txt' => array(
			'error' => _U('crayons:svp_copier_coller'),
			'sauvegarder' => $wdgcfg['msgAbandon'] ? _U('crayons:sauvegarder') : ''
		),
		'img' => array(
			'searching' => array(
				'txt' => _U('crayons:veuillez_patienter')
			),
			'crayon' => array(
				'txt' => _U('crayons:editer')
			),
			'edit' => array(
				'txt' => _U('crayons:editer_tout')
			),
			'img-changed' => array(
				'txt' => _U('crayons:deja_modifie')
			)
		),
		'cfg' => $wdgcfg
	));


	// Est-ce que PortePlume est la ?
	$meta_crayon = isset($GLOBALS['meta']['crayons']) ? unserialize($GLOBALS['meta']['crayons']): array();
	$pp = '';
	if (isset($meta_crayon['barretypo']) && $meta_crayon['barretypo']) {
		if (function_exists('chercher_filtre')
		AND $f = chercher_filtre('info_plugin')
		AND $f('PORTE_PLUME','est_actif')) {

		$pp = <<<EOF
cQuery(function() {
	if (typeof onAjaxLoad == 'function') {
		function barrebouilles_crayons() {
			$('.formulaire_crayon textarea.crayon-active')
			.barre_outils('edition');
		}
		onAjaxLoad(barrebouilles_crayons);
	}
});
EOF;

		}
	}


	$incCSS = "<link rel=\"stylesheet\" href=\"{$cssFile}\" type=\"text/css\" media=\"all\" />";
	$incJS = <<<EOH
<script type="text/javascript">/* <![CDATA[ */
var configCrayons;
function startCrayons() {
	configCrayons = new cQuery.prototype.cfgCrayons({$config});
	cQuery.fn.crayonsstart();
{$pp}
}
var cr = document.createElement('script');
cr.type = 'text/javascript'; cr.async = true;
cr.src = '{$jsFile}\x26callback=startCrayons';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(cr, s);
/* ]]> */</script>

EOH;

	if ($mode == 'head')
		return $page . $incJS . $incCSS; //js inline avant les css, sinon ca bloque le chargement

	$pos_head = strpos($page, '</head>');
	if ($pos_head === false)
		return $page;

	// js inline avant la premiere css, ou sinon avant la fin du head
	$pos_link = strpos($page, '<link ');
	if (!$pos_link)
		$pos_link = $pos_head;
	$page = substr_replace($page, $incJS, $pos_link, 0);

	// css avant la fin du head
	$pos_head = strpos($page, '</head>');
		$page = substr_replace($page, $incCSS, $pos_head, 0);
	
	return $page;
}


/**
 * Balise indiquant un champ SQL crayonnable
 *
 * @note
 *   Si cette fonction est absente, balise_EDIT_dist() déclarée par SPIP
 *   ne retourne rien
 * 
 * @example
 *   <div class="#EDIT{texte}">#TEXTE</div>
 *   <div class="#EDIT{ps}">#PS</div>
 *
 * @param Champ $p
 *   Pile au niveau de la balise
 * @return Champ
 *   Pile complétée par le code à générer
**/
function balise_EDIT($p) {

	// le code compile de ce qui se trouve entre les {} de la balise
	$label = interprete_argument_balise(1,$p);

	// Verification si l'on est dans le cas d'une meta
	// #EDIT{meta-descriptif_site} ou #EDIT{meta-demo/truc}
	if (preg_match('/meta-(.*)\'/',$label,$meta)) {
		$type = 'meta';
		$label= 'valeur';
		$primary = $meta[1];
		$p->code = "classe_boucle_crayon('"
			. $type
			."',"
			.sinon($label,"''")
			.","
			. "str_replace('/', '__', '$primary')" # chaque / doit être remplacé pour CSS.
			.").' '";
		$p->interdire_scripts = false;
		return $p;
	}

	$i_boucle = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	// #EDIT hors boucle? ne rien faire
	if (!$type = ($p->boucles[$i_boucle]->type_requete)) {
		$p->code = "''";
		$p->interdire_scripts = false;
		return $p;
	}

	// crayon sur une base distante 'nua:article-intro-5'
	if ($distant = $p->boucles[$i_boucle]->sql_serveur)
		$type = $distant.'__'.$type;

	// le compilateur 1.9.2 ne calcule pas primary pour les tables secondaires
	// il peut aussi arriver une table sans primary (par ex: une vue)
	if(!($primary = $p->boucles[$i_boucle]->primary)){
		include_spip('inc/vieilles_defs'); # 1.9.2 pour trouver_def_table
		list($nom, $desc) = trouver_def_table(
			$p->boucles[$i_boucle]->type_requete, $p->boucles[$i_boucle]);
		$primary = $desc['key']['PRIMARY KEY'];
	}

	$primary = explode(',',$primary);
	$id = array();
	foreach($primary as $key) {
		$id[] = champ_sql(trim($key),$p);
	}
	$primary = implode(".'-'.",$id);

	$p->code = "classe_boucle_crayon('"
		. $type
		."',"
		.sinon($label,"''")
		.","
		. $primary
		.").' '";
	$p->interdire_scripts = false;
	return $p;
}


/**
 * Balise indiquant une configuration crayonnable
 *
 * @example
 *   <div class="#EDIT_CONFIG{descriptif_site}">#DESCRIPTIF_SITE_SPIP</div>
 *   <div class="#EDIT_CONFIG{demo/truc}">#CONFIG{demo/truc}</div>
 *
 * @param Champ $p
 *   Pile au niveau de la balise
 * @return Champ
 *   Pile complétée par le code à générer
**/
function balise_EDIT_CONFIG_dist($p) {

	// le code compile de ce qui se trouve entre les {} de la balise
	$config = interprete_argument_balise(1,$p);
	if (!$config) return $p;

	// chaque / du nom de config doit être transformé pour css.
	// nous utiliserons '__' à la place.

	$type = 'meta';
	$label= 'valeur';

	$p->code = "classe_boucle_crayon('"
		. $type
		. "','"
		. $label
		. "',"
		. "str_replace('/', '__', $config)" 
		. ").' '";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Crée le controleur du crayon indiqué par la classe CSS
 *
 * @param string $class
 *   Class CSS de crayon tel que créé par #EDIT
 * @return string
 *   HTML du crayon, sinon texte d'erreur
**/
function creer_le_crayon($class) {
	include_spip('inc/crayons');
	include_spip('action/crayons_html');
	$a = affiche_controleur($class, array('w' => 485, 'h' => 300, 'wh' => 500));
	return $a['$erreur'] ? $a['$erreur'] : $a['$html'];
}

/**
 * Balise #CRAYON affichant un formulaire de crayon
 * SI ?edit=1;
 *
 * @example
 *   #CRAYON{ps}
 *
 * @param Champ $p
 *   Pile au niveau de la balise
 * @return Champ
 *   Pile complétée par le code à générer
**/
function balise_CRAYON($p) {
	$p = balise_EDIT($p);
	$p->code = 'creer_le_crayon('.$p->code.')';
	return $p;
}


/**
 * Donne la classe CSS crayon en fonction
 * - du type de la boucle
 *   (attention aux exceptions pour #EDIT dans les boucles HIERARCHIE et SITES)
 * - du champ demande (vide, + ou se terminant par + : (+)classe type--id)
 * - de l'id courant
 * 
 * @param string $type
 *   Type d'objet, ou "meta" pour un champ de configuration
 * @param string $champ
 *   Champ SQL concerné
 * @param int|string $id
 *   Identifiant de la ligne sql
 * @return string
 *   Classes CSS (à ajouter dans le HTML à destination du javascript de Crayons)
**/
function classe_boucle_crayon($type, $champ, $id) {
	// $type = objet_type($type);
	$type = $type[strlen($type) - 1] == 's' ?
		substr($type, 0, -1) :
		str_replace(
			array('hierarchie', 'syndication'),
			array('rubrique',   'site'),
		$type);

	$plus = (substr($champ, -1) == '+' AND $champ = substr($champ, 0, -1))
		? " $type--$id"
		: '';
	
	// test rapide pour verifier que l'id est valide (a-zA-Z0-9)
	if (false !== strpos($id, ' ')) {
		spip_log("L'identifiant ($id) ne pourra être géré ($type | $champ)", 'crayons');
		return 'crayon_id_ingerable';
	}
	
	return 'crayon ' . $type . '-' . $champ . '-' . $id . $plus;
}

?>
