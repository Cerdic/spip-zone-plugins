<?php
/*
 * Plugin A propos des plugins pour SPIP 2
 * Liste les plugins actifs avec affichage icon, nom, version, etat, short description
 * Utilisation intensive des fonctions faisant cela dans le code de SPIP
 * Auteur Jean-Philippe Guihard
 * version 0.2 du 06 janvier 2011, 23h40
 * ajout de la possibilite de n'afficher que le nombre de plugin et extension  
 * code emprunte dans le code source de SPIP
 */
/* 
include_spip('inc/texte');
include_spip('inc/plugin');
include_spip('exec/admin_plugin');
include_spip('inc/charsets');
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
	$p->code = 'calcul_info_apropos("liste")';
	}
	$p->interdire_scripts = false;
	return $p;
}

// fait l tri dans l'argument passé avec la balise : apropos|liste, apropos|nombre, apropos|plugins, apropos|extensions
// liste pour afficher la totale, 
// nombre pour afficher le nombre total plugin et extensions
// plugins pour afficher le nombre de plugins
// extensions pour afficher le nombre d'extensions

function calcul_info_apropos($params){
// si parametre liste, alors tout afficher
if ($params == "liste"){
$affiche = charger_fonction('admin_plugin','exec');
$lcpa = liste_chemin_plugin_actifs();
$lpf = liste_plugin_files();

/* on s'occupe de la liste des extensions */
$liste_extensions_actives = apropos_affiche_les_extension(_DIR_EXTENSIONS);
// liste les plugins
$liste_des_pgI_actifs = apropos_affiche_les_PiG($lcpa,$lpf);
return $liste_des_pgI_actifs.$liste_extensions_actives;
}

// si parametre nombre, alors afficher que le nombre de plugins et extensions
if ($params == "nombre"){
$pig = liste_chemin_plugin_actifs();
$ext = liste_plugin_files(_DIR_EXTENSIONS);
$nbre_pig = count($pig);
$nbre_ext = count($ext);
return $nbre_ext+$nbre_pig;
}
if ($params == "plugins"){
$pig = liste_chemin_plugin_actifs();
$nbre_pig = count($pig);
return $nbre_pig;
}if ($params == "extensions"){
$ext = liste_plugin_files(_DIR_EXTENSIONS);
$nbre_ext = count($ext);
return $nbre_ext;
}}

function apropos_affiche_les_extension($liste_extensions_actives){
	$rese = "";
	if ($liste_extensions = liste_plugin_files(_DIR_EXTENSIONS)) {
		$rese .= "<div class='apropos-liste'>";
		$rese .= "<h3>".count($liste_extensions)." extensions activées automatiquement.</h3>";
		$format = 'liste'; 
		$rese .= apropos_plugins_afficher_list_dist(self(), $liste_extensions,$liste_extensions, _DIR_EXTENSIONS);// surcharge de fonction
		$rese .= "</div>\n";
	}
	return $rese;
}
/* Fin liste les extensions */


/* les fonctions utilisees pour les plugins */
// entete liste des plugins pour affichage du nombre du plugin actives
function apropos_affiche_les_PiG($lcpa,$lpf){
		$h3 = "<div class='apropos-liste'><h3>".sinon(
						singulier_ou_pluriel(count($lcpa), 'plugins_actif_un', 'plugins_actifs', 'count'),
						_T('plugins_actif_aucun')
						)."</h3>";
		$corps = apropos_affiche_les_plugin($lcpa, $lcpa, $format);
return $h3.$corps."</div>\n";
}

// Extrait de http://doc.spip.org/@affiche_les_plugins
function apropos_affiche_les_plugin($liste_plugins, $liste_plugins_actifs, $format='liste'){
	if (!$format)
		$format = 'liste';
	if (!in_array($format,array('liste','repertoires')))
		$format = 'repertoires';
	$res = apropos_plugins_afficher_list_dist(self(), $liste_plugins,$liste_plugins_actifs);

	if (!$res) return "";
	return	$res;
}

// Extrait de  http://doc.spip.org/@affiche_liste_plugins
/* Creation de la liste des plugins actifs, trie de la liste par ordre alphabetique*/
function apropos_plugins_afficher_list_dist($url_page,$liste_plugins, $liste_plugins_actifs, $dir_plugins=_DIR_PLUGINS,$afficher_un = 'afficher_plugin'){
	$get_infos = charger_fonction('get_infos','plugins');
	
	// je recupere la liste des plugin par leur nom
	$liste_plugins = array_flip($liste_plugins);
	foreach(array_keys($liste_plugins) as $chemin) {
		$info = $get_infos($chemin, false, $dir_plugins);
		$liste_plugins[$chemin] = strtoupper(trim(typo(translitteration(unicode2charset(html2unicode($info['nom']))))));
	}
	
	// je trie cette liste par le nm de chacun
	asort($liste_plugins);
	$exposed = urldecode(_request('plugin'));

	$block_par_lettre = false;//count($liste_plugins)>10;
	$fast_liste_plugins_actifs = array_flip($liste_plugins_actifs);
	$res = '';
	$block = '';
	$initiale = '';
	$block_actif = false;
	
	// pour chaque plugin, je vais aller chercher les informations complementaires a afficher
	// en l'occurrence, nom, version, etat, slogan, description et logo.
	foreach($liste_plugins as $plug => $nom){
		$block .= apropos_plugins_afficher_plugins($url_page, $plug, "item", $dir_plugins)."\n";
	}

	return "<ul>".$block."</ul>";
}

