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
	
	/* si paramètre adisposition, afficher le nombre de plugins du dossier plugins */
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

	/* si paramètre defaut, on récupère le prefixe du plugin pour afficher la description complète de celui-ci */
	default:
	//$leResume = count($liste_tous_les_plugins);
	$leResume = apropos_afficher_info_du_plugins($url_page, $params, $class_li="item",$dir_plugins=_DIR_PLUGINS,$afficheQuoi="latotale",$params);
	return "<br />".$leResume."<br />";
	break; 
	}

}

function apropos_affiche_les_extension($liste_extensions_actives,$afficheQuoi){
	$lesExtensions = "";
	if ($liste_extensions = liste_plugin_files(_DIR_PLUGINS_DIST)) {
		$format = 'liste'; 
		$lesExtensions .= "<div class='apropos-liste'>";
		$lesExtensions .= "<h3>".count($liste_extensions)." "._T('apropos:extensions_automatiquement').".</h3>";
		$lesExtensions .= apropos_afficher_list(self(), $liste_extensions,$liste_extensions, _DIR_PLUGINS_DIST,$afficheQuoi);// surcharge de fonction
		$lesExtensions .= "</div>\n";
	}
	return $lesExtensions;
}

/* les fonctions utilisees pour les plugins */
// entete liste des plugins pour affichage du nombre du plugin actives
function apropos_affiche_les_pluginsActifs($liste_prefix_plugin_actifs, $afficheQuoi){
		$lesPlugins .= "<div class='apropos-liste'><h3>".sinon(
						singulier_ou_pluriel(count($liste_prefix_plugin_actifs), 'plugins_actif_un', 'plugins_actifs', 'count'),
						_T('plugins_actif_aucun')
						)."</h3>";
		$lesPlugins .= apropos_afficher_list(self(), $liste_prefix_plugin_actifs,$liste_plugins_actifs, _DIR_PLUGINS, $format='liste',$afficheQuoi,$params);
	return $lesPlugins."</div>\n";
}


// Extrait de  http://doc.spip.org/@affiche_liste_plugins
/* Creation de la liste des plugins actifs, trie de la liste par ordre alphabetique*/
function apropos_afficher_list($url_page,$liste_plugins, $liste_plugins_actifs, $dir_plugins,$afficheQuoi){
	$get_infos = charger_fonction('get_infos','plugins');
	
	// je recupere la liste des plugin par leur nom
	$liste_plugins = array_flip($liste_plugins);
	foreach(array_keys($liste_plugins) as $chemin) {
		$info = $get_infos($chemin, false, $dir_plugins);
		$liste_plugins[$chemin] = strtoupper(trim(typo(translitteration(unicode2charset(html2unicode($info['nom']))))));
	}	

	// je trie par ordre alphabetique la liste
	
	asort($liste_plugins);

	$block_UL = '';
	// pour chaque plugin, je vais aller chercher les informations complementaires a afficher
	// en l'occurrence, nom, version, etat, slogan ou description et logo.
	// je construis une liste classique avec des UL et des LI
	foreach($liste_plugins as $plug => $nom){
		$block_UL .= apropos_afficher_info_du_plugins($url_page, $plug, "item", $dir_plugins,$afficheQuoi,$params)."\n";
	}

	return "<ul>".$block_UL."</ul>";
}

