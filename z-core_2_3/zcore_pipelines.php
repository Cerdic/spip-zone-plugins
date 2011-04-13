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
	// En 2.3 on peut faire simplement un define('_ZPIP',true);
	define('_ZPIP',true);
	
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

/**
 * Tester la presence sur une page
 * @param <type> $p
 * @return <type>
 */
if (!function_exists('balise_SI_PAGE_dist')){
function balise_SI_PAGE_dist($p) {
	$_page = interprete_argument_balise(1,$p);
	$p->code = "(((\$Pile[0][_SPIP_PAGE]==(\$zp=$_page)) OR (\$Pile[0]['composition']==\$zp AND \$Pile[0]['type']=='page'))?' ':'')";
	$p->interdire_scripts = false;
	return $p;
}
}
?>