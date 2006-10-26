<?php
//
//  espace de nommage
//
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_SPIPICIOUS',(_DIR_PLUGINS.end($p)));
 

include_spip('base/create');
include_spip('base/spipicious');
include_spip('inc/plugin');

//
// espace prive:  ajout de bouton
//
function spipicious_ajouterBoutons($boutons_admin) {
	// si on est admin
	
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
	  // on voit le bouton dans la barre "naviguer"
	  $boutons_admin['configuration']->sousmenu['spipicious']= new Bouton(
		"../"._DIR_PLUGIN_SPIPICIOUS."/img_pack/spipicious.png",  // icone
		_T('spipicious:spipicious')	// titre
		);
	}
	return $boutons_admin;	
}

//
//  base a jour ?
//

function spipicious_header_prive($texte) { 
		spipicious_verifier_base();
		return $texte;
}

//
// creation base ?
//

function spipicious_verifier_base() {
		$info_plugin_spipicious = plugin_get_infos(_NOM_PLUGIN_SPIPICIOUS);
		$version_plugin = $info_plugin_spipicious['version'];
		if (!isset($GLOBALS['meta']['spip_spipicious_version'])) {
			creer_base();
			ecrire_meta('spip_spipicious_version', $version_plugin);
			ecrire_metas();
		} else {
			$version_base = $GLOBALS['meta']['spip_spipicious_version'];
		}
		return true;
}


//
// recuperer l'id du groupe -tags-
//
function spipicious_get_idgroup_tags() {
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
  $result = spip_query("SELECT id_groupe,titre FROM {$table_pref}_groupes_mots WHERE titre='- tags -'");
  while($row=spip_fetch_array($result)){
      $groupe_tags = Array();
      $groupe_tags['id_groupe'] = $row['id_groupe'];
      $groupe_tags['titre'] = $row['titre'];
      return $groupe_tags;
  }
  return false;  
}

//
// Fonctions liees au calcul de balise
// 
function calcul_POPULARITE_TAG($id_mot,$id_article) {
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
  
  // a ameliorer 
  // pour tenir compte des extremes et eviter trop de classes
  $result = spip_query("SELECT id_mot FROM {$table_pref}_spipicious WHERE id_mot='$id_mot' AND id_article='$id_article'");
  return spip_num_rows($result);

} 
?>
