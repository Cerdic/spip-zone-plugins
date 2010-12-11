<?php
/*
 * Plugin Z-core
 * (c) 2008-2010 Cedric MORIN Yterium.net
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// definition des balises et filtres boites
include_spip('inc/filtres_boites');

// verifier une seule fois que l'on peut utiliser APL si demande
if (defined('_Z_AJAX_PARALLEL_LOAD')) {
	// les pages APL contiennent un <noscript>
	// avec une meta refresh sur self()+var_zapl=non
	// ainsi, les clients sans javascript rechargent la page,
	// se voient poser un cookie, et voient ensuite le site sans APL
	if (_request('var_zapl')=='non') {
		include_spip('inc/cookie');
		spip_setcookie('no_zapl',$_COOKIE['no_zapl']='no_zapl');
	}
	if (!isset($_COOKIE['no_zapl'])
	 AND !_IS_BOT
	 AND !_request('var_zajax')
	 AND _request('var_mode')!=="debug"
	 AND $_SERVER['REQUEST_METHOD'] == 'GET'
	 ) {
		define('_Z_AJAX_PARALLEL_LOAD_OK',true);
		$GLOBALS['marqueur'] .= ":Zapl";
	}
}

/**
 * Inutilise mais permet le chargement de ce fichier avant le decodage des urls
 * et l'utilisation de _DEFINIR_CONTEXTE_TYPE
 * @param <type> $flux
 * @return <type>
 */
function zcore_declarer_url_objets($flux){
	return $flux;
}

/**
 * Fonction Page automatique a partir de contenu/page-xx
 *
 * @param array $flux
 * @return array
 */
function zcore_styliser($flux){
	// dans les futures versions de SPIP on pourra faire simplement un define('_ZPIP',true);
	if (!test_espace_prive()) {
		$styliser_par_z = charger_fonction('styliser_par_z','public');
		$flux = $styliser_par_z($flux);
	}
	
	return $flux;
}




/**
 * Surcharger les intertires avant que le core ne les utilise
 * pour y mettre la class h3
 * une seule fois suffit !
 *
 * @param string $flux
 * @return string
 */
function zcore_pre_propre($flux){
	static $init = false;
	if (!$init){
		$intertitre = $GLOBALS['debut_intertitre'];
		$class = extraire_attribut($GLOBALS['debut_intertitre'],'class');
		$class = ($class ? " $class":"");
		$GLOBALS['debut_intertitre'] = inserer_attribut($GLOBALS['debut_intertitre'], 'class', "h3$class");
		foreach($GLOBALS['spip_raccourcis_typo'] as $k=>$v){
			$GLOBALS['spip_raccourcis_typo'][$k] = str_replace($intertitre,$GLOBALS['debut_intertitre'],$GLOBALS['spip_raccourcis_typo'][$k]);
		}
		$init = true;
	}
	return $flux;
}

/**
 * Ajouter le inc-insert-head du theme si il existe
 *
 * @param string $flux
 * @return string
 */
function zcore_insert_head($flux){
	if (find_in_path('inc-insert-head.html')){
		$flux .= recuperer_fond('inc-insert-head',array());
	}
	return $flux;
}

//
// fonction standard de calcul de la balise #INTRODUCTION
// mais retourne toujours dans un <p> comme propre
//
// http://doc.spip.org/@filtre_introduction_dist
function filtre_introduction($descriptif, $texte, $longueur, $connect) {
	include_spip('public/composer');
	$texte = filtre_introduction_dist($descriptif, $texte, $longueur, $connect);

	if ($GLOBALS['toujours_paragrapher'] AND strpos($texte,"</p>")===FALSE)
		// Fermer les paragraphes ; mais ne pas en creer si un seul
		$texte = paragrapher($texte, $GLOBALS['toujours_paragrapher']);

	return $texte;
}

/**
 * Tester la presence sur une page
 * @param <type> $p
 * @return <type>
 */
function balise_SI_PAGE_dist($p) {
	$_page = interprete_argument_balise(1,$p);
	$p->code = "(((\$Pile[0][_SPIP_PAGE]==(\$zp=$_page)) OR (\$Pile[0]['composition']==\$zp AND \$Pile[0]['type']=='page'))?' ':'')";
	$p->interdire_scripts = false;
	return $p;
}
?>