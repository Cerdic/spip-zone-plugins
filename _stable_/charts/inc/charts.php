<?php
/*
 * charts
 *
 * Auteur :
 * Cedric MORIN
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */


	function charts_install(){
		chart_verifier_base();
	}
	
	function charts_uninstall(){
		include_spip('base/charts');
		include_spip('base/abstract_sql');
	}
	
	function charts_verifier_base(){
		$version_base = 0.10;
		$current_version = 0.0;
		if (   (isset($GLOBALS['meta']['charts_base_version']) )
				&& (($current_version = $GLOBALS['meta']['charts_base_version'])==$version_base))
			return;

		include_spip('base/charts');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('charts_base_version',$current_version=$version_base);
		}
		ecrire_metas();
	}

	// Fonction utilitaires
	function charts_chart_editable($id_chart = 0) {
		global $connect_statut;
		return $connect_statut == '0minirezo';
	}
	
	function charts_chart_administrable($id_chart = 0) {
		global $connect_statut;
		return $connect_statut == '0minirezo';
	}

	//
	// Afficher un pave charts dans la colonne de gauche
	// (edition des articles)
	
	function charts_afficher_insertion_chart($id_article) {
		global $connect_id_auteur, $connect_statut;
		global $couleur_foncee, $couleur_claire, $options;
		global $spip_lang_left, $spip_lang_right;
	
		$s = "";
		// Ajouter un chart
		$s .= "\n<p>";
		$s .= debut_cadre_relief("../"._DIR_PLUGIN_CHARTS."/img_pack/chart-24.gif", true);
	
		$s .= "<div style='padding: 2px; background-color: $couleur_claire; text-align: center; color: black;'>";
		$s .= bouton_block_invisible("ajouter_chart");
		$s .= "<strong class='verdana3' style='text-transform: uppercase;'>"
			._T("charts:article_inserer_un_chart")."</strong>";
		$s .= "</div>\n";
	
		$s .= debut_block_invisible("ajouter_chart");
		$s .= "<div class='verdana2'>";
		$s .= _T("charts:article_inserer_un_chart_detail");
		$s .= "</div>";
	
		$query = "SELECT id_chart, titre FROM spip_charts ORDER BY titre";
		$result = spip_query($query);
		if (spip_num_rows($result)) {
			$s .= "<br />\n";
			$s .= "<div class='bandeau_rubriques' style='z-index: 1;'>";
			$s .= "<div class='plan-articles'>";
			while ($row = spip_fetch_array($result)) {
				$id_chart = $row['id_chart'];
				$titre = typo($row['titre']);
				
				$link = generer_url_ecrire('charts_edit',"id_chart=$id_chart&retour=".urlencode(self()));
				$s .= "<a href='".$link."'>";
				$s .= $titre."</a>\n";
				$s .= "<div class='arial1' style='text-align:$spip_lang_right;color: black; padding-$spip_lang_left: 4px;' "."title=\""._T("charts:article_recopier_raccourci")."\">";
				$s .= "<strong>&lt;chart".$id_chart."&gt;</strong>";
				$s .= "</div>";
			}
			$s .= "</div>";
			$s .= "</div>";
		}
	
		// Creer un chart
		if (charts_chart_editable()) {
			$s .= "\n<br />";
			$link = generer_url_ecrire('charts_edit',"new=oui&retour=".urlencode(self()));
			$s .= icone_horizontale(_T("charts:icone_creer_chart"),
				$link, "../"._DIR_PLUGIN_CHARTS."/img_pack/chart-24.gif", "creer.gif", false);
		}
	
		$s .= fin_block();
	
		$s .= fin_cadre_relief(true);
		return $s;
	}

	//
	// Afficher une liste de charts
	//
	
	function charts_afficher_charts($titre_table, $requete, $icone = '') {
		global $couleur_claire, $couleur_foncee;
		global $connect_id_auteur;

		$tous_id = array();
		
		$select = $requete['SELECT'] ? $requete['SELECT'] : '*';
		$from = $requete['FROM'] ? $requete['FROM'] : 'spip_articles AS articles';
		$join = $requete['JOIN'] ? (' LEFT JOIN ' . $requete['JOIN']) : '';
		$where = $requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '';
		$order = $requete['ORDER BY'] ? (' ORDER BY ' . $requete['ORDER BY']) : '';
		$group = $requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '';
		$limit = $requete['LIMIT'] ? (' LIMIT ' . $requete['LIMIT']) : '';
	
		$cpt = "$from$join$where$group";
		$tmp_var = substr(md5($cpt), 0, 4);

		if (!$group){
			$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM $cpt"));
			if (! ($cpt = $cpt['n'])) return $tous_id ;
		}
		else
			$cpt = spip_num_rows(spip_query("SELECT $select FROM $cpt"));
		if ($requete['LIMIT']) $cpt = min($requete['LIMIT'], $cpt);
	
		$nb_aff = 1.5 * _TRANCHES;
		$deb_aff = intval(_request('t_' .$tmp_var));
	
		if ($cpt > $nb_aff) {
			$nb_aff = (_TRANCHES); 
			$tranches = afficher_tranches_requete($cpt, 3, $tmp_var, '', $nb_aff);
		}
		
		if (!$icone) $icone = "../"._DIR_PLUGIN_CHARTS."/img_pack/chart-24.png";
		
		if ($cpt) {
			if ($titre_table) echo "<div style='height: 12px;'></div>";
			echo "<div class='liste'>";
			bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
			echo "<table width='100%' cellpadding='4' cellspacing='0' border='0'>";
	
			echo $tranches;
	
			$result = spip_query("SELECT $select FROM $from$join$where$group$order LIMIT $deb_aff, $nb_aff");
			$num_rows = spip_num_rows($result);
	
			$ifond = 0;
			$premier = true;
			
			$compteur_liste = 0;
			while ($row = spip_fetch_array($result)) {
				$vals = '';
				$id_chart = $row['id_chart'];
				$reponses = $row['reponses'];
				$titre = $row['titre'];

				$tous_id[] = $id_chart;

				$retour = parametre_url(self(),'duplique_chart','');
				$link = generer_url_ecrire('charts_edit',"id_chart=$id_chart&retour=".urlencode($retour));
				if ($reponses) {
					$puce = 'puce-verte-breve.gif';
				}
				else {
					$puce = 'puce-orange-breve.gif';
				}
	
				$s = "<img src='"._DIR_IMG_PACK."$puce' width='7' height='7' border='0'>&nbsp;&nbsp;";
				$vals[] = $s;
				
				//$s .= typo($titre);
				$s = icone_horizontale(typo($titre), $link,"../"._DIR_PLUGIN_CHARTS."/img_pack/chart-24.png", "",false);
				$vals[] = $s;
				
				$s = "";
				$vals[] = $s;
	
				$s = "";
				
				$s = "";
				if(charts_chart_administrable($id_chart)){
					$link = parametre_url(self(),'duplique_chart',$id_chart);
					$vals[] = "<a href='$link'>"._T("charts:dupliquer")."</a>";
				}
				$vals[] = $s;

				$table[] = $vals;
			}
			spip_free_result($result);
			
			$largeurs = array('','','','','');
			$styles = array('arial11', 'arial11', 'arial1', 'arial1','arial1');
			echo afficher_liste($largeurs, $table, $styles);
			echo "</table>";
			echo "</div>\n";
		}
		return $tous_id;
	}


?>
