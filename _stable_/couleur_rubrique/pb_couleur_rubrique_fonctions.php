<?php
//
// ajout bouton
// 
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_PB_COULEUR_RUBRIQUE',(_DIR_PLUGINS.end($p)));
 
 

if ($_POST["pb_couleur_rubrique"] && $GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
	$couleur = str_replace("#", "", $_POST["pb_couleur_rubrique"]);
	$id_rubrique = $_GET["id_rubrique"];
	
	
	ecrire_meta("pb_couleur_rubrique$id_rubrique",$couleur);
	if ($_POST["supprimer"]) ecrire_meta("pb_couleur_rubrique$id_rubrique","");
	ecrire_metas();
	
}
 
//
// functions
//

function pb_couleur_rubrique($id_rubrique) {
			$pb_couleur_rubrique = lire_meta("pb_couleur_rubrique$id_rubrique");
//			if (!$pb_couleur_rubrique) $pb_couleur_rubrique = "#999999";
	
	return $pb_couleur_rubrique;
}

?>
