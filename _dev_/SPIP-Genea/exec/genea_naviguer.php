<?php
/* *********************************************************************
   *
   * Copyright (c) 2007-2008
   * Xavier Burot
   * fichier : exec/genea_naviguer.php
   *
   * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
   *
   *********************************************************************
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// -- Chargement des fonctions supplementaires --------------------------
include_spip('inc/presentation');
include_spip('base/abstract_sql');
/*include_spip('inc/texte');
include_spip('inc/rubriques');
include_spip('inc/actions');
include_spip('inc/forum');
include_spip('inc/mots');
include_spip('inc/documents');
charger_generer_url();*/

// -- Fonction d'apppel de la partie d'administration des arbres
function exec_genea_naviguer(){
///// Definition des variables generales
	global $connect_statut, $connect_toutes_rubriques, $titre;
}

// http://doc.spip.org/@tester_rubrique_vide
function genea_tester_rubrique_vide($id_rubrique) {
	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_rubriques WHERE id_parent='$id_rubrique' LIMIT 1"));
	if ($n['n'] > 0) return false;

	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_articles WHERE id_rubrique='$id_rubrique' AND (statut='publie' OR statut='prepa' OR statut='prop') LIMIT 1"));
	if ($n['n'] > 0) return false;

	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_breves WHERE id_rubrique='$id_rubrique' AND (statut='publie' OR statut='prop') LIMIT 1"));
	if ($n['n'] > 0) return false;

	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_syndic WHERE id_rubrique='$id_rubrique' AND (statut='publie' OR statut='prop') LIMIT 1"));
	if ($n['n'] > 0) return false;

	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_documents_rubriques WHERE id_rubrique='$id_rubrique' LIMIT 1"));
	if ($n['n'] > 0) return false;

	$n = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_genea WHERE id_rubrique='$id_rubrique' LIMIT 1"));
	if ($n['n'] > 0) return false;

	return true;
}

?>