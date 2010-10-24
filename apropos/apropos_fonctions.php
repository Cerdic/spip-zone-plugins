<?php
/*
 * Plugin A propos des plugins pour SPIP 2
 * Liste les plugins actifs avec affichage icon, nom, version, etat, short description
 * Utilisation intensive des fonctions faisant cela dans le code de SPIP
 * Auteur Jean-Philippe Guihard
 * version 0.ß4 du 24 octobre 2010, 15h40
 * code emprunte dans le code source de SPIP
 */
 
include_spip('inc/texte');
include_spip('inc/plugin');
include_spip('exec/admin_plugin');
include_spip('inc/charsets');

function balise_APROPOS_dist($p) {
	$p->code = 'calcul_info_apropos()';
	$p->interdire_scripts = false;
	return $p;
}


function calcul_info_apropos(){
$affiche = charger_fonction('admin_plugin','exec');
$lcpa = liste_chemin_plugin_actifs();
$lpf = liste_plugin_files();

/* liste les extensions */
$liste_extensions_actives = apropos_affiche_les_extension(_DIR_EXTENSIONS);
// liste les plugins
$liste_des_pgI_actifs = apropos_affiche_les_PiG($lcpa,$lpf);

return $liste_extensions_actives.$liste_des_pgI_actifs;
}


function apropos_affiche_les_extension($liste_extensions_actives){
	$rese = "";
	if ($liste_extensions = liste_plugin_files(_DIR_EXTENSIONS)) {
		$rese .= "<div class='plug-liste'>";
		$rese .= "<h3>"._T('plugins_liste_extensions')." activées automatiquement.</h3>";
		$format = 'liste'; 
		$rese .= apropos_plugins_afficher_list_dist(self(), $liste_extensions,$liste_extensions, _DIR_EXTENSIONS);// surcharge de fonction
		$rese .= "</div>\n";
	}
	return $rese;
}
/* Fin affiche les extensions */


/* les fonctions utilisees pour les plugins */
//entete liste des plugins affichage du nombre du plugin actives
function apropos_affiche_les_PiG($lcpa,$lpf){
		$h3 = "<div class='plug-liste'><h3>".sinon(
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
/* je ne sais pâs trop a quoi ca sert ca cree le gros bloc des <ul>*/
function apropos_plugins_afficher_list_dist($url_page,$liste_plugins, $liste_plugins_actifs, $dir_plugins=_DIR_PLUGINS,$afficher_un = 'afficher_plugin'){
	$get_infos = charger_fonction('get_infos','plugins');
	$liste_plugins = array_flip($liste_plugins);
	foreach(array_keys($liste_plugins) as $chemin) {
		$info = $get_infos($chemin, false, $dir_plugins);
		$liste_plugins[$chemin] = strtoupper(trim(typo(translitteration(unicode2charset(html2unicode($info['nom']))))));
	}
	asort($liste_plugins);
	$exposed = urldecode(_request('plugin'));

	$block_par_lettre = false;//count($liste_plugins)>10;
	$fast_liste_plugins_actifs = array_flip($liste_plugins_actifs);
	$res = '';
	$block = '';
	$initiale = '';
	$block_actif = false;
	foreach($liste_plugins as $plug => $nom){
		if (($i=substr($nom,0,1))!==$initiale){
			$res .= $block_par_lettre ? affiche_block_initial($initiale,$block,$block_actif): $block;
			$initiale = $i;
			$block = '';
			$block_actif = false;
		}
		// le rep suivant
		$actif = @isset($fast_liste_plugins_actifs[$plug]);
		$block_actif = $block_actif | $actif;
		$expose = ($exposed AND ($exposed==$plug OR $exposed==$dir_plugins . $plug OR $exposed==substr($dir_plugins,strlen(_DIR_RACINE)) . $plug));
		$block .= apropos_plugins_afficher_plugins_dist($url_page, $plug, $actif, $expose, "item", $dir_plugins)."\n";
	}
	$res .= $block_par_lettre ? apropos_affiche_block_initiale($initiale,$block,$block_actif): $block;
	$class = basename($dir_plugins);
	return $res ? "<ul class='liste-items plugins $class'>$res</ul>" : "";
}

// Extrait de  http://doc.spip.org/@ligne_plug
/* je ne sais pâs trop a quoi ca sert, creation des blocs <li> */
function apropos_plugins_afficher_plugins_dist($url_page, $plug_file, $actif, $expose=false, $class_li="item", $dir_plugins=_DIR_PLUGINS) {

	static $id_input = 0;
	static $versions = array();

	$force_reload = (_request('var_mode')=='recalcul');
	$get_infos = charger_fonction('get_infos','plugins');
	$info = $get_infos($plug_file, $force_reload, $dir_plugins);
	$prefix = $info['prefix'];
	$erreur = (!isset($info['erreur']) ? ''
	: ("<div class='erreur'>" . join('<br >', $info['erreur']) . "</div>"));
	
	$versions[$prefix] = $id = isset($versions[$prefix]) ? $versions[$prefix] + 1 : '';

	$class_li .= ($actif?" actif":"") . ($expose?" on":"") . (isset($info['erreur']) ? " erreur" : '');

	return "<li>"
	.  apropos_plugin_resumer($info, $dir_plugins, $plug_file, $url_page, $actif)
	. $cfg
	. $erreur
	. "<div class='details'>" // pour l'ajax de exec/info_plugin
	. (!$expose ? '' : affiche_bloc_plugin($plug_file, $info))
	. "</div>"
	."</li>";
}

// Extrait de Cartouche Resume a modifier pour l'affichage final
/* Recupere les infos du fichier plugin.xml */
function apropos_plugin_resumer($info, $dir_plugins, $plug_file, $url_page, $actif) {

	$get_desc = charger_fonction('afficher_plugin','plugins');
	$desc = plugin_propre($info['description']);
	$dir = $dir_plugins.$plug_file;
	if (($p=strpos($desc, "<br />"))!==FALSE)
		$desc = substr($desc, 0,$p);
	$url = parametre_url($url_page, "plugin", $dir);
	$leNom = typo($info['nom']);
	if (isset($info['icon']) and $i = trim($info['icon'])) {
		include_spip("inc/filtres_images_mini");
		$i = inserer_attribut(image_reduire("$dir/$i", 32),'alt','Icone du plugin '.$leNom);
		$i = "<div class='plug-icon'>$i</div>";
	} else {
		$generic = $dir_plugins."apropos/generique.png"; //mettre une icone generique si pas d'icone de defini
		include_spip("inc/filtres_images_mini");
		$i = inserer_attribut(image_reduire("$generic", 32),'alt','Icone g&eacute;n&eacute;rique pour le plugin '.$leNom);
		$i = "<div class='plug-icon'>$i</div>";
		//$i = '';
}
	return "<div class='plug-resume'>"
	. $i." <span class='plug-nom'>".$leNom."</span>"
	. " <span class='plug-version'>".$info['version']."</span>"
	. " <span class='plug-etat'> - "
	. plugin_etat_en_clair($info['etat'])
	. "</span>"
	. "<div class='plug-description'>".couper($desc,170)."</div>"
	. "</div>";
}



?>