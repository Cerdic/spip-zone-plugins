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
		$current_version= 0.0;
		if (!isset($GLOBALS['meta']['spip_spipicious_version'])) {
			creer_base();
			// ajout d'index
			spip_query("ALTER TABLE spip_spipicious ADD INDEX ( `id_auteur` )");
			spip_query("ALTER TABLE spip_spipicious ADD INDEX ( `id_article` )");
			spip_query("ALTER TABLE spip_spipicious ADD INDEX ( `id_mot` )");
			// creation groupe - tags - 
			$table_pref = 'spip';
			if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
				$result = spip_query("SELECT id_groupe FROM `{$table_pref}_groupes_mots` WHERE titre = '- tags -'"); // creation du groupe de mots cles uniquement si n'existe pas
			if (spip_num_rows($result) == 0) {
				spip_query("INSERT INTO {$table_pref}_groupes_mots (id_groupe, titre, descriptif, texte, unseul, obligatoire, articles, breves, rubriques, syndic, minirezo, comite, forum , maj )
				VALUES ('',  '- tags -', '', '', '', '', 'oui', '', 'non', '', 'oui', 'non', 'non', NOW( ));");
			}
			
			ecrire_meta('spip_spipicious_version', $current_version=0.02);
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

	$result = spip_query("SELECT id_mot FROM {$table_pref}_spipicious WHERE id_mot='$id_mot' AND id_article='$id_article'");
	$nb = spip_num_rows($result);
	if ($nb > 10) $nb = 10; // FIXME a ameliorer pour tenir compter extremes, au lieu compteur avoir echelle log
	return $nb;
}

//
// Verifie le article est encore lie un tag (synchronisation spip_mots_articles et spip_spipicious)
//
function spipicious_maintenance_nuage_article($id_article) {
	$table_pref = 'spip';
	if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
	$idgroup_tags = spipicious_get_idgroup_tags();

	$result = spip_query("SELECT id_mot FROM {$table_pref}_mots_articles WHERE id_article=$id_article"); // ts les mots cles lies a l'article
	while($row=spip_fetch_array($result)){
		$current_id_mot = $row['id_mot'];
		$result2 = spip_query("SELECT id_mot FROM {$table_pref}_mots WHERE id_mot=$current_id_mot AND id_groupe=$idgroup_tags");  // est ce que le mot appartient au groupe - tags - ?
		if (spip_num_rows($result2) > 0) { // oui, est il encore lie a l'article (suite aux mises a jour) ? 
			$result3 = spip_query("SELECT id_mot FROM {$table_pref}_spipicious WHERE id_article=$id_article AND id_mot=$current_id_mot LIMIT 1");
			if (spip_num_rows($result3) == 0)
			spip_query("DELETE FROM {$table_pref}_mots_articles WHERE id_article=$id_article AND id_mot=$current_id_mot LIMIT 1");
		}
	}
}

//
// filtres (pour les pages publiques)
//

//
// filtre pour dernier mot d'un chaine 
// ex. "berlin lille marseille" -> retourne "marseille"
//
function dernier_mot($str) {
	$pos = strrpos($str, " ");
	if ($pos === false) return $str; // php4.03+
	return substr($str,$pos+1);
}

?>