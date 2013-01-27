<?php
/*
 * Plugin A propos des plugins pour SPIP 3
 * Liste les plugins actifs avec affichage icon, nom, version, etat, short description
 * Utilisation intensive des fonctions faisant cela dans le code de SPIP
 * Auteur Jean-Philippe Guihard
 * version 0.3.4 du 04 décembre 2011, 13h40
 * ajout de la possibilite de n'afficher que le nombre de plugin et extension  
 * code emprunte dans le code source de SPIP
 */

/*
to do
vérifier les parties à traduire
*/
include_spip('inc/charsets');
include_spip('inc/texte');
include_spip('inc/plugin'); // pour plugin_est_installe
include_spip('inc/xml');

//Creation de la balise #APROPOS
function balise_APROPOS_dist($p) {
	//recupere un eventuel argument 
	$premier = interprete_argument_balise(1, $p);
	//s'il y en a 1, on traite la chose
	if ($premier != ''){
	$p->code = 'calcul_info_apropos(' . $premier . ')';
	}else{
	//si pas d\'argument, on affiche la liste des plugins
	$p->code = 'calcul_info_apropos("listes")';
	}
	$p->interdire_scripts = false;
	return $p;
}

// fait l tri dans l'argument passé avec la balise : apropos|liste, apropos|nombre, apropos|plugins, apropos|extensions, apropos|default
// liste pour afficher la totale, 
// nombre pour afficher le nombre total plugin et extensions
// plugins pour afficher le nombre de plugins
// extensions pour afficher le nombre d'extensions
// default pour afficher une description complète du plugin

function calcul_info_apropos($params){
//liste_prefix_plugin_actifs est la liste des prefixes des plugins actifs 
$liste_prefix_plugin_actifs = liste_chemin_plugin_actifs();
// $liste_prefix_extensions_actives est la liste des prefixes des extensions actives
$liste_prefix_extensions_actives = liste_plugin_files(_DIR_PLUGINS_DIST);
// liste la totalité des plugins di dosier plugin
$liste_tous_les_plugins = liste_plugin_files(_ROOT_PLUGINS);
//return "<b>".$params."</b>";
switch ($params) { 
	// si parametre liste, alors afficher la liste de tout ce qui est actif avec un résumé pour chaque
	case "liste": 
	/* on s'occupe de la liste les plugins */
	$liste_plugins_actifs = apropos_affiche_les_pluginsActifs($liste_prefix_plugin_actifs,$afficheQuoi="resume");

	/* on s'occupe de la liste des extensions */
	$liste_extensions_actives = apropos_affiche_les_extension(_DIR_PLUGINS_DIST,$afficheQuoi="resume");
	return $liste_plugins_actifs.$liste_extensions_actives;
	break;
	
	// si parametre nombre, alors afficher le nombre de plugins et extensions actifs
	case "nombre":
	$nbre_pluginsActifs = count($liste_prefix_plugin_actifs);
	$nbre_ext = count($liste_prefix_extensions_actives);
	return $nbre_ext+$nbre_pluginsActifs;
	break;
	
	/* si parametre plugins, afficher le nombre de plugin actifs */
	case "plugins":
	$nbre_pluginsActifs = count($liste_prefix_plugin_actifs);
	return $nbre_pluginsActifs;
	break;
	
	/* si paramètre extensions, afficher le nombre d'extensions actives */
	case "extensions":
	$nbre_ext = count($liste_prefix_extensions_actives);
	return $nbre_ext;
	break;
	
	/* si paramètre adisposition, afficher le nombre total de plugins du dossier plugins */
	case "adisposition":
	$nbre_tous = count($liste_tous_les_plugins);
	return $nbre_tous;
	break;
	
	/* additionne tout ce qui est disponible e, plugins et extensions */ 
	case "disponible":
	$nbre_ext = count($liste_prefix_extensions_actives);
	$nbre_tous = count($liste_tous_les_plugins);
	return $nbre_tous+$nbre_ext;
	break;

	/* si paramètre defaut, on récupère le prefixe du plugin pour afficher la description complète de celui-ci
	default:
	//$leResume = count($liste_tous_les_plugins);
	$leResume = apropos_afficher_info_du_plugins($url_page, $params, $class_li="item",$dir_plugins=_DIR_PLUGINS,$afficheQuoi="latotale",$params);
	return "<br />".$leResume."<br />";
	break;  */
	}

}


?>