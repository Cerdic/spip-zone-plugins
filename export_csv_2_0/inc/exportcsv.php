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

include_spip("base/db_mysql");
include_spip("base/abstract_sql");
include_spip("base/exportcsv_librairie");

# éléments d'écriture des lignes CSV ou tableau
global $tr, $l, $g, $gg, $gd, $th, $ht,	$d, $s, $g, $d, $g;
# par défaut, éléments de lignes CSV
$tr = "";
$l = chr(13).chr(10);
$g = $gg = $gd = $th = $ht = '"';
$d = ';';
$s = $g.$d.$g;

# -----------------------------------------------
# -----------------------------------------------
function exportcsv_make($return = true) {
	# éléments d'écriture des lignes CSV ou tableau
	global $tr, $l, $g, $gg, $gd, $th, $ht,	$d, $s, $g, $d, $g;
	global $connect_toutes_rubriques, $prefix_t;
	
	$config = lire_config(_PLUGIN_NAME_EXPORTCSV);

# les variables array 
	$secteur = lire_config(_PLUGIN_NAME_EXPORTCSV.'/secteur');
	$rub = lire_config(_PLUGIN_NAME_EXPORTCSV.'/rub');
	$gmc_art = lire_config(_PLUGIN_NAME_EXPORTCSV.'/articles_l_gmc');
	$gmc_rub = lire_config(_PLUGIN_NAME_EXPORTCSV.'/rubriques_d_gmc');
	
# TEST si config faite
	if(is_null($config)) {
		echo _T('exportcsv:erreur_pas_de_config');
		if(!$connect_toutes_rubriques) 
			echo _T('exportcsv:erreur_admin_config');
		else
			echo _T('exportcsv:erreur_lien_config');
		exit;
	}
# TEST si au moins 1 rubrique est configurée
	if(count($secteur) < 1 && count($rub) < 1) {
		echo _T('exportcsv:erreur_pas_de_rub');
		if(!$connect_toutes_rubriques) 
			echo _T('exportcsv:erreur_admin_config');
		else
			echo _T('exportcsv:erreur_lien_config');
		exit;
	}

	$titre_col = $art_fields = $rub_fields = $rub_mc = $art_mc = $data = array();
	$cpt_col = $j = $k = $n = $x = $y = 0;
	
# intitulé colonnes et groupes de MC
	ksort($config);

	foreach($config as $cle => $val) {
	# dans un 1er temps, on ne prend que les non array pour les titres de colonne
	# car on a pas les noms des groupes de MC , juste leur ID
		if(!is_array($val)) {
		
			if(!is_null($val)) {
#	var_dump($val); echo ' !<br>';
				$titre_col[$cpt_col] = ereg_replace("_[a-z]_", "_", $cle);
#	sdn_debug($titre_col[$cpt_col], 'val '.$cle);
				$cpt_col++;
			}

		} else {
		# dans un 2nd temps, récupères les titres des groupes 
		# de colonne car on a leur ID trié par type (rub ou art)
			if(ereg("^rubriques_", $cle)) { # si c un array pour les groupes
			# on sépare les types de groupe : pour rub ou art 
				foreach($val as $clev => $valv) { # Gr. MC rubrique
					$rub_mc[$j] = $valv;
					$j++;
#					$q = "SELECT titre 
#					FROM ".$prefix_t."groupes_mots 
#					WHERE id_groupe='".$valv."'";
/*					
					$select = array('titre');
					$from = array($prefix_t."groupes_mots");
					$where = array("id_groupe='".$valv."'");
*/					
					$r = sql_fetsel('titre', $prefix_t."groupes_mots", "id_groupe='".$valv."'");
#					$r = sql_fetch(sql_select($select, $from, $where));
					$titre_col[$cpt_col] = $r['titre'];
					$cpt_col++;
sdn_debug($r['titre'], 'titre rub');
				}					
			}
			if(ereg("^articles_", $cle)) {  # Gr. MC article
sdn_debug($cle, 'is_art');
				foreach($val as $clev => $valv) {
					$art_mc[$k] = $valv;
					$k++;
#					$q = "SELECT titre 
#					FROM ".$prefix_t."groupes_mots 
#					WHERE id_groupe='".$valv."'";

					$select = array('titre');
					$from = array($prefix_t."groupes_mots");
					$where = array("id_groupe='".$valv."'");
					
					$r = sql_fetsel($select, $from, $where);
#					$r = sql_fetch(sql_select($select, $from, $where));

#					$r = spip_fetch_array(spip_query($q));
					$titre_col[$cpt_col] = $r['titre'];
					$cpt_col++;
sdn_debug($r['titre'], 'titre art');
				}
			}
		}
	}
	$nb_col = count($titre_col);

# TEST si au moins 1 champ à afficher est configurée
	if($nb_col < 1) {
		echo _T('exportcsv:erreur_pas_de_champ');
		if(!$connect_toutes_rubriques) 
			echo _T('exportcsv:erreur_admin_config');
		else
			echo _T('exportcsv:erreur_lien_config');
		exit;
	}	

# écriture de la requete principale
# Tous les articles publiés dans les secteurs et/ou les rubriques 
#	$sql = "SELECT articles.id_rubrique, articles.id_article ";
	$sel_champs = array("articles.id_rubrique", "articles.id_article");
	$sel_from = array($prefix_t."articles AS `articles`");
	$sel_where = $sel_order = array();
#array_push ($stack, "pomme", "bleuet");

	# les champs à sélectionner
	for($i = 0; $i < $nb_col; $i++) {
		if(ereg("^articles_", $titre_col[$i])) {
		# pour les articles
			$art_fields[$x] = substr(strstr($titre_col[$i], "_"), 1);
			$x++;
			
#			if($i <= ($nb_col-1))
#				$sql .= ", ";
#			$sql .= ereg_replace("^articles_", "articles.", $titre_col[$i]);
			array_push ($sel_champs, ereg_replace("^articles_", "articles.", $titre_col[$i]));
		}
		elseif(ereg("^rubriques_", $titre_col[$i])) {
		# pour les rubriques
			$rub_fields[$y] = substr(strstr($titre_col[$i], "_"), 1);
			$y++;
		}
	}
#	$sql .= " FROM ".$prefix_t."articles AS `articles` 
#	WHERE ";
	$where = '';
	if(count($secteur) > 0) {
		$where .= "(";
		for($i = 0; $i < count($secteur); $i++) {
			if($i > 0)
				$where .= "OR ";
			$where .= "articles.id_secteur = '".$secteur[$i]."' ";
		}
		$where .= ") ";
		
		array_push($sel_where, $where);
	}
	
	$where = '';
	if(count($rub) > 0) {
		if(count($secteur) > 0) 
			$where .= "OR ";
		$where .= "( ";
		for($i = 0; $i < count($rub); $i++) {
			if($i > 0)
				$where .= "OR ";
			$where .= "articles.id_rubrique = '".$rub[$i]."' ";
		}
		$where .= ") ";
		
		array_push($sel_where, $where."AND (articles.statut = 'publie')");
	}

	array_push($sel_where, "(articles.statut = 'publie')");
	
	array_push($sel_order, "articles.id_secteur", "articles.id_rubrique");


	if(!$return) {
#		$sql_cpt = $sql; # pour comptage total lignes
#		$sql .= " ";
		$sel_limit = "0,100";
	}
	else
		$sel_limit = "";
	
#	sdn_debug("<b>SQL :</b> ".$sql);
#	$req = spip_query($sql);

#sql_select ( $select = array(), $from = array(), $where = array(), $groupby = array(), $orderby = array(), $limit = ’’, $having = array(), $serveur=’’, $option=true)
	
	if(!$return) {	# pour comptage total lignes
#	$req_cpt = spip_query($sql_cpt);
#		$req_cpt = sql_select($sel_champs, $sel_from, $sel_where, $sel_group);
		$nb_lignes = sql_countsel($sel_from, $sel_where, $sel_group);
	}
	
	$req = sql_select($sel_champs, $sel_from, $sel_where, '', $sel_order, $sel_limit);
#	echo sql_get_select($sel_champs, $sel_from, $sel_where, '', $sel_order, $sel_limit);
	ecco_pre($sel_champs, 'sel_champs');
	ecco_pre($sel_from, 'sel_from');
	ecco_pre($sel_where, 'sel_where');
	ecco_pre($sel_order, 'sel_order');

	while($res = sql_fetch($req)) {
# article	
		$id_art = $res['id_article'];
		$id_rub = $res['id_rubrique'];

	# éléments de l'article à afficher
		for($i = 0; $i < count($art_fields); $i++) {
		# nettoyage des données (raccourcis typo, etc.)
			$data[$n] = supprimer_numero(textebrut(propre($res[$art_fields[$i]])));
			$n++;
		}
		
	# mots-clés article
		if(count($art_mc) > 0) {

			for($i = 0; $i < count($art_mc); $i++) {
				
				$mot = "";
#				$sql4 = "SELECT mots.titre 
#				FROM ".$prefix_t."mots_articles AS `L1`, ".$prefix_t."mots AS `mots` 
#				WHERE (L1.id_article = '".$id_art."') 
#				AND (mots.id_groupe = '".$art_mc[$i]."') 
#				AND mots.id_mot=L1.id_mot 
#				GROUP BY mots.id_mot";
				
				$select = array("mots.titre");
				$from = array($prefix_t."mots_articles AS `L1`", $prefix_t."mots AS `mots`");
				$where = array("(L1.id_article = '".$id_art."') AND (mots.id_groupe = '".$art_mc[$i]."') AND mots.id_mot=L1.id_mot");
				$group = array("mots.id_mot");
				
				$req4 = sql_select($select, $from, $where, $group);
					
				while($res4 = sql_fetch($req4)) {

					$mot .= $res4['titre'].chr(10);
				}
				$data[$n] = substr($mot, 0, -1);
				$n++;
#			sdn_debug( "<hr><b>SQL 4 :</b> ".$sql4);
			}
		}

	# rubrique contenant l'article
#		$sql2 = "SELECT rubriques.id_rubrique ";
		$select = array("rubriques.id_rubrique");
		
		for($i = 0; $i < count($rub_fields); $i++) {				
#			if($i <= (count($rub_fields)-1))
#				$sql2 .= ", ";
				
			array_push($select, "rubriques.".$rub_fields[$i]);
		}
#		$sql2 .= " FROM ".$prefix_t."rubriques AS `rubriques`".
#		WHERE (rubriques.id_rubrique = '".$id_rub."') 
#		AND (rubriques.statut = 'publie')";
		$from = array($prefix_t."rubriques AS `rubriques`");
		$where = array("(rubriques.id_rubrique = '".$id_rub."') AND (rubriques.statut = 'publie')");

#		sdn_debug("<hr><b>SQL 2 :</b> ".$sql2);

		$res2 = sql_fetsel($select, $from, $where);

	# éléments de la rubrique à afficher
		for($i = 0; $i < count($rub_fields); $i++) {
		# nettoyage des données (raccourcis typo, etc.)
			$data[$n] = supprimer_numero(textebrut(propre($res2[$rub_fields[$i]])));
			$n++;
		}

	# mots-clés pour la rubrique
		if(count($rub_mc) > 0) { # si des mots-clés pour rubrique sont sélectionnés
		
			for($i = 0; $i < count($rub_mc); $i++) {
				$mot = "";
#				$sql3 = "SELECT mots.titre  
#				FROM ".$prefix_t."mots_rubriques AS `L1`, ".$prefix_t."mots AS `mots` 
#				WHERE (L1.id_rubrique = '".$id_rub."')
#				AND (mots.id_groupe = '".$rub_mc[$i]."') 
#				AND mots.id_mot=L1.id_mot 
#				GROUP BY mots.id_mot";
#				$req3 = spip_query($sql3);

				$select = array("mots.titre");
				$from = array($prefix_t."mots_rubriques AS `L1`", $prefix_t."mots AS `mots`");
				$where = array("(L1.id_rubrique = '".$id_rub."') AND (mots.id_groupe = '".$rub_mc[$i]."') AND mots.id_mot=L1.id_mot");
				$group = array("mots.id_mot");
				
				$req3 = sql_select($select, $from, $where, $group);

				while($res3 = sql_fetch($req3)) {
					$mot .= $res3['titre'].chr(10);
				}
				$data[$n] = substr($mot, 0, -1);
				$n++;
			}			
		}
		
#		sdn_debug( "<hr><b>SQL 3 :</b> ".$sql3);
		
	}
# DEBUG
	ecco_pre($rub_mc, "rub MC");
	ecco_pre($titre_col, "colonnes");
	ecco_pre($data, "data");
	ecco_pre($config, "exportcsv");
#	sdn_debug(htmlentities($outh));

# écriture du contenu($data) dans un fichier(true) ou tableau(false) selon $return
# initialisation écriture des lignes : 
	# (defaut : extraction vers CSV)
	# extraction pour affichage aperçu en table HTML
	if(!$return) {
		$tr = '<tr>';
		$l = '</tr>';
		$th = '<th>';
		$ht = '</th>';
		$gg = '<td>';
		$gd = '</td>';
		$d = ' ';
		$s = $gd.$gg;
	}
	#	$outh =  entetes (headers))
	#	$outl = lignes de données
	$outh = $tr; $outl = "";

# écriture des titres des colonnes
	for($i = 0; $i < $nb_col; $i++) {
		$outh .= $th.str_replace("_", " ", $titre_col[$i]).$ht.$d;
	}
	$outh = substr($outh, 0, -1).$l;
	
#	$nb_lignes = 0;
	
	for($i = 0; $i < count($data); $i += $nb_col) {
		$outl .= $tr;
		for($z = $i; $z < ($i+$nb_col); $z++) {
			
			$outl .= $gg.$data[$z].$gd.$d;
		}
		$outl = substr($outl, 0, -1).$l;
#		$nb_lignes++;
	}
	$out = $outh.$outl;

	if($return) return $out; # l'export CSV 
	else {
		$nb_lignes > 100 ? $nb_res = 100 : $nb_res = $nb_lignes;
		
		echo '<p style="margin-top:0;"><strong>'._T('exportcsv:info_nb_lignes_a').$nb_res._T('exportcsv:sur_total').$nb_lignes.' :</strong></p>
	<table>'.$out.'</table>'; # affichage de l'export
	}

}

