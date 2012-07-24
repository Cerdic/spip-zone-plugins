<?php
/**
 * Plugin À propos statistiques
 * (c) 2012 Jean-Philippe Guihard
 * Tout ceci vient du plugin SVP Statistiques d'Eric Lupinacci
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
	
	$nombreDeSites = sql_fetsel("nbr_sites", "spip_plugins", "prefixe=".sql_quote($params));
	
	// je vérifie que je recupere bien un array car il peut y avoir un retour vide si le plugin n'est pas présent dans la base de la zone
	
	if ( is_array( $nombreDeSites ) ) {
	$result = implode('',$nombreDeSites) ;
	return $result ;
	}
}
?>