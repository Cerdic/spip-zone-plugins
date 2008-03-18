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

# -----------------------------------------------
# -----------------------------------------------
function exportcsv_make($return = true) {
	global $connect_toutes_rubriques;
	
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
			if(strlen($val) > 0) {
				$titre_col[$cpt_col] = ereg_replace("_[a-z]_", "_", $cle);
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
					$q = "SELECT titre 
					FROM spip_groupes_mots 
					WHERE id_groupe='".$valv."'";
					$r = spip_fetch_array(spip_query($q));
					$titre_col[$cpt_col] = $r['titre'];
					$cpt_col++;
				}					
			}
			if(ereg("^articles_", $cle)) {  # Gr. MC article
				foreach($val as $clev => $valv) {
					$art_mc[$k] = $valv;
					$k++;
					$q = "SELECT titre 
					FROM spip_groupes_mots 
					WHERE id_groupe='".$valv."'";
					$r = spip_fetch_array(spip_query($q));
					$titre_col[$cpt_col] = $r['titre'];
					$cpt_col++;
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
	$sql = "SELECT articles.id_rubrique, articles.id_article ";

	# les champs à sélectionner
	for($i = 0; $i < $nb_col; $i++) {
		if(ereg("^articles_", $titre_col[$i])) {
		# pour les articles
			$art_fields[$x] = substr(strrchr($titre_col[$i], "_"), 1);
			$x++;
			
			if($i <= ($nb_col-1))
				$sql .= ", ";
			$sql .= str_replace("_", ".", $titre_col[$i]);
		}
		elseif(ereg("^rubriques_", $titre_col[$i])) {
		# pour les rubriques
			$rub_fields[$y] = substr(strrchr($titre_col[$i], "_"), 1);
			$y++;
		}
	}
	$sql .= " FROM spip_articles AS `articles` 
	WHERE ";
	if(count($secteur) > 0) {
		$sql .= "(";
		for($i = 0; $i < count($secteur); $i++) {
			if($i > 0)
				$sql .= "OR ";
			$sql .= "articles.id_secteur = '".$secteur[$i]."' ";
		}
		$sql .= ") ";
	}
	
	if(count($rub) > 0) {
		if(count($secteur) > 0) 
			$sql .= "OR ";
		$sql .= "( ";
		for($i = 0; $i < count($rub); $i++) {
			if($i > 0)
				$sql .= "OR ";
			$sql .= "articles.id_rubrique = '".$rub[$i]."' ";
		}
		$sql .= ") ";
	}
	
	$sql .= "AND (articles.statut = 'publie') 
	ORDER BY articles.id_secteur, articles.id_rubrique";

	sdn_debug("<b>SQL :</b> ".$sql);
	$req = spip_query($sql);

	while($res = spip_fetch_array($req)) {
		
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
				$sql4 = "SELECT mots.titre 
				FROM spip_mots_articles AS `L1`, spip_mots AS `mots` 
				WHERE (L1.id_article = '".$id_art."') 
				AND (mots.id_groupe = '".$art_mc[$i]."') 
				AND mots.id_mot=L1.id_mot 
				GROUP BY mots.id_mot";
					
				$req4 = spip_query($sql4);
					
				while($res4 = spip_fetch_array($req4)) {

					$mot .= $res4['titre'].chr(10);
				}
				$data[$n] = substr($mot, 0, -1);
				$n++;
#			sdn_debug( "<hr><b>SQL 4 :</b> ".$sql4);
			}
		}

	# rubrique contenant l'article
		$sql2 = "SELECT rubriques.id_rubrique ";
		
		for($i = 0; $i < count($rub_fields); $i++) {				
			if($i <= (count($rub_fields)-1))
				$sql2 .= ", ";
				
			$sql2 .= "rubriques.".$rub_fields[$i];
		}
		
		$sql2 .= " FROM spip_rubriques AS `rubriques` 
		WHERE (rubriques.id_rubrique = '".$id_rub."') 
		AND (rubriques.statut = 'publie')";

#		sdn_debug("<hr><b>SQL 2 :</b> ".$sql2);

		$res2 = spip_fetch_array(spip_query($sql2));

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
				$sql3 = "SELECT mots.titre  
				FROM spip_mots_rubriques AS `L1`, spip_mots AS `mots` 
				WHERE (L1.id_rubrique = '".$id_rub."')
				AND (mots.id_groupe = '".$rub_mc[$i]."') 
				AND mots.id_mot=L1.id_mot 
				GROUP BY mots.id_mot";

				$req3 = spip_query($sql3);
				while($res3 = spip_fetch_array($req3)) {
					$mot .= $res3['titre'].chr(10);
				}
				$data[$n] = substr($mot, 0, -1);
				$n++;
			}			
		}
		
		sdn_debug( "<hr><b>SQL 3 :</b> ".$sql3);
		
	}
	ecco_pre($rub_mc, "rub MC");
	ecco_pre($titre_col, "colonnes");
	ecco_pre($data, "data");
	ecco_pre($config, "exportcsv");
	sdn_debug(htmlentities($outh));

# écriture du contenu($data) dans un fichier(true) ou tableau(false) selon $return
# initialisation écriture des lignes : 
	# defaut : extraction vers CSV
	if($return) {
		$tr = "";
		$l = chr(13).chr(10);
		$g = $gg = $gd = $th = $ht = '"';
		$d = ';';
		$s = $g.$d.$g;
	} # extraction pour affichage aperçu en table HTML
	else {
		$tr = '<tr>';
		$l = '</tr>';
		$th = '<th>';
		$ht = '</th>';
		$gg = '<td>';
		$gd = '</td>';
		$d = ' ';
		$s = $gd.$gg;
	}
	#	$outh =  entete  
	#	$outl = lignes de données
	$outh = $tr; $outl = "";

# écriture des titres des colonnes
	for($i = 0; $i < $nb_col; $i++) {
		$outh .= $th.str_replace("_", " ", $titre_col[$i]).$ht.$d;
	}
	$outh = substr($outh, 0, -1).$l;	

	for($i = 0; $i < count($data); $i += $nb_col) {
		$outl .= $tr;
		for($z = $i; $z < ($i+$nb_col); $z++) {
			
			$outl .= $gg.$data[$z].$gd.$d;
		}
		$outl = substr($outl, 0, -1).$l;	
	}
	$out = $outh.$outl;

	if($return) return $out;
	else echo '<table>'.$out.'</table>';

}

?>
