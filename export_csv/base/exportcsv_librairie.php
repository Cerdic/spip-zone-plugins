<?php
/*##############################################################
 * ExportCSV
 * Export des articles / rubriques SPIP en fichiers CSV.
 *
 * Auteur :
 * Stéphanie De Nadaï
 * webdesigneuse.net
 * © 2008 - Distribué sous licence GNU/GPL
 *
##############################################################*/

global $debug; $debug = 0; 

if (!defined('_DIR_PLUGIN_EXPORTCSV')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_EXPORTCSV',(_DIR_PLUGINS.end($p))."/");
}
# repertoire img_pack/
if (!defined("_DIR_IMG_EXPORTCSV")) {
	define('_DIR_IMG_EXPORTCSV', _DIR_PLUGIN_EXPORTCSV.'img_pack/');
}
// prefixe du plugin
if (!defined("_PLUGIN_NAME_EXPORTCSV")) {
	define('_PLUGIN_NAME_EXPORTCSV', 'exportcsv');
}

function acces_interdit() {
	debut_page(_T('avis_acces_interdit'), "documents", _PLUGIN_NAME_EXPORTCSV);
	debut_gauche();
	debut_droite();
	echo "<strong>"._T('avis_acces_interdit')."</strong>"
	.fin_gauche().fin_page();
	exit;
}
function sdn_debug($val, $l=0) {
	global $debug;
	if($debug == 1)
		echo "<strong>DEBUG **</strong> ".$l . " : " . $val . " **<br>";
}
function ecco_pre($val, $nom = '') {
	global $debug;
	if($debug == 1) {
		echo '<pre><strong>DEBUG ('.$nom.') :</strong> ';
		print_r($val);
		echo '</pre><hr>';
	}
}
function is_checked($input, $val) {
	$tab = lire_config(_PLUGIN_NAME_EXPORTCSV.'/'.$input);
	$trouve = 0;
	for($i = 0; $i < count($tab); $i++) {
		if($tab[$i] == $val)
			$trouve = 1;
	}
	echo ($trouve == 1 ? 'checked="checked"' : '');
}

function excsv_mots_cles($type) {
	$q = "SELECT id_groupe, titre FROM spip_groupes_mots WHERE $type = 'oui' ORDER BY titre" ; 

	if($type == 'articles') $nom = $type.'_l_gmc[]';
	else $nom = $type.'_d_gmc[]';
	
	$req = spip_query($q);
	while($r = spip_fetch_array($req)) {
		
		echo '<label class="racine">
		<input type="checkbox" name="'.$nom.'" value="'.$r['id_groupe'].'"';
		
		is_checked(substr($nom,0,-2), $r['id_groupe']);
		
		echo ' /> ' 
		.supprimer_numero($r['titre']).'</label>';
	}
}

function is_config() {
	$q = spip_query("SELECT nom FROM spip_meta WHERE nom LIKE '"._PLUGIN_NAME_EXPORTCSV."'");
	$r = spip_fetch_array($q);
	if(spip_num_rows($q) <= 0)
		return false;
	else
		return true;
}
?>