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

# $debog = 1 : affichage des messages 'ecco_pre' et 'sdn_debug'
global $debog, $prefix_t; 
$debog = 0;  $prefix_t = $GLOBALS['table_prefix'].'_';

#var_dump($GLOBALS['table_prefix']); exit;

if (!defined('_DIR_PLUGIN_EXPORTCSV')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_EXPORTCSV',(_DIR_PLUGINS.end($p))."/");
}
# repertoire img_pack/
if (!defined("_DIR_IMG_EXPORTCSV")) {
	define('_DIR_IMG_EXPORTCSV', _DIR_PLUGIN_EXPORTCSV.'img_pack/');
}
# prefixe du plugin
if (!defined("_PLUGIN_NAME_EXPORTCSV")) {
	define('_PLUGIN_NAME_EXPORTCSV', 'exportcsv');
}

function acces_interdit() {
	include_spip('inc/minipres');
	echo minipres(_T('ecrire:avis_acces_interdit'));	
	exit;
}
function acces_probleme($msg) {
	include_spip('inc/minipres');
	echo minipres(_T($msg));	
	exit;
}
function sdn_debug($val, $l=0) {
	global $debog;
	if($debog == 1)
		echo "<strong>DEBUG **</strong> ".$l . " : " . $val . " **<br>";
}
function ecco_pre($val, $nom = '') {
	global $debog;
	if($debog == 1) {
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
	global $prefix_t;
	
#	$q = "SELECT id_groupe, titre FROM spip_groupes_mots WHERE $type = 'oui' ORDER BY titre" ; 

	$select = array("id_groupe", "titre");
	$from = array($prefix_t."groupes_mots");
	$where = array("tables_liees LIKE '%$type%'");
	$order = array("titre");
	
	if($type == 'articles') $nom = $type.'_l_gmc[]';
	else $nom = $type.'_d_gmc[]';
	
	$req = sql_select($select, $from, $where, '', $order);
	while($r = sql_fetch($req)) {
		
		echo '<label class="racine">
		<input type="checkbox" name="'.$nom.'" value="'.$r['id_groupe'].'"';
		
		is_checked(substr($nom,0,-2), $r['id_groupe']);
		
		echo ' /> ' 
		.supprimer_numero($r['titre']).'</label>';
	}
}

function is_config() {
global $prefix_t;
#	$q = spip_query("SELECT nom FROM spip_meta WHERE nom LIKE '"._PLUGIN_NAME_EXPORTCSV."'");
#	$r = spip_fetch_array($q);
	if(sql_countsel($prefix_t."meta", "nom LIKE '"._PLUGIN_NAME_EXPORTCSV."'") <= 0)
		return false;
	else
		return true;
}
?>