// Extrait de  http://doc.spip.org/@ligne_plug
/* Extrait les infos de chaque plugin */
function apropos_afficher_info_du_plugins($url_page, $plug_file, $class_li="item", $dir_plugins=_DIR_PLUGINS,$afficheQuoi,$params) {

	$force_reload = (_request('var_mode')=='recalcul');
	$get_infos = charger_fonction('get_infos','plugins');
	$info = $get_infos($plug_file, $force_reload, $dir_plugins);
	$leBloc = charger_fonction('afficher_plugin', 'plugins');

// Affichage des différentes informations à récupérer
// suivants que nous voulions un liste totale des plugins
// ou juste le description complete d'un seul plugin

		$lefichier = '';
		//recherche la presence d'un fichier paquet.xml
		if (is_readable($file = "$dir_plugins$plug_file/" . ($desc = "paquet") . ".xml")) {
			$lefichier = 'paquet';
			}else{
			if (is_readable($file = "$dir_plugins$plug_file/" . ($desc = "plugin") . ".xml"))
			$lefichier = 'plugin';
		}
		
		$prefix = $info['prefix'];
		$dir = "$dir_plugins$plug_file";
		// si afficheQuoi = latotale, je vais afficher toutes les infos du plugin, 
		// y comprit une description complète et non uniquement un résumé.
		// Autrement, affiche le résumé
		// fonction demandée pour pouvoir afficher une page par plugin, page qui affiche
		// la description complète de ce plugin.

		if ($afficheQuoi == "latotale"){
			//je teste pour vérifier que $prefix n'est pas vide. Si vide, c'est que le préfixe entré est invalide ou que le plugin est dans le dossier extension
			// de toute façon, a revoir car pas gégène
			if ($prefix ==''){
				// je check si par hasard ce ne serait pas un plugin place dans le dossier extension
				$info = $get_infos($plug_file, $force_reload, _DIR_PLUGINS_DIST);
				$dir = _DIR_PLUGINS_DIST.$plug_file;
				$prefix = $info['prefix'];
				if ($prefix !=''){
				//return "<span class='apropos-erreur'>Le paramètre entré n'est pas valide. Consultez la documentation du plugin.</span>";
				}else{
				return "<span class='apropos-erreur'>"
				."Erreur dans la saisie du préfixe du plugin.</span><br />Vous avez entré <b>".$params."</b> comme préfixe. Vérifiez ce dernier qui se trouve dans le fichier paquet.xml ou plugin.xml du plugin.";
			}}

				//je récupère la description complète, la version avec svn, le crédit et la licence
				$slogan = PtoBR(plugin_propre($info['description'], "$dir/lang/paquet-$prefix"));

				// prise en compte des plugin sans tag description par ex crayons dans sa version 1.13.0
				if ((strpos($slogan, "_description")) !==FALSE) { // $dir."/lang/paquet-".$prefix.":".$prefix."_description"){ //$dir."/lang/paquet-".$prefix.":".$prefix."_description"){
					$slogan = PtoBR(plugin_propre($info['slogan'], "$dir/lang/paquet-$prefix"));
				}
				//$slogan .= "<br />".$description;
				$documentation = $info['documentation'];
				if ($documentation != ''){
					$documentation = "<div class='apropos-description'>"._T('apropos:la_documentation')." <a href=\"".$info['documentation']."\">".$info['documentation']."</a></div>";
				}
				$demonstration = $info['demonstration'];
				if ($demonstration != ''){
					$demonstration = "<div class='apropos-description'>"._T('apropos:la_demonstration')." <a href=\"".$info['demonstration']."\">".$info['demonstration']."</a></div>";
				}
				$credit = $info['credit'];
				if ($credit != ''){
					$credit = "<div class='apropos-auteur'>"._T('plugin_info_credit')." : ".implode($info['credit'])."</div>";
				}

				//teste si le numero de version SVn n'est pas vide, dans ce cas, on n'affiche rien			
				if ($svn_revision !==''){
					$svn_revision = version_svn_courante($dir_plugins.$plug_file);
					$leSVN = "SVN ".($svn_revision<0 ? ' SVN':'').' ['.abs($svn_revision).']'; // version_svn_courante($dir_plugins.$plug_file);	  
				}
				$infoSVN = "<div class='apropos-svn'>".$leSVN.", "._T('repertoire_plugins')." ".$dir."</div>"; 

		}else{

		// si pas la totale des infos, le minimum pour l'affichage en liste
			$slogan = PtoBR(plugin_propre($info['slogan'], "$dir/lang/paquet-$prefix"));
			// test si slogan vide afin de prendre la description via le fichier plugin.xml le cas echeant
			if ($slogan!==''){
				// une seule ligne dans le slogan : couper si besoin
				if (($p=strpos($slogan, "<br />"))!==FALSE)
					$slogan = substr($slogan, 0,$p);
				// couper par securite
				$slogan = couper($slogan, 180).".";
				}else{
				$get_desc = charger_fonction('afficher_plugin','plugins');
				$slogan = couper(plugin_propre($info['description']), 180);
			}
		}
		$leNom = PtoBR(plugin_propre($info['nom']));
		$url = parametre_url($url_page, "plugin", substr($dir,strlen(_DIR_RACINE)));
	
		// affiche l'icone du plugin ou une icone générique si absente
		if (isset($info['logo']) and $i = trim($info['logo'])) {
			include_spip("inc/filtres_images_mini");
			$i = inserer_attribut(image_reduire("$dir/$i", 32),'alt','Icone du plugin '.$leNom);
			$i = "<span class='apropos-icon'>".$i."</span>";
		} else {
			$generic = _DIR_PLUGIN_APROPOS."img/generique.png"; //mettre une icone generique si pas d'icone de defini
			include_spip("inc/filtres_images_mini");
			$i = inserer_attribut(image_reduire("$generic", 32),'alt','Icone g&eacute;n&eacute;rique pour le plugin '.$leNom);
			$i = "<div class='apropos-icon'>$i</div>";
		}
		
		// grosse différence avec Spip 2 qui retournait une liste et non 1 array

	if (isset($info['auteur']))
	{
		if (is_array($info['auteur'])){
			$a = formater_credits($info['auteur'], ', ');
			$auteur =  _T('public:par_auteur') .PtoBR(propre($a, $dir));
		}
		}
	
	
	// on construit l'affichage des informations
	$leResume = "<div class='resume'>"
	. $i
	. "<span class='apropos-nom'>".$leNom."</span>"
	. "<span class='apropos-version'>v ".$info['version']."</span>"
	. "<span class='apropos-etat'> - ".plugin_etat_en_clair($info['etat'])."</span>"
	. $infoSVN
	. "<div class='apropos-description'>".$slogan."</div>"
	. "<span class='apropos-auteur'>".$auteur.$credit."</span>"
	. $documentation
	. $demonstration
	. "</div>";


	return "<li>"
	. $leResume
	. $erreur
	."</li>";
}

?>