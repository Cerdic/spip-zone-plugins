<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/attributs_gestion');

function exec_attributs_dist(){
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $supp_attribut, $conf_attribut, $titre_conf, $na, $nr, $nb, $nau, $ns;

	$supp_attribut = intval($supp_attribut);
	$conf_attribut = intval($conf_attribut);

	if ($supp_attribut && autoriser('supprimer','attribut',$supp_attribut))
		attributs_supprimer_attribut($supp_attribut);

	debut_page(_T('attributs:attributs'));
	debut_gauche();

	if (autoriser('modifier','attribut')) {
		$res = icone_horizontale(_T('attributs:icone_creation_attribut'), generer_url_ecrire("attribut_edit","new=oui"),  "../"._DIR_PLUGIN_ATTRIBUTS. "img_pack/attribut-24.png", "creer.gif",false);
		echo bloc_des_raccourcis($res);
	}


	debut_droite();
	gros_titre(_T('attributs:les_attributs'));
	if ($connect_statut == '0minirezo' && $connect_toutes_rubriques) {
		echo typo(_T("attributs:infos_creation_attributs"));
	}

	echo '<br />';
	echo typo(_T("attributs:infos_attributs_redacteurs"));
	echo " &nbsp;<img src='"._DIR_PLUGIN_ATTRIBUTS."/img_pack/redac-12.gif' width='12' height='12' />";

	echo '<br /><br />';

	debut_cadre_enfonce("../"._DIR_PLUGIN_ATTRIBUTS. "img_pack/attribut-24.png", false, '', _T("attributs"));

	//Confirmation de suppression d'un attribut
	if ($conf_attribut && autoriser('supprimer','attribut',$conf_attribut)) {
		debut_boite_info();
		echo _T('attributs:conf_supp_1');
		echo " <b>$titre_conf</b> ($conf_attribut). ";
		echo _T('attributs:conf_supp_2');
		$s = "";
			if ($na>0){
				$s .= "$na&nbsp;"._T('attributs:articles');
				if ($nr>0|$nb>0|$nau>0|$ns>0) $s.=", ";
			}
			if ($nr>0){
				$s .= "$nr&nbsp;"._T('attributs:rubriques');
				if ($nb>0|$nau>0|$ns>0) $s.=", ";
			}
			if ($nb>0){
				$s .= "$nb&nbsp;"._T('attributs:breves');
				if ($nau>0|$ns>0) $s.=", ";
			}
			if ($nau>0){
				$s .= "$nau&nbsp;"._T('attributs:auteurs');
				if ($ns>0) $s.=", ";
			}
			if ($ns>0)
				$s .= "$ns&nbsp;"._T('attributs:sites');
		echo " <b>$s</b>, ";
		echo _T('attributs:conf_supp_3');
		echo "<div style='text-align:right;'>";
		echo "<b><a href='".generer_url_ecrire("attributs","supp_attribut=$conf_attribut")."'>"._T('attributs:oui')."</a></b><br />";
		echo _T('attributs:je_veux_supp');
		echo "</div>";
		fin_boite_info();
	}

	//Afficher les différents attributs
	$requete = array("SELECT"=>"attributs.*","FROM"=>"spip_attributs AS attributs","ORDER BY"=>"attributs.titre");
	$select = $requete['SELECT'] ? $requete['SELECT'] : '*';
	$from = $requete['FROM'] ? $requete['FROM'] : 'spip_attributs AS attributs';
	$join = $requete['JOIN'] ? (' LEFT JOIN ' . $requete['JOIN']) : '';
	$where = $requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '';
	$order = $requete['ORDER BY'] ? (' ORDER BY ' . $requete['ORDER BY']) : 'attributs.titre';
	$group = $requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '';
	$limit = $requete['LIMIT'] ? (' LIMIT ' . $requete['LIMIT']) : '';

	$cpt = "$from$join$where$group$order";
	$tmp_var = "debut";

	if (!$group){
		$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM $cpt"));
		$cpt = $cpt['n'];
	}
	else
		$cpt = spip_num_rows(spip_query("SELECT $select FROM $cpt"));

	if ($requete['LIMIT']) $cpt = min($requete['LIMIT'], $cpt);

	$nb_aff = 1.5 * _TRANCHES;

	if ($cpt > $nb_aff) {
		$nb_aff = (_TRANCHES); 
		$tranches = afficher_tranches_requete($cpt, 3, $tmp_var, '', $nb_aff);
	}

	$deb_aff = _request($tmp_var);
	$deb_aff = ($deb_aff !== NULL ? intval($deb_aff) : 0);

  if ($cpt) {
	 	$result = spip_query("SELECT $select FROM $from$join$where$group$order". (($deb_aff < 0) ? '' : " LIMIT $deb_aff, $nb_aff"));

		$vals = '';
		$vals[] = _T('attributs:colonne_id');
		$vals[] = _T('attributs:titre');
		$vals[] = _T('attributs:descriptif');
		$vals[] = _T('attributs:art');
		$vals[] = _T('attributs:rub');
		$vals[] = _T('attributs:brv');
		$vals[] = _T('attributs:aut');
		$vals[] = _T('attributs:sit');
		$vals[] = '';
		$vals[] = '';
		$table[] = $vals;
		
		while ($row = spip_fetch_array($result)){
			$vals = array();
			$id_attribut = $row['id_attribut'];
			//Créer les fonctions de comptage.
			$nb_art = attributs_nb_articles($id_attribut);
			$nb_rub = attributs_nb_rubriques($id_attribut);
			$nb_brv = attributs_nb_breves($id_attribut);
			$nb_aut = attributs_nb_auteurs($id_attribut);
			$nb_sit = attributs_nb_syndic($id_attribut);
			
			$s = $row['id_attribut'];
			$vals[] = $s;

			$s = "";
			$s .= "<a href='".generer_url_ecrire("attribut_edit","id_attribut=$id_attribut")."'>";
			$titre = $row['titre'];
			$s .= $titre;
			$s .= "</a>";
			if($row['redacteurs']=='oui') $s .= " &nbsp;<img src='"._DIR_PLUGIN_ATTRIBUTS."/img_pack/redac-12.gif' width='12' height='12' />";
			$vals[] = $s;

			$s = propre($row['descriptif']);
			$vals[] = $s;
			
			$s = ($row['articles']=='oui')?"<img src='"._DIR_PLUGIN_ATTRIBUTS."/img_pack/attribut-16.png' width='16' height='16' />":'';
			$vals[] = $s;
			
			$s = ($row['rubriques']=='oui')?"<img src='"._DIR_PLUGIN_ATTRIBUTS."/img_pack/attribut-16.png' width='16' height='16' />":'';
			$vals[] = $s;
			
			$s = ($row['breves']=='oui')?"<img src='"._DIR_PLUGIN_ATTRIBUTS."/img_pack/attribut-16.png' width='16' height='16' />":'';
			$vals[] = $s;
			
			$s = ($row['auteurs']=='oui')?"<img src='"._DIR_PLUGIN_ATTRIBUTS."/img_pack/attribut-16.png' width='16' height='16' />":'';
			$vals[] = $s;
			
			$s = ($row['syndic']=='oui')?"<img src='"._DIR_PLUGIN_ATTRIBUTS."/img_pack/attribut-16.png' width='16' height='16' />":'';
			$vals[] = $s;
			
			$s = "";
			if ($nb_art>0){
				$s .= "$nb_art&nbsp;"._T('attributs:articles');
				if ($nb_rub>0|$nb_brv>0|$nb_sit>0|$nb_aut>0) $s.=",<br />";
			}
			if ($nb_rub>0){
				$s .= "$nb_rub&nbsp;"._T('attributs:rubriques');
				if ($nb_brv>0|$nb_sit>0|$nb_aut>0) $s.=",<br />";
			}
			if ($nb_brv>0){
				$s .= "$nb_brv&nbsp;"._T('attributs:breves');
				if ($nb_sit>0|$nb_aut>0) $s.=",<br />";
			}
			if ($nb_aut>0){
				$s .= "$nb_aut&nbsp;"._T('attributs:auteurs');
				if ($nb_sit>0) $s.=",<br />";
			}
			if ($nb_sit>0)
				$s .= "$nb_sit&nbsp;"._T('attributs:sites');
			$vals[] = $s;
			
			$s = "";

			if (autoriser('supprimer','attribut',$id_attribut)) {
				if ($nb_art OR  $nb_rub OR $nb_brv OR $nb_aut OR $nb_sit) 
					$href = generer_url_ecrire("attributs","conf_attribut=$id_attribut&titre_conf=$titre&na=$nb_art&nr=$nb_rub&nb=$nb_brv&nau=$nb_aut&ns=$nb_sit");
				else 
					$href = generer_url_ecrire("attributs","supp_attribut=$id_attribut");

				$s .= "<a href='$href' title='"._T('attributs:supprimer_attribut')."' >";
				$s .= "<img src='"._DIR_PLUGIN_ATTRIBUTS."/img_pack/croix-rouge.gif' width='7' height='7' alt='"._T('attributs:supprimer_attribut')."' />";
				$s .= "</a>";
			}

			$vals[] = $s;

			$table[] = $vals;
		}
	}

	// on affiche la table
	echo "<div class='liste'>";
	echo "<table width='100%' cellpadding='5' cellspacing='0' border='0'>";
	echo $tranches;
	$largeurs = array('','','','','','','','','','');
	$styles = array('arial1','arial1','arial1','arial1','arial1','arial1','arial1','arial1','arial1','arial1');
	echo afficher_liste($largeurs, $table, $styles);
	echo "</table>";
	echo "</div>";



	fin_cadre_enfonce();




	echo fin_gauche(), fin_page();
}




?>