function exportcsv_make_petition($id_article) {
	# éléments d'écriture des lignes CSV
	global $tr, $l, $g, $gg, $gd, $th, $ht,	$d, $s, $g, $d, $g, $prefix_t;
/*
$tr = "";
$l = chr(13).chr(10);
$g = $gg = $gd = $th = $ht = '"';
$d = ';';
$s = $g.$d.$g;
*/	
	
# spécifique FAPT
#	$titre_col = array('nom_email', 'ad_email', 'message', 'metier', 'ville', 'dept');
# base 
	$titre_col = array('nom_email', 'ad_email', 'nom_site', 'url_site', 'message');

	$nb_col = count($titre_col);
	
	#	$outh =  entetes (headers)
	#	$outl = lignes de données
	$outh = $outl = $tr;
	
	if(is_numeric($id_article)) {

# spécifique FAPT
#		$sql = "SELECT nom_email, ad_email, message, metier, ville, dept 
#		FROM ".$prefix_t."signatures WHERE id_article=".$id_article." AND statut='publie'";
		# base
#		$sql = "SELECT * FROM spip_signatures WHERE id_article=".$id_article." AND statut='publie'";
		$select = $titre_col;
		$from = array($prefix_t."signatures");
		$where = array("id_article=".$id_article." AND statut='publie'");
		
#		$req = spip_query($sql);
#		$num = spip_num_rows($req);
		$req = sql_select($select, $from, $where);
		$num = sql_countsel($from, $where);
		
	# écriture des titres des colonnes
		# Ligne 1 CSV = titre de l'article + nombre de signatures
#		$req_titre = spip_fetch_array(spip_query("SELECT titre FROM ".$prefix_t."articles WHERE id_article=".$id_article));
		$req_titre = sql_fetsel("titre", $prefix_t."articles", "id_article=".$id_article);
		
		$outh .= $th.supprimer_numero(textebrut(propre($req_titre['titre']))).$ht.$d.$num.' signatures'.$l;
		
		# Ligne 2 CSV = titres des colonnes		
		for($i = 0; $i < $nb_col; $i++) {
			$outh .= $th.str_replace("_", " ", $titre_col[$i]).$ht.$d;
		}
		$outh = substr($outh, 0, -1).$l;
				
		while($res = sql_fetch($req)) {
			$outl .= $tr; 
			
			for($z = 0; $z < $nb_col; $z++) {
				$outl .= $gg.$res[$titre_col[$z]].$gd.$d;
			}
			
			$outl = substr($outl, 0, -1).$l;
		}
		$out = $outh.$outl;

		return $out; # l'export CSV 
		
	} else 
		return false;
}
?>