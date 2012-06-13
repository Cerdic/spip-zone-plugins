<?php
/**
 * Plugin À propos statistiques
 * (c) 2012 Jean-Philippe Guihard
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

//Creation de la balise #APROPOS
function balise_APROPOS_STATS_dist($p) {
	//recupere un eventuel argument 
	$premier = interprete_argument_balise(1, $p);
	//s'il y en a 1, on traite la chose
	if ($premier != ''){
	$p->code = 'calcul_info_apropos_stats(' . $premier . ')';
	}else{
	//si pas d\'argument, on affiche la liste des plugins
	$p->code = 'calcul_info_apropos_stats("listes")';
	}
	$p->interdire_scripts = false;
	return $p;
}


function calcul_info_apropos_stats($params){

	//je vais lire la table spip_plugins, la colone nbr_sites de la ligne prefixe=ce que j'ai passé en paramètre
	
	$nombreDeSites = implode(sql_fetsel("nbr_sites", "spip_plugins", "prefixe=".sql_quote($params)));
	return $nombreDeSites ;

}
?>