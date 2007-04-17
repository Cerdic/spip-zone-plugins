<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acces_restreint_base');
include_spip('inc/acces_restreint');
include_spip('inc/acces_restreint_gestion');
include_spip('inc/presentation');

function exec_acces_restreint(){
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $couleur_claire;
	global $spip_lang_right;
	if ($GLOBALS['spip_version_code']<1.9204){
		include_spip('base/create');
		creer_base(); // au cas ou
	}
	  
	debut_page(_T('accesrestreint:page_zones_acces'));
	
	echo "<br /><br /><br />";
	gros_titre(_T('accesrestreint:titre_zones_acces'));
	debut_gauche();
	
	debut_boite_info();
	echo propre(_T('accesrestreint:info_page'));	
	fin_boite_info();
	
	debut_droite();
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	if (_request('creer')!=NULL)
		AccesRestreint_cree_zone();
	if (_request('supp_zone')!=NULL)
		AccesRestreint_supprimer_zone();

	$requete = array("SELECT"=>"zones.*","FROM"=>"spip_zones AS zones");
	$select = $requete['SELECT'] ? $requete['SELECT'] : '*';
	$from = $requete['FROM'] ? $requete['FROM'] : 'spip_articles AS articles';
	$join = $requete['JOIN'] ? (' LEFT JOIN ' . $requete['JOIN']) : '';
	$where = $requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '';
	$order = $requete['ORDER BY'] ? (' ORDER BY ' . $requete['ORDER BY']) : '';
	$group = $requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '';
	$limit = $requete['LIMIT'] ? (' LIMIT ' . $requete['LIMIT']) : '';

	$cpt = "$from$join$where$group";
	$tmp_var = "debut";

	if (!$group){
		$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM $cpt"));
		$cpt = $cpt['n'];
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

  if ($cpt) {
	 	$result = spip_query("SELECT $select FROM $from$join$where$group$order LIMIT $deb_aff, $nb_aff");

		$vals = '';
		$vals[] = _T('accesrestreint:colonne_id');
		$vals[] = _T('accesrestreint:titre');
		$vals[] = _T('accesrestreint:descriptif');
		$vals[] = _T('accesrestreint:publique');
		$vals[] = _T('accesrestreint:privee');
		$vals[] = '';
		$vals[] = '';
		$table[] = $vals;
		
		while ($row = spip_fetch_array($result)){
			$vals = array();
			$id_zone = $row['id_zone'];
			$nb_rub_pub = count(AccesRestreint_liste_contenu_zone_rub($id_zone, TRUE));
			$nb_rub_priv = count(AccesRestreint_liste_contenu_zone_rub($id_zone, FALSE));
			$nb_aut = count(AccesRestreint_liste_contenu_zone_auteur($id_zone));
			
			$s = $row['id_zone'];
			$vals[] = $s;

			$s = "";
			$s .= "<a href='".generer_url_ecrire("acces_restreint_edit","id_zone=$id_zone")."'>";
			$s .= $row['titre'];
			$s .= "</a>";
			$vals[] = $s;

			$s = propre($row['descriptif']);
			$vals[] = $s;
			
			$s = "";
			if ($nb_rub_pub>0) $s .= "$nb_rub_pub "._T('accesrestreint:rubriques');
			$vals[] = $s;
			
			$s = "";
			if ($nb_rub_priv>0) $s .= "$nb_rub_priv "._T('accesrestreint:rubriques');
			$vals[] = $s;
			
			$s = "";
			if ($nb_aut>0) $s .= "$nb_aut "._T('accesrestreint:auteurs');

			$vals[] = $s;
			
			$s="";
			$s = icone_horizontale (_T('accesrestreint:icone_supprimer_zone'), generer_url_ecrire('acces_restreint', "supp_zone=$id_zone"), "../"._DIR_PLUGIN_ACCESRESTREINT."/img_pack/zones-acces-24.gif", "supprimer.gif", false);
			$vals[] = $s;

			$table[] = $vals;
		}
	}

	// on affiche la table
	$titre_table = _T('accesrestreint:titre_table');
	$icone = "../"._DIR_PLUGIN_ACCESRESTREINT."/img_pack/zones-acces-24.gif";
	//if ($titre_table) echo "<div style='height: 12px;'></div>";
	echo "<div class='liste'>";
	bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
	echo "<table width='100%' cellpadding='5' cellspacing='0' border='0'>";
	echo $tranches;
	$largeurs = array('','','','','','','');
	$styles = array('arial11', 'arial1', 'arial1','arial1','arial1','arial1','arial1');
	echo afficher_liste($largeurs, $table, $styles);
	echo "</table>";
	echo "</div>";

	echo "<br/>";

	debut_cadre_relief();
	echo generer_url_post_ecrire("acces_restreint");
	AccesRestreint_formulaire_zone($id_zone , _T('accesrestreint:titre'), _T('accesrestreint:descriptif'), 'oui', 'non');

	if ($connect_statut == "0minirezo" && $connect_toutes_rubriques){
		echo "<div class='verdana2'>";
		echo "<input type='checkbox' name='auto_attribue_droits' value='oui' checked='checked' id='droits_admin'> <label for='droits_admin'>"._T("accesrestreint:ajouter_droits_auteur")."</label><br>";
		echo "</div>";
	}

	echo "<div style='text-align:$spip_lang_right'><input type='submit' name='creer' value='"._T('accesrestreint:bouton_creer_la_zone')."' class='fondo'></div>";
	echo "</div>";
	echo "</form>";
	fin_cadre_relief();

	fin_page();
}

?>