// Extrait de  http://doc.spip.org/@ligne_plug
/* Extrait les infos de chaque plugin */
function apropos_plugins_afficher_plugins($url_page, $plug_file, $class_li="item", $dir_plugins=_DIR_PLUGINS) {

	$force_reload = (_request('var_mode')=='recalcul');
	$get_infos = charger_fonction('get_infos','plugins');
	$info = $get_infos($plug_file, $force_reload, $dir_plugins);
	$leBloc = charger_fonction('afficher_plugin', 'plugins');
	

	$leResume = apropos_plugin_resumer($info, $dir_plugins, $plug_file, $url_page);
	return "<li>"
	. $leResume
	. $erreur
	."</li>";
}

// Extrait de Cartouche Resume a modifier pour l'affichage final
/* Traite les infos a afficher */
function apropos_plugin_resumer($info, $dir_plugins, $plug_file, $url_page) {
	if (is_readable($file = "$dir_plugins$plug_file/" . ($desc = "paquet") . ".xml")) {
		$lefichier = 'lepaquet';
		if (is_readable($file = "$dir_plugins$plug_file/" . ($desc = "plugin") . ".xml"))
		$lefichier = 'le pluginxml';
	}
	
	$prefix = $info['prefix'];
	$dir = "$dir_plugins$plug_file";
	$slogan = PtoBR(plugin_propre($info['slogan'], "$dir/lang/paquet-$prefix"));
	// une seule ligne dans le slogan : couper si besoin
	if (($p=strpos($slogan, "<br />"))!==FALSE)
		$slogan = substr($slogan, 0,$p);
	// couper par securite
	$slogan = couper($slogan, 180);
	
	$url = parametre_url($url_page, "plugin", substr($dir,strlen(_DIR_RACINE)));

	if (isset($info['logo']) and $i = trim($info['logo'])) {
		include_spip("inc/filtres_images_mini");
		$i = inserer_attribut(image_reduire("$dir/$i", 32),'alt','Icone du plugin '.$info['nom']);
		$i = "<span class='apropos-icon'>".$i."</span>";
	} else {
		$generic = _DIR_PLUGIN_APROPOS."img/generique.png"; //mettre une icone generique si pas d'icone de defini
		include_spip("inc/filtres_images_mini");
		$i = inserer_attribut(image_reduire("$generic", 32),'alt','Icone g&eacute;n&eacute;rique pour le plugin '.$info['nom']);
		$i = "<div class='apropos-icon'>$i</div>";
	}
	$auteur = implode($info['auteur']);

	return "<div class='resume'>"
	. $i
	. "<span class='apropos-nom'>"
	. typo(attribut_html($info['nom']))
	. "</span>"
	. " <span class='apropos-version'>v ".$lefichier.$info['version']."</span>"
	. " <span class='apropos-etat'> - "
	. plugin_etat_en_clair($info['etat'])
	. "</span>"
	. "<div class='apropos-description'>".$slogan.".</div><span class='apropos-auteur'>". _T('public:par_auteur') .$auteur.".</span>"
	. "</div>";


/*	$prefix = $info['prefix'];
	$dir = "$dir_plugins$plug_file";
	$slogan = typo(attribut_html($info['slogan'])); //PtoBR(plugin_propre($info['slogan'], "$dir/lang/paquet-$prefix"));
	$get_desc = charger_fonction('afficher_plugin','plugins');
	$desc = plugin_propre($info['description']);
	// une seule ligne dans le slogan : couper si besoin
	if (($p=strpos($slogan, "<br />"))!==FALSE)
		$slogan = substr($slogan, 0,$p);
	// couper par securite
	$slogan = couper($slogan, 80);

	$url = parametre_url($url_page, "plugin", substr($dir,strlen(_DIR_RACINE)));

	if (isset($info['logo']) and $i = trim($info['logo'])) {
		include_spip("inc/filtres_images_mini");
		$i = inserer_attribut(image_reduire("$dir/$i", 32),'alt','Icone du plugin '.$info['nom']);
		$i = "<div class='apropos-icon'>$i</div>";
	} else {
		$generic = _DIR_PLUGIN_APROPOS."img/generique.png"; //mettre une icone generique si pas d'icone de defini
		include_spip("inc/filtres_images_mini");
		$i = inserer_attribut(image_reduire("$generic", 32),'alt','Icone g&eacute;n&eacute;rique pour le plugin '.$info['nom']);
		$i = "<div class='apropos-icon'>$i</div>";
		//$i = '';
	}

	return "<div class='apropos-resume'>"
	. $i
	. " <span class='apropos-nom'>"
	. typo(attribut_html($info['nom']))
	. "</span>"
	. "<span class='apropos-version'> v ".$info['version']."</span>"
	. "<span class='apropos-etat'> - "
	. typo(attribut_html($info['etat']))
	. "</span>"
	. "<div class='short'>".$slogan."</div>"
	. "<div class='short'>".couper($desc,220)."</div>"
	. "</div>";
*/
}